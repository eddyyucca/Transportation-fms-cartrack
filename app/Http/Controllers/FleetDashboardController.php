<?php

namespace App\Http\Controllers;

use App\Models\FleetDailySummary;
use App\Models\FleetVehicleDailyStat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FleetDashboardController extends Controller
{
    public function index(Request $request)
    {
        $endDate = $this->normalizeDate($request->get('date_to', $request->get('date', now()->subDay()->format('Y-m-d'))));
        $startDate = $this->normalizeDate($request->get('date_from', $endDate));

        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $query = FleetVehicleDailyStat::query()
            ->whereBetween('report_date', [$startDate, $endDate]);

        if ($request->filled('registration')) {
            $query->where('registration', 'like', '%' . trim((string) $request->registration) . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'performance_score');
        $allowedSorts = ['performance_score', 'pa_score', 'distance_km', 'total_trips', 'idle_ratio', 'safety_score'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'performance_score';
        }

        $rows = $query->orderByDesc($sort)->get();

        $summaryRows = FleetDailySummary::query()
    ->whereBetween('report_date', [$startDate, $endDate])
    ->orderBy('report_date')
    ->get()
    ->groupBy(fn($r) => Carbon::parse($r->report_date)->format('Y-m-d'))
    ->map(fn($group) => (object)[
        'report_date' => $group->first()->report_date,
        'avg_pa_score' => $group->avg('avg_pa_score'),
        'avg_utilization_ratio' => $group->avg('avg_utilization_ratio'),
        'avg_safety_score' => $group->avg('avg_safety_score'),
    ])
    ->values();

        $summary = $this->buildRangeSummary($summaryRows, $rows);
        $recommendations = $this->buildRecommendations($summary, $rows);

        $trendChart = [
            'labels' => $summaryRows->pluck('report_date')->map(fn ($d) => Carbon::parse($d)->format('d M'))->values(),
            'pa' => $summaryRows->pluck('avg_pa_score')->map(fn ($v) => round((float) $v, 2))->values(),
            'utilization' => $summaryRows->pluck('avg_utilization_ratio')->map(fn ($v) => round((float) $v, 2))->values(),
            'safety' => $summaryRows->pluck('avg_safety_score')->map(fn ($v) => round((float) $v, 2))->values(),
        ];

        $topDistance = $rows->sortByDesc('distance_km')->take(10)->values();
        $topIdle = $rows->sortByDesc('total_idle_min')->take(10)->values();
        $topTrips = $rows->sortByDesc('total_trips')->take(10)->values();
        $bestPa = $rows->sortByDesc('pa_score')->take(10)->values();
        $worstIdle = $rows->sortByDesc('idle_ratio')->take(10)->values();

        return view('fleet.dashboard', [
            'selectedDate' => $endDate,
            'dateFrom' => $startDate,
            'dateTo' => $endDate,
            'summary' => $summary,
            'rows' => $rows,
            'filters' => [
                'registration' => (string) $request->get('registration', ''),
                'status' => (string) $request->get('status', ''),
                'sort' => $sort,
            ],
            'trendChart' => $trendChart,
            'recommendations' => $recommendations,
            'chartTopDistance' => [
                'labels' => $topDistance->pluck('registration'),
                'values' => $topDistance->pluck('distance_km'),
            ],
            'chartTopIdle' => [
                'labels' => $topIdle->pluck('registration'),
                'values' => $topIdle->map(fn ($item) => round($item->total_idle_min / 60, 2))->values(),
            ],
            'chartTopTrips' => [
                'labels' => $topTrips->pluck('registration'),
                'values' => $topTrips->pluck('total_trips'),
            ],
            'chartBestPa' => [
                'labels' => $bestPa->pluck('registration'),
                'values' => $bestPa->pluck('pa_score'),
            ],
            'chartWorstIdleRatio' => [
                'labels' => $worstIdle->pluck('registration'),
                'values' => $worstIdle->pluck('idle_ratio'),
            ],
        ]);
    }

    public function vehicleDetail(Request $request, string $registration)
    {
        $registration = trim($registration);
        $dateTo = $this->normalizeDate($request->get('date_to', now()->subDay()->format('Y-m-d')));
        $dateFrom = $this->normalizeDate($request->get('date_from', Carbon::parse($dateTo)->subDays(29)->format('Y-m-d')));

        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $rows = FleetVehicleDailyStat::query()
            ->where('registration', $registration)
            ->whereBetween('report_date', [$dateFrom, $dateTo])
            ->orderBy('report_date')
            ->get();

        abort_if($rows->isEmpty(), 404, 'Data unit tidak ditemukan.');

        $latest = $rows->last();
        $summary = [
            'days' => $rows->count(),
            'distance_km' => round((float) $rows->sum('distance_km'), 2),
            'total_trips' => (int) $rows->sum('total_trips'),
            'avg_pa_score' => round((float) $rows->avg('pa_score'), 2),
            'avg_idle_ratio' => round((float) $rows->avg('idle_ratio'), 2),
            'avg_utilization_ratio' => round((float) $rows->avg('utilization_ratio'), 2),
            'avg_safety_score' => round((float) $rows->avg('safety_score'), 2),
            'avg_performance_score' => round((float) $rows->avg('performance_score'), 2),
            'total_harsh_events' => (int) $rows->sum('total_harsh_events'),
            'total_speeding_events' => (int) $rows->sum('total_speeding_events'),
        ];

        $trend = [
            'labels' => $rows->pluck('report_date')->map(fn ($d) => Carbon::parse($d)->format('d M'))->values(),
            'pa' => $rows->pluck('pa_score')->map(fn ($v) => round((float) $v, 2))->values(),
            'idle_ratio' => $rows->pluck('idle_ratio')->map(fn ($v) => round((float) $v, 2))->values(),
            'utilization' => $rows->pluck('utilization_ratio')->map(fn ($v) => round((float) $v, 2))->values(),
            'distance' => $rows->pluck('distance_km')->map(fn ($v) => round((float) $v, 2))->values(),
            'trips' => $rows->pluck('total_trips')->values(),
        ];

        return view('fleet.vehicle-detail', [
            'registration' => $registration,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'rows' => $rows,
            'latest' => $latest,
            'summary' => $summary,
            'trend' => $trend,
        ]);
    }

    public function exportDailyCsv(Request $request): StreamedResponse
    {
        $endDate = $this->normalizeDate($request->get('date_to', $request->get('date', now()->subDay()->format('Y-m-d'))));
        $startDate = $this->normalizeDate($request->get('date_from', $endDate));
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $rows = FleetVehicleDailyStat::query()
            ->whereBetween('report_date', [$startDate, $endDate])
            ->when($request->filled('registration'), fn ($q) => $q->where('registration', 'like', '%' . trim((string) $request->registration) . '%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderBy('report_date')
            ->orderBy('registration')
            ->get();

        $filename = 'fleet-report-' . $startDate . '-to-' . $endDate . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'report_date', 'registration', 'total_trips', 'distance_km', 'engine_on_min', 'driving_min', 'idle_min',
                'idle_ratio', 'utilization_ratio', 'pa_score', 'safety_score', 'performance_score',
                'harsh_events', 'speeding_events', 'status'
            ]);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->report_date?->format('Y-m-d'),
                    $row->registration,
                    $row->total_trips,
                    $row->distance_km,
                    $row->total_engine_on_min,
                    $row->total_driving_min,
                    $row->total_idle_min,
                    $row->idle_ratio,
                    $row->utilization_ratio,
                    $row->pa_score,
                    $row->safety_score,
                    $row->performance_score,
                    $row->total_harsh_events,
                    $row->total_speeding_events,
                    $row->status,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function buildRangeSummary($summaryRows, $rows): ?object
    {
        if ($summaryRows->isEmpty() && $rows->isEmpty()) {
            return null;
        }

        return (object) [
            'days_count' => $summaryRows->count(),
            'total_vehicles' => $rows->unique('registration')->count(),
            'total_trips' => (int) $rows->sum('total_trips'),
            'total_distance_km' => round((float) $rows->sum('distance_km'), 2),
            'total_engine_on_min' => round((float) $rows->sum('total_engine_on_min'), 2),
            'total_driving_min' => round((float) $rows->sum('total_driving_min'), 2),
            'total_idle_min' => round((float) $rows->sum('total_idle_min'), 2),
            'total_harsh_events' => (int) $rows->sum('total_harsh_events'),
            'total_speeding_events' => (int) $rows->sum('total_speeding_events'),
            'avg_idle_ratio' => round((float) $rows->avg('idle_ratio'), 2),
            'avg_utilization_ratio' => round((float) $rows->avg('utilization_ratio'), 2),
            'avg_pa_score' => round((float) $rows->avg('pa_score'), 2),
            'avg_safety_score' => round((float) $rows->avg('safety_score'), 2),
            'avg_performance_score' => round((float) $rows->avg('performance_score'), 2),
        ];
    }

    protected function buildRecommendations(?object $summary, $rows): array
    {
        if (!$summary || $rows->isEmpty()) {
            return [
                'Belum ada data tersimpan untuk filter ini. Jalankan sync command atau longgarkan filter tanggal/unit.',
            ];
        }

        $recommendations = [];
        $highIdle = $rows->where('idle_ratio', '>=', 35)->count();
        $criticalUnits = $rows->where('status', 'Critical')->count() + $rows->where('status', 'High Risk')->count();
        $highRisk = $rows->filter(fn ($row) => ($row->total_harsh_events + $row->total_speeding_events) >= 10)->count();
        $lowPa = $rows->where('pa_score', '<', 65)->count();
        $bestUnits = $rows->sortByDesc('pa_score')->take(5)->pluck('registration')->implode(', ');
        $worstIdleUnits = $rows->sortByDesc('idle_ratio')->take(5)->pluck('registration')->implode(', ');

        if ($summary->avg_idle_ratio >= 30) {
            $recommendations[] = 'Idle ratio rata-rata tinggi. Evaluasi waiting time dispatch, antrean loading, dan kebiasaan engine idle saat standby.';
        }

        if ($highIdle > 0) {
            $recommendations[] = $highIdle . ' unit memiliki idle ratio >= 35%. Prioritaskan coaching operator dan audit aktivitas standby untuk unit: ' . $worstIdleUnits . '.';
        }

        if ($lowPa > 0) {
            $recommendations[] = $lowPa . ' unit memiliki PA di bawah 65. Jadikan daftar pemantauan harian untuk menekan engine hidup saat unit tidak produktif.';
        }

        if ($highRisk > 0) {
            $recommendations[] = $highRisk . ' unit memiliki event safety tinggi. Hubungkan data ini dengan operator, shift, dan rute untuk tindakan korektif yang lebih presisi.';
        }

        if ($criticalUnits > 0) {
            $recommendations[] = $criticalUnits . ' unit masuk status risiko tinggi. Buat daftar follow up operasional dan inspeksi lapangan harian.';
        }

        if ($summary->avg_performance_score >= 80 && $bestUnits !== '') {
            $recommendations[] = 'Gunakan unit dengan PA dan performa terbaik sebagai benchmark SOP operasi: ' . $bestUnits . '.';
        }

        $recommendations[] = 'Tahap lanjutan yang paling bernilai adalah menambahkan master referensi unit, tipe unit, site, dan operator agar analisis keputusan bisa dipecah per kelompok.';

        return array_values(array_unique($recommendations));
    }

    protected function normalizeDate(?string $date): string
    {
        return Carbon::parse($date ?: now()->subDay()->format('Y-m-d'))->format('Y-m-d');
    }
}
