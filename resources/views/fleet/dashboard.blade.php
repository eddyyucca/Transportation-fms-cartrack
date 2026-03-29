@extends('layouts.fleet-adminlte', ['title' => 'Fleet Dashboard', 'selectedDate' => $dateTo])

@php
    $fmtHour = fn($min) => number_format(($min ?? 0) / 60, 2) . ' h';
    $statusClass = function ($status) {
        return strtolower(str_replace(' ', '-', $status ?: 'good'));
    };
@endphp

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <div class="page-title">Fleet Dashboard Summary</div>
                <div class="text-muted">
                    Dashboard untuk PA, idle behavior, utilization, safety, dan ringkasan keputusan operasional.
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="card filter-card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('fleet.dashboard') }}" class="row align-items-end">
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="font-weight-bold">Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>

                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="font-weight-bold">Sampai Tanggal</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>

                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="font-weight-bold">Registration</label>
                        <input type="text" name="registration" class="form-control" value="{{ $filters['registration'] }}" placeholder="mis. DT-1042">
                    </div>

                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="font-weight-bold">Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua</option>
                            <option value="Excellent" {{ $filters['status'] === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                            <option value="Good" {{ $filters['status'] === 'Good' ? 'selected' : '' }}>Good</option>
                            <option value="Monitor" {{ $filters['status'] === 'Monitor' ? 'selected' : '' }}>Monitor</option>
                            <option value="Critical" {{ $filters['status'] === 'Critical' ? 'selected' : '' }}>Critical</option>
                            <option value="High Risk" {{ $filters['status'] === 'High Risk' ? 'selected' : '' }}>High Risk</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="font-weight-bold">Sort By</label>
                        <select name="sort" class="form-control">
                            <option value="performance_score" {{ $filters['sort'] === 'performance_score' ? 'selected' : '' }}>Performance</option>
                            <option value="pa_score" {{ $filters['sort'] === 'pa_score' ? 'selected' : '' }}>PA</option>
                            <option value="distance_km" {{ $filters['sort'] === 'distance_km' ? 'selected' : '' }}>Distance</option>
                            <option value="total_trips" {{ $filters['sort'] === 'total_trips' ? 'selected' : '' }}>Trips</option>
                            <option value="idle_ratio" {{ $filters['sort'] === 'idle_ratio' ? 'selected' : '' }}>Idle Ratio</option>
                            <option value="safety_score" {{ $filters['sort'] === 'safety_score' ? 'selected' : '' }}>Safety Score</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 mb-3">
                        <button class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-search mr-1"></i> Tampilkan
                        </button>
                        <a href="{{ route('fleet.dashboard.export', request()->query()) }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-file-csv mr-1"></i> Export CSV
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if(!$summary)
            <div class="alert alert-warning shadow-sm border-0 rounded-lg">
                Data untuk filter ini belum tersedia. Jalankan command
                <strong>php artisan fleet:sync-cartrack --date={{ $dateTo }}</strong>.
            </div>
        @endif

        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="small-box bg-fleet-primary">
                    <div class="inner">
                        <h3>{{ number_format($summary->total_vehicles ?? 0) }}</h3>
                        <p>Total Vehicles</p>
                        <div class="metric-note">Unique unit sesuai filter</div>
                    </div>
                    <div class="icon"><i class="fas fa-truck-moving"></i></div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="small-box bg-fleet-success">
                    <div class="inner">
                        <h3>{{ number_format($summary->total_trips ?? 0) }}</h3>
                        <p>Total Trips</p>
                        <div class="metric-note">Total perjalanan pada rentang tanggal</div>
                    </div>
                    <div class="icon"><i class="fas fa-route"></i></div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="small-box bg-fleet-warning">
                    <div class="inner">
                        <h3>{{ number_format($summary->total_distance_km ?? 0, 2) }}</h3>
                        <p>Total Distance (KM)</p>
                        <div class="metric-note">Akumulasi jarak seluruh unit</div>
                    </div>
                    <div class="icon"><i class="fas fa-road"></i></div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="small-box bg-fleet-danger">
                    <div class="inner">
                        <h3>{{ number_format(($summary->total_harsh_events ?? 0) + ($summary->total_speeding_events ?? 0)) }}</h3>
                        <p>Safety Events</p>
                        <div class="metric-note">Harsh + speeding events</div>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="mini-kpi">
                    <div class="label">Engine On</div>
                    <div class="value">{{ $fmtHour($summary->total_engine_on_min ?? 0) }}</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="mini-kpi">
                    <div class="label">Driving Time</div>
                    <div class="value">{{ $fmtHour($summary->total_driving_min ?? 0) }}</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="mini-kpi">
                    <div class="label">Idle Time</div>
                    <div class="value">{{ $fmtHour($summary->total_idle_min ?? 0) }}</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="mini-kpi">
                    <div class="label">Average PA</div>
                    <div class="value">{{ number_format($summary->avg_pa_score ?? 0, 2) }}%</div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">Average Idle Ratio</div>
                        <h3 class="font-weight-bold text-warning">{{ number_format($summary->avg_idle_ratio ?? 0, 2) }}%</h3>
                        <div class="progress progress-sm mt-3">
                            <div class="progress-bar bg-warning" style="width: {{ min(100, (float)($summary->avg_idle_ratio ?? 0)) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">Average Utilization</div>
                        <h3 class="font-weight-bold text-primary">{{ number_format($summary->avg_utilization_ratio ?? 0, 2) }}%</h3>
                        <div class="progress progress-sm mt-3">
                            <div class="progress-bar bg-primary" style="width: {{ min(100, (float)($summary->avg_utilization_ratio ?? 0)) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">Average Safety Score</div>
                        <h3 class="font-weight-bold text-success">{{ number_format($summary->avg_safety_score ?? 0, 2) }}</h3>
                        <div class="progress progress-sm mt-3">
                            <div class="progress-bar bg-success" style="width: {{ min(100, (float)($summary->avg_safety_score ?? 0)) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">Average Performance Score</div>
                        <h3 class="font-weight-bold text-danger">{{ number_format($summary->avg_performance_score ?? 0, 2) }}</h3>
                        <div class="progress progress-sm mt-3">
                            <div class="progress-bar bg-danger" style="width: {{ min(100, (float)($summary->avg_performance_score ?? 0)) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8 mb-4">
                <div class="card h-100">
                    <div class="card-header border-0 bg-white">
                        <h3 class="card-title font-weight-bold">Trend PA, Utilization, Safety</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-box chart-box-lg">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 mb-4">
                <div class="card h-100">
                    <div class="card-header border-0 bg-white">
                        <h3 class="card-title font-weight-bold">Rekomendasi Keputusan</h3>
                    </div>
                    <div class="card-body">
                        <ul class="recommendation-list pl-3 mb-0">
                            @forelse($recommendations as $item)
                                <li>{{ $item }}</li>
                            @empty
                                <li>Tidak ada rekomendasi untuk periode ini.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header border-0 bg-white">
                        <h3 class="card-title font-weight-bold">Top Distance per Vehicle</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-box chart-box-md">
                            <canvas id="distanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header border-0 bg-white">
                        <h3 class="card-title font-weight-bold">Top Trips per Vehicle</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-box chart-box-md">
                            <canvas id="tripChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header border-0 bg-white">
                        <h3 class="card-title font-weight-bold">Highest Idle Time</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-box chart-box-md">
                            <canvas id="idleChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header border-0 bg-white">
                        <h3 class="card-title font-weight-bold">Best PA Unit</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-box chart-box-md">
                            <canvas id="paChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 mb-4">
                <div class="card h-100">
                    <div class="card-header border-0 bg-white">
                        <h3 class="card-title font-weight-bold">Worst Idle Ratio</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-box chart-box-sm">
                            <canvas id="worstIdleRatioChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title font-weight-bold mb-2 mb-md-0">Detail Unit Performance</h3>
                        <span class="text-muted">Klik registration untuk membuka histori detail unit</span>
                    </div>

                    <div class="card-body table-responsive p-0" style="max-height: 650px;">
                        <table class="table table-hover table-head-fixed text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Registration</th>
                                    <th>Trips</th>
                                    <th>Distance</th>
                                    <th>Engine On</th>
                                    <th>Driving</th>
                                    <th>Idle</th>
                                    <th>Idle Ratio</th>
                                    <th>Utilization</th>
                                    <th>PA</th>
                                    <th>Safety</th>
                                    <th>Performance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $row)
                                    <tr>
                                        <td>{{ optional($row->report_date)->format('Y-m-d') }}</td>
                                        <td class="font-weight-bold">
                                            <a class="summary-link" href="{{ route('fleet.vehicle.detail', ['registration' => $row->registration, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                                                {{ $row->registration }}
                                            </a>
                                        </td>
                                        <td>{{ number_format($row->total_trips) }}</td>
                                        <td>{{ number_format($row->distance_km, 2) }} km</td>
                                        <td>{{ $fmtHour($row->total_engine_on_min) }}</td>
                                        <td>{{ $fmtHour($row->total_driving_min) }}</td>
                                        <td>{{ $fmtHour($row->total_idle_min) }}</td>
                                        <td>{{ number_format($row->idle_ratio, 2) }}%</td>
                                        <td>{{ number_format($row->utilization_ratio, 2) }}%</td>
                                        <td>{{ number_format($row->pa_score, 2) }}%</td>
                                        <td>{{ number_format($row->safety_score, 2) }}</td>
                                        <td>{{ number_format($row->performance_score, 2) }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $statusClass($row->status) }}">
                                                {{ $row->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center text-muted py-4">Tidak ada data untuk filter ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .chart-box {
        position: relative;
        width: 100%;
    }

    .chart-box-lg {
        height: 320px;
    }

    .chart-box-md {
        height: 300px;
    }

    .chart-box-sm {
        height: 280px;
    }

    @media (max-width: 991.98px) {
        .chart-box-lg,
        .chart-box-md,
        .chart-box-sm {
            height: 280px;
        }
    }

    @media (max-width: 575.98px) {
        .chart-box-lg,
        .chart-box-md,
        .chart-box-sm {
            height: 240px;
        }
    }
</style>
@endsection

@push('scripts')
<script>
(() => {
    const trendLabels = @json($trendChart['labels'] ?? []);
    const trendPa = @json($trendChart['pa'] ?? []);
    const trendUtilization = @json($trendChart['utilization'] ?? []);
    const trendSafety = @json($trendChart['safety'] ?? []);

    const distanceLabels = @json($chartTopDistance['labels'] ?? []);
    const distanceValues = @json($chartTopDistance['values'] ?? []);

    const idleLabels = @json($chartTopIdle['labels'] ?? []);
    const idleValues = @json($chartTopIdle['values'] ?? []);

    const tripLabels = @json($chartTopTrips['labels'] ?? []);
    const tripValues = @json($chartTopTrips['values'] ?? []);

    const paLabels = @json($chartBestPa['labels'] ?? []);
    const paValues = @json($chartBestPa['values'] ?? []);

    const worstIdleLabels = @json($chartWorstIdleRatio['labels'] ?? []);
    const worstIdleValues = @json($chartWorstIdleRatio['values'] ?? []);

    window.fleetCharts = window.fleetCharts || {};

    const destroyChart = (key) => {
        if (window.fleetCharts[key]) {
            window.fleetCharts[key].destroy();
            window.fleetCharts[key] = null;
        }
    };

    const buildLineChart = (id, key, config) => {
        const el = document.getElementById(id);
        if (!el) return;

        destroyChart(key);

        window.fleetCharts[key] = new Chart(el, config);
    };

    const buildBarChart = (id, key, labels, values, color, horizontal = false) => {
        const el = document.getElementById(id);
        if (!el) return;

        destroyChart(key);

        window.fleetCharts[key] = new Chart(el, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: color,
                    borderRadius: 8,
                    maxBarThickness: 28
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                indexAxis: horizontal ? 'y' : 'x',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: !horizontal
                    },
                    y: {
                        beginAtZero: horizontal ? true : false
                    }
                }
            }
        });
    };

    buildLineChart('trendChart', 'trendChart', {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [
                {
                    label: 'PA',
                    data: trendPa,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,.08)',
                    tension: 0.35,
                    fill: false,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                {
                    label: 'Utilization',
                    data: trendUtilization,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22,163,74,.08)',
                    tension: 0.35,
                    fill: false,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                {
                    label: 'Safety',
                    data: trendSafety,
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220,38,38,.08)',
                    tension: 0.35,
                    fill: false,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    buildBarChart('distanceChart', 'distanceChart', distanceLabels, distanceValues, 'rgba(37,99,235,.8)', false);
    buildBarChart('tripChart', 'tripChart', tripLabels, tripValues, 'rgba(22,163,74,.8)', false);
    buildBarChart('idleChart', 'idleChart', idleLabels, idleValues, 'rgba(245,158,11,.85)', false);
    buildBarChart('paChart', 'paChart', paLabels, paValues, 'rgba(15,23,42,.8)', false);
    buildBarChart('worstIdleRatioChart', 'worstIdleRatioChart', worstIdleLabels, worstIdleValues, 'rgba(220,38,38,.75)', true);
})();
</script>
@endpush