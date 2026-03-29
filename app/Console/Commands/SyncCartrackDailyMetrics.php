<?php

namespace App\Console\Commands;

use App\Services\CartrackApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncCartrackDailyMetrics extends Command
{
    protected $signature   = 'fleet:sync-metrics {--date= : Tanggal Y-m-d, default kemarin}';
    protected $description = 'Sync data harian unit dari Cartrack API ke fleet_unit_daily_metrics';

    public function __construct(protected CartrackApiService $cartrack)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->format('Y-m-d')
            : now()->subDay()->format('Y-m-d');

        $this->info("Sync data Cartrack untuk tanggal: $date");

        $result = $this->cartrack->syncDailyMetrics($date);

        $this->info($result['message']);
        $this->line("  ✓ Synced : {$result['synced']} unit");
        $this->line("  ✗ Skipped: {$result['skipped']} unit (tidak ada di master unit / registration kosong)");

        return 0;
    }
}