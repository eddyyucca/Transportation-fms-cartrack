<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Scheduler Laravel: setiap jam 00:00 ambil data hari sebelumnya
Schedule::command('fleet:sync-cartrack')->dailyAt('00:00');

Artisan::command('fleet:sync-today', function () {
    $this->call('fleet:sync-cartrack', ['--date' => now()->format('Y-m-d')]);
})->purpose('Sync fleet data untuk hari ini');
