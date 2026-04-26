<?php

use App\Http\Controllers\FleetDashboardController;
use App\Http\Controllers\FleetUnitController;
use App\Http\Controllers\FleetMetricController;
use App\Http\Controllers\BusLvReportController;
use App\Http\Controllers\P2hReportController;
use App\Http\Controllers\P2hUnitController;
use App\Http\Controllers\PmCheckReportController;
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
    Route::redirect('/fleet/pm/report', '/fleet/pm-reports', 301);
    Route::get('/fleet/pm/create', [PmScheduleController::class, 'create'])->name('fleet.pm.create');
    Route::post('/fleet/pm', [PmScheduleController::class, 'store'])->name('fleet.pm.store');
    Route::get('/fleet/pm/{pm}/edit', [PmScheduleController::class, 'edit'])->name('fleet.pm.edit');
    Route::put('/fleet/pm/{pm}', [PmScheduleController::class, 'update'])->name('fleet.pm.update');
    Route::delete('/fleet/pm/{pm}', [PmScheduleController::class, 'destroy'])->name('fleet.pm.destroy');
    Route::post('/fleet/pm/{pm}/done', [PmScheduleController::class, 'markDone'])->name('fleet.pm.done');
    Route::post('/fleet/pm/{pm}/notif', [PmScheduleController::class, 'sendNotif'])->name('fleet.pm.notif');
    Route::post('/fleet/pm/send-overdue', [PmScheduleController::class, 'sendOverdueNotif'])->name('fleet.pm.overdue-notif');
    Route::post('/fleet/pm/generate-biweekly', [PmScheduleController::class, 'generateBiweekly'])->name('fleet.pm.generate');

    // PM Check Reports
    Route::get('/fleet/pm-reports', [PmCheckReportController::class, 'index'])->name('fleet.pm-reports.index');
    Route::get('/fleet/pm-reports/create', [PmCheckReportController::class, 'create'])->name('fleet.pm-reports.create');
    Route::post('/fleet/pm-reports', [PmCheckReportController::class, 'store'])->name('fleet.pm-reports.store');
    Route::get('/fleet/pm-reports/{pmReport}', [PmCheckReportController::class, 'show'])->name('fleet.pm-reports.show');
    Route::get('/fleet/pm-reports/{pmReport}/edit', [PmCheckReportController::class, 'edit'])->name('fleet.pm-reports.edit');
    Route::put('/fleet/pm-reports/{pmReport}', [PmCheckReportController::class, 'update'])->name('fleet.pm-reports.update');
    Route::delete('/fleet/pm-reports/{pmReport}', [PmCheckReportController::class, 'destroy'])->name('fleet.pm-reports.destroy');

    // Bus & LV Reports
    Route::get('/fleet/bus-lv-reports', [BusLvReportController::class, 'index'])->name('fleet.bus-lv-reports.index');
    Route::get('/fleet/bus-lv-reports/create', [BusLvReportController::class, 'create'])->name('fleet.bus-lv-reports.create');
    Route::post('/fleet/bus-lv-reports', [BusLvReportController::class, 'store'])->name('fleet.bus-lv-reports.store');
    Route::get('/fleet/bus-lv-reports/{busLvReport}', [BusLvReportController::class, 'show'])->name('fleet.bus-lv-reports.show');
    Route::get('/fleet/bus-lv-reports/{busLvReport}/edit', [BusLvReportController::class, 'edit'])->name('fleet.bus-lv-reports.edit');
    Route::put('/fleet/bus-lv-reports/{busLvReport}', [BusLvReportController::class, 'update'])->name('fleet.bus-lv-reports.update');
    Route::delete('/fleet/bus-lv-reports/{busLvReport}', [BusLvReportController::class, 'destroy'])->name('fleet.bus-lv-reports.destroy');

    // P2H
    Route::get('/fleet/p2h', [P2hReportController::class, 'index'])->name('fleet.p2h.index');
    Route::get('/fleet/p2h/dashboard', [P2hReportController::class, 'dashboard'])->name('fleet.p2h.dashboard');
    Route::get('/fleet/p2h/daily', [P2hReportController::class, 'dailyIndex'])->name('fleet.p2h.daily.index');
    Route::get('/fleet/p2h/daily/{checklist}/edit', [P2hReportController::class, 'editChecklist'])->name('fleet.p2h.daily.edit');
    Route::put('/fleet/p2h/daily/{checklist}', [P2hReportController::class, 'updateChecklist'])->name('fleet.p2h.daily.update');
    Route::post('/fleet/p2h/manual', [P2hReportController::class, 'manualStore'])->name('fleet.p2h.manual.store');
    Route::post('/fleet/p2h/upload', [P2hReportController::class, 'uploadChecklist'])->name('fleet.p2h.upload');
    Route::post('/fleet/p2h/import-master', [P2hReportController::class, 'importMaster'])->name('fleet.p2h.import-master');
    Route::get('/fleet/p2h/units', [P2hUnitController::class, 'index'])->name('fleet.p2h.units.index');
    Route::get('/fleet/p2h/units/create', [P2hUnitController::class, 'create'])->name('fleet.p2h.units.create');
    Route::post('/fleet/p2h/units', [P2hUnitController::class, 'store'])->name('fleet.p2h.units.store');
    Route::get('/fleet/p2h/units/{unit}/edit', [P2hUnitController::class, 'edit'])->name('fleet.p2h.units.edit');
    Route::put('/fleet/p2h/units/{unit}', [P2hUnitController::class, 'update'])->name('fleet.p2h.units.update');

});
