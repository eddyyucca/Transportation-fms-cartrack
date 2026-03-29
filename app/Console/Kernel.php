<?php

namespace App\Console;

use App\Console\Commands\SyncCartrackTripsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        SyncCartrackTripsCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('fleet:sync-cartrack')->dailyAt('00:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
