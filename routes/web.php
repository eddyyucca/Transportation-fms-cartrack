<?php

use App\Http\Controllers\FleetDashboardController;
use App\Http\Controllers\FleetUnitController;
use App\Http\Controllers\FleetMetricController;
use App\Http\Controllers\PmScheduleController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Fleet routes (protected)
Route::middleware('auth')->group(function () {

    // Dashboard (existing)
    Route::get('/fleet-dashboard', [FleetDashboardController::class, 'index'])->name('fleet.dashboard');
    Route::get('/fleet-dashboard/export', [FleetDashboardController::class, 'exportDailyCsv'])->name('fleet.dashboard.export');
    Route::get('/fleet-dashboard/unit/{registration}', [FleetDashboardController::class, 'vehicleDetail'])->name('fleet.vehicle.detail');

    // Master Unit
    Route::get('/fleet/units', [FleetUnitController::class, 'index'])->name('fleet.units.index');
    Route::get('/fleet/units/create', [FleetUnitController::class, 'create'])->name('fleet.units.create');
    Route::post('/fleet/units', [FleetUnitController::class, 'store'])->name('fleet.units.store');
    Route::get('/fleet/units/{unit}/edit', [FleetUnitController::class, 'edit'])->name('fleet.units.edit');
    Route::put('/fleet/units/{unit}', [FleetUnitController::class, 'update'])->name('fleet.units.update');
    Route::delete('/fleet/units/{unit}', [FleetUnitController::class, 'destroy'])->name('fleet.units.destroy');
    Route::post('/fleet/units/{unit}/toggle-monitor', [FleetUnitController::class, 'toggleMonitor'])->name('fleet.units.toggle');

    // Daily Metrics
    Route::get('/fleet/metrics', [FleetMetricController::class, 'index'])->name('fleet.metrics.index');
    Route::get('/fleet/metrics/create', [FleetMetricController::class, 'create'])->name('fleet.metrics.create');
    Route::post('/fleet/metrics', [FleetMetricController::class, 'store'])->name('fleet.metrics.store');
    Route::get('/fleet/metrics/{metric}/edit', [FleetMetricController::class, 'edit'])->name('fleet.metrics.edit');
    Route::put('/fleet/metrics/{metric}', [FleetMetricController::class, 'update'])->name('fleet.metrics.update');
    Route::delete('/fleet/metrics/{metric}', [FleetMetricController::class, 'destroy'])->name('fleet.metrics.destroy');
    Route::get('/fleet/metrics/export', [FleetMetricController::class, 'export'])->name('fleet.metrics.export');

    // PM Schedule
    Route::get('/fleet/pm', [PmScheduleController::class, 'index'])->name('fleet.pm.index');
    Route::get('/fleet/pm/create', [PmScheduleController::class, 'create'])->name('fleet.pm.create');
    Route::post('/fleet/pm', [PmScheduleController::class, 'store'])->name('fleet.pm.store');
    Route::get('/fleet/pm/{pm}/edit', [PmScheduleController::class, 'edit'])->name('fleet.pm.edit');
    Route::put('/fleet/pm/{pm}', [PmScheduleController::class, 'update'])->name('fleet.pm.update');
    Route::delete('/fleet/pm/{pm}', [PmScheduleController::class, 'destroy'])->name('fleet.pm.destroy');
    Route::post('/fleet/pm/{pm}/done', [PmScheduleController::class, 'markDone'])->name('fleet.pm.done');
    Route::post('/fleet/pm/{pm}/notif', [PmScheduleController::class, 'sendNotif'])->name('fleet.pm.notif');
    Route::post('/fleet/pm/send-overdue', [PmScheduleController::class, 'sendOverdueNotif'])->name('fleet.pm.overdue-notif');
    Route::post('/fleet/pm/generate-biweekly', [PmScheduleController::class, 'generateBiweekly'])->name('fleet.pm.generate');

});