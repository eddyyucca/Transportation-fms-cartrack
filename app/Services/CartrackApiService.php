<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CartrackApiService
{
    public function fetchTripsByDate(string $date): array
    {
        $start = Carbon::parse($date)->format('Y-m-d') . ' 00:00:00';
        $end   = Carbon::parse($date)->format('Y-m-d') . ' 23:59:59';

        $baseUrl = rtrim(config('cartrack.base_url'), '/');
        $username = config('cartrack.username');
        $password = config('cartrack.password');
        $timeout = (int) config('cartrack.timeout', 90);

        if (!$baseUrl || !$username || !$password) {
            throw new RuntimeException('Konfigurasi Cartrack belum lengkap. Cek file .env dan config/cartrack.php');
        }

        $page = 1;
        $limit = 1000;
        $allTrips = [];
        $lastPage = 1;

        do {
            $response = Http::timeout($timeout)
                ->acceptJson()
                ->withBasicAuth($username, $password)
                ->get($baseUrl . '/rest/trips', [
                    'start_timestamp' => $start,
                    'end_timestamp' => $end,
                    'page' => $page,
                    'limit' => $limit,
                ]);

            if (!$response->successful()) {
                throw new RuntimeException('API Cartrack gagal. HTTP ' . $response->status() . ' - ' . $response->body());
            }

            $payload = $response->json();
            $data = $payload['data'] ?? [];
            $meta = $payload['meta'] ?? [];

            $allTrips = array_merge($allTrips, $data);
            $lastPage = (int) ($meta['last_page'] ?? 1);
            $page++;
        } while ($page <= $lastPage);

        return [
            'start' => $start,
            'end' => $end,
            'trips' => $allTrips,
        ];
    }

    public function aggregateTripReport(array $trips): array
    {
        $report = [];

        foreach ($trips as $trip) {
            $registration = $trip['registration'] ?? 'UNKNOWN';

            if (!isset($report[$registration])) {
                $report[$registration] = [
                    'registration' => $registration,
                    'total_trips' => 0,
                    'distance_km' => 0,
                    'total_engine_on_min' => 0,
                    'total_driving_min' => 0,
                    'total_idle_min' => 0,
                    'harsh_acceleration' => 0,
                    'harsh_braking' => 0,
                    'harsh_cornering' => 0,
                    'threshold_speeding' => 0,
                    'road_speeding' => 0,
                    'total_harsh_events' => 0,
                    'total_speeding_events' => 0,
                    'idle_ratio' => 0,
                    'utilization_ratio' => 0,
                    'pa_score' => 0,
                    'safety_score' => 100,
                    'performance_score' => 0,
                    'status' => 'Good',
                ];
            }

            $tripDuration = (int) ($trip['trip_duration_seconds'] ?? 0);
            $idleTime = (int) ($trip['idle_time_seconds'] ?? 0);
            $drivingMin = max(0, ($tripDuration - $idleTime) / 60);
            $engineOnMin = $tripDuration / 60;
            $idleMin = $idleTime / 60;

            $report[$registration]['total_trips']++;
            $report[$registration]['distance_km'] += (float) ($trip['trip_distance'] ?? 0) / 1000;
            $report[$registration]['total_engine_on_min'] += $engineOnMin;
            $report[$registration]['total_driving_min'] += $drivingMin;
            $report[$registration]['total_idle_min'] += $idleMin;
            $report[$registration]['harsh_acceleration'] += (int) ($trip['harsh_acceleration_events'] ?? 0);
            $report[$registration]['harsh_braking'] += (int) ($trip['harsh_braking_events'] ?? 0);
            $report[$registration]['harsh_cornering'] += (int) ($trip['harsh_cornering_events'] ?? 0);
            $report[$registration]['threshold_speeding'] += (int) ($trip['thresholds_speeding_events'] ?? 0);
            $report[$registration]['road_speeding'] += (int) ($trip['road_speeding_events'] ?? 0);
        }

        foreach ($report as &$row) {
            $row['distance_km'] = round($row['distance_km'], 2);
            $row['total_engine_on_min'] = round($row['total_engine_on_min'], 2);
            $row['total_driving_min'] = round($row['total_driving_min'], 2);
            $row['total_idle_min'] = round($row['total_idle_min'], 2);
            $row['total_harsh_events'] = $row['harsh_acceleration'] + $row['harsh_braking'] + $row['harsh_cornering'];
            $row['total_speeding_events'] = $row['threshold_speeding'] + $row['road_speeding'];

            $row['idle_ratio'] = $row['total_engine_on_min'] > 0
                ? round(($row['total_idle_min'] / $row['total_engine_on_min']) * 100, 2)
                : 0;

            $row['utilization_ratio'] = $row['total_engine_on_min'] > 0
                ? round(($row['total_driving_min'] / $row['total_engine_on_min']) * 100, 2)
                : 0;

            // PA operasional: semakin kecil idle saat engine on, semakin baik
            $row['pa_score'] = max(0, round(100 - $row['idle_ratio'], 2));

            $tripRiskRate = $row['total_trips'] > 0
                ? (($row['total_harsh_events'] + $row['total_speeding_events']) / $row['total_trips']) * 10
                : 0;

            $row['safety_score'] = max(0, round(100 - $tripRiskRate, 2));
            $row['performance_score'] = round(
                ($row['utilization_ratio'] * 0.50) +
                ($row['pa_score'] * 0.30) +
                ($row['safety_score'] * 0.20),
                2
            );

            if ($row['performance_score'] >= 85) {
                $row['status'] = 'Excellent';
            } elseif ($row['performance_score'] >= 70) {
                $row['status'] = 'Good';
            } elseif ($row['performance_score'] >= 55) {
                $row['status'] = 'Monitor';
            } else {
                $row['status'] = 'Critical';
            }
        }
        unset($row);

        return array_values($report);
    }

    public function buildSummary(array $rows, int $totalTrips): array
    {
        $summary = [
            'total_vehicles' => count($rows),
            'total_trips' => $totalTrips,
            'total_distance_km' => round(array_sum(array_column($rows, 'distance_km')), 2),
            'total_engine_on_min' => round(array_sum(array_column($rows, 'total_engine_on_min')), 2),
            'total_driving_min' => round(array_sum(array_column($rows, 'total_driving_min')), 2),
            'total_idle_min' => round(array_sum(array_column($rows, 'total_idle_min')), 2),
            'total_harsh_acceleration' => array_sum(array_column($rows, 'harsh_acceleration')),
            'total_harsh_braking' => array_sum(array_column($rows, 'harsh_braking')),
            'total_harsh_cornering' => array_sum(array_column($rows, 'harsh_cornering')),
            'total_threshold_speeding' => array_sum(array_column($rows, 'threshold_speeding')),
            'total_road_speeding' => array_sum(array_column($rows, 'road_speeding')),
            'avg_idle_ratio' => round($this->avg($rows, 'idle_ratio'), 2),
            'avg_utilization_ratio' => round($this->avg($rows, 'utilization_ratio'), 2),
            'avg_pa_score' => round($this->avg($rows, 'pa_score'), 2),
            'avg_safety_score' => round($this->avg($rows, 'safety_score'), 2),
            'avg_performance_score' => round($this->avg($rows, 'performance_score'), 2),
        ];

        $summary['total_harsh_events'] = $summary['total_harsh_acceleration'] + $summary['total_harsh_braking'] + $summary['total_harsh_cornering'];
        $summary['total_speeding_events'] = $summary['total_threshold_speeding'] + $summary['total_road_speeding'];

        return $summary;
    }

    public function syncDailyMetrics(string $date): array
{
    // Ambil data dari API
    $payload = $this->fetchTripsByDate($date);
    $trips   = $payload['trips'];

    if (empty($trips)) {
        return ['synced' => 0, 'skipped' => 0, 'message' => 'Tidak ada trip data.'];
    }

    // Aggregate per registration (sudah ada method ini)
    $report = $this->aggregateTripReport($trips);

    // Ambil semua unit yang dimonitor, index by registration
    $unitMap = \App\Models\FleetUnit::where('is_monitored', true)
        ->whereNotNull('registration')
        ->get()
        ->keyBy('registration');

    $synced  = 0;
    $skipped = 0;

    foreach ($report as $row) {
        $registration = $row['registration'];

        // Skip jika registration tidak ada di master unit
        if (!isset($unitMap[$registration])) {
            $skipped++;
            continue;
        }

        $unit = $unitMap[$registration];

   \App\Models\FleetUnitDailyMetric::updateOrCreate(
    [
        'unit_code'   => $unit->unit_code,
        'report_date' => $date,
    ],
    [
        'idle_hours'       => round($row['total_idle_min'] / 60, 2),
        'distance_km'      => $row['distance_km'],
        'ua_percent'       => $row['utilization_ratio'],
        'standby_hours'    => round(($row['total_engine_on_min'] - $row['total_driving_min']) / 60, 2),
        'fuel_consumption' => 0,
        'hm_start'         => null,
        'hm_end'           => null,
        'hm_usage'         => null,
        'notes'            => 'Auto sync dari Cartrack',
    ]
);

        $synced++;
    }

    return [
        'synced'  => $synced,
        'skipped' => $skipped,
        'message' => "Sync selesai: $synced unit tersync, $skipped tidak ditemukan di master unit.",
    ];
}

    protected function avg(array $rows, string $key): float
    {
        if (count($rows) === 0) {
            return 0;
        }

        return array_sum(array_column($rows, $key)) / count($rows);
    }
}
