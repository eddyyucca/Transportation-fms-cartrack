<?php

namespace App\Console\Commands;

use App\Models\FleetDailySummary;
use App\Models\FleetVehicleDailyStat;
use App\Services\CartrackApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncCartrackTripsCommand extends Command
{
    protected $signature = 'fleet:sync-cartrack {--date=}';
    protected $description = 'Ambil data Cartrack REST API harian lalu simpan ke database';

    public function handle(CartrackApiService $service): int
    {
        $date = $this->option('date') ?: now()->subDay()->format('Y-m-d');
        $date = Carbon::parse($date)->format('Y-m-d');

        $this->info('Sync data Cartrack tanggal: ' . $date);

        try {
            $payload = $service->fetchTripsByDate($date);
            $rows = $service->aggregateTripReport($payload['trips']);
            $summary = $service->buildSummary($rows, count($payload['trips']));

            DB::transaction(function () use ($date, $rows, $summary) {
                FleetVehicleDailyStat::query()->whereDate('report_date', $date)->delete();
                FleetDailySummary::query()->whereDate('report_date', $date)->delete();

                FleetDailySummary::create([
                    'report_date' => $date,
                    'total_vehicles' => $summary['total_vehicles'],
                    'total_trips' => $summary['total_trips'],
                    'total_distance_km' => $summary['total_distance_km'],
                    'total_engine_on_min' => $summary['total_engine_on_min'],
                    'total_driving_min' => $summary['total_driving_min'],
                    'total_idle_min' => $summary['total_idle_min'],
                    'total_harsh_acceleration' => $summary['total_harsh_acceleration'],
                    'total_harsh_braking' => $summary['total_harsh_braking'],
                    'total_harsh_cornering' => $summary['total_harsh_cornering'],
                    'total_threshold_speeding' => $summary['total_threshold_speeding'],
                    'total_road_speeding' => $summary['total_road_speeding'],
                    'total_harsh_events' => $summary['total_harsh_events'],
                    'total_speeding_events' => $summary['total_speeding_events'],
                    'avg_idle_ratio' => $summary['avg_idle_ratio'],
                    'avg_utilization_ratio' => $summary['avg_utilization_ratio'],
                    'avg_pa_score' => $summary['avg_pa_score'],
                    'avg_safety_score' => $summary['avg_safety_score'],
                    'avg_performance_score' => $summary['avg_performance_score'],
                    'raw_meta' => [
                        'source' => 'cartrack_rest_api',
                        'synced_at' => now()->toDateTimeString(),
                    ],
                ]);

                foreach (array_chunk($rows, 200) as $chunk) {
                    $insert = [];

                    foreach ($chunk as $row) {
                        $insert[] = array_merge($row, [
                            'report_date' => $date,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    FleetVehicleDailyStat::insert($insert);
                }
            });

            $this->info('Sync selesai. Total unit: ' . count($rows) . ', total trips: ' . count($payload['trips']));
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Sync gagal: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
