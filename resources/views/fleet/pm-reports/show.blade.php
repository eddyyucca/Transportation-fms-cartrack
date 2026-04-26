@extends('layouts.fleet-adminlte', ['title' => 'Dashboard PM Check', 'selectedDate' => optional($report->end_date)->format('Y-m-d')])

@push('styles')
<style>
    .pm-slide-wrap { padding: 0 1rem 1.5rem; overflow-x: auto; }
    .pm-slide {
        width: 1366px;
        min-height: 768px;
        margin: 0 auto;
        background: #eef1f4;
        display: grid;
        grid-template-columns: 330px 1fr;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(0,0,0,.15);
    }
    .pm-sidebar {
        position: relative;
        background: #14223a;
        color: #fff;
        padding: 30px 24px 0 24px;
    }
    .pm-sidebar::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 8px;
        width: 8px;
        background: #f0aa00;
    }
    .pm-company { margin-left: 72px; color: #ffd45a; font-size: 10px; font-weight: 900; }
    .pm-side-title { margin: 25px 0 0 52px; font-size: 30px; font-weight: 900; letter-spacing: 9px; line-height: 1; }
    .pm-subtitle { margin: 18px 0 0 78px; color: #c7d4e4; font-size: 10px; font-style: italic; }
    .pm-week { margin: 12px 10px 0; height: 59px; background: #f0aa00; color: #0f1a2c; display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 900; letter-spacing: 1px; }
    .pm-period { text-align: center; margin-top: 14px; color: #d7e0ec; font-size: 10px; }
    .pm-divider { height: 4px; margin: 13px 16px 8px; background: #466486; }
    .pm-kpi { height: 90px; margin: 13px 0; border: 1px solid #344a69; background: #15253d; display: grid; grid-template-columns: 1fr 1.25fr; align-items: center; padding: 0 22px; }
    .pm-kpi .value { font-size: 28px; font-weight: 900; color: #b9c9da; }
    .pm-kpi .value.green { color: #38a877; }
    .pm-kpi .value.gold { color: #f0aa00; }
    .pm-kpi .label { font-size: 11px; font-weight: 900; color: #fff; }
    .pm-kpi .note { margin-top: 16px; font-size: 9px; color: #9fb0c4; font-style: italic; }
    .pm-side-footer { display: none; }
    .pm-main { position: relative; }
    .pm-header { height: 59px; background: #123154; color: #fff; display: flex; align-items: center; padding-left: 25px; border-left: 7px solid #f0aa00; font-size: 16px; font-weight: 900; letter-spacing: 3px; }
    .pm-section-title, .pm-detail-title { height: 23px; margin: 5px 2px 0 0; background: #2f537b; color: #d9e7f6; display: flex; align-items: center; padding-left: 16px; font-size: 10px; font-weight: 900; letter-spacing: 6px; }
    .pm-detail-title { justify-content: center; margin-bottom: 4px; }
    .pm-charts { display: grid; grid-template-columns: 1.35fr .95fr; gap: 10px; padding: 0 16px 0 8px; height: 260px; }
    .pm-chart-box { background: #fafafa; border-top: 22px solid #dde5ee; position: relative; padding: 8px 14px 4px; }
    .pm-chart-title { position: absolute; top: -17px; left: 0; right: 0; color: #08436d; text-align: center; font-size: 10px; font-weight: 900; }
    .pm-vendor-grid { padding: 0 8px 0 2px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 11px; min-height: 351px; }
    .pm-vendor { background: #162b44; color: #fff; border: 3px solid #fff; position: relative; overflow: hidden; min-height: 351px; }
    .pm-vendor.bagong { border-color: #fff; }
    .pm-vendor.transkon { border-color: #ffc000; }
    .pm-vendor.trac { border-color: #fff; }
    .pm-vendor-head { height: 39px; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 900; border-bottom: 1px solid rgba(255,255,255,.25); }
    .pm-vendor.bagong .pm-vendor-head { background: #0f5a43; }
    .pm-vendor.transkon .pm-vendor-head { background: #9a6500; }
    .pm-vendor.trac .pm-vendor-head { background: #0d4b82; }
    .pm-vendor.default .pm-vendor-head { background: #37577d; }
    .pm-vendor-body { padding: 24px 26px 10px; display: grid; grid-template-columns: 1fr 1fr; align-items: start; }
    .pm-percent { font-size: 38px; font-weight: 900; line-height: 1; }
    .pm-plan-actual { display: grid; grid-template-columns: 1fr auto; row-gap: 16px; column-gap: 30px; font-size: 10px; font-weight: 700; }
    .pm-plan-actual .num { font-size: 18px; font-weight: 900; }
    .pm-status { grid-column: 1 / 3; margin-top: 26px; text-align: center; font-size: 11px; font-weight: 900; }
    .pm-daily-divider { height: 4px; background: #304d70; margin: 3px 12px 0; }
    .pm-daily-title { color: #7d93ad; text-align: center; margin-top: 9px; font-size: 9px; font-weight: 900; letter-spacing: 4px; }
    .pm-daily-bars { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; height: 61px; margin: 10px 12px 0; align-items: end; }
    .pm-daily-bars span { display: block; background: #2f8b63; }
    .pm-daily-bars span.low { background: #f0aa00; }
    .pm-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin: 9px 12px 0; color: #7d93ad; font-size: 8px; text-align: center; font-weight: 700; }
    .pm-vendor-total { position: absolute; left: 0; right: 0; bottom: 10px; text-align: center; font-size: 8px; font-weight: 900; }
    .pm-toolbar { padding: 0 1rem 1rem; }
    @media (max-width: 1400px) { .pm-slide-wrap { padding-bottom: 2rem; } }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="page-title">Dashboard PM Check</div>
            <div class="text-muted">Hasil report dibuat dari input CRUD per vendor dan ditampilkan seperti format dashboard referensi.</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('fleet.pm-reports.index') }}" class="btn btn-outline-secondary">Kembali</a>
            <a href="{{ route('fleet.pm-reports.edit', $report) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Edit Report</a>
        </div>
    </div>
</div>

<div class="pm-toolbar">
    <div class="container-fluid">
        <div class="alert alert-light border shadow-sm mb-0">
            <strong>{{ $report->title }}</strong> | Week {{ $report->week_number }} | {{ $report->start_date?->format('d M Y') }} - {{ $report->end_date?->format('d M Y') }}
        </div>
    </div>
</div>

<section class="pm-slide-wrap">
    <section class="pm-slide">
        <aside class="pm-sidebar">
            <div class="pm-company">{{ strtoupper($report->company_name) }}</div>
            <div class="pm-side-title">PM CHECK</div>
            <div class="pm-subtitle">Preventive Maintenance Report</div>
            <div class="pm-week">WEEK {{ $report->week_number }}</div>
            <div class="pm-period">{{ $report->start_date?->format('d') }} - {{ $report->end_date?->format('d M Y') }}</div>
            <div class="pm-divider"></div>

            <div class="pm-kpi">
                <div class="value">{{ $kpi->total_units }}</div>
                <div>
                    <div class="label">Total Units</div>
                    <div class="note">{{ $kpi->vendors }} Vendors</div>
                </div>
            </div>

            <div class="pm-kpi">
                <div class="value">{{ $kpi->plan_units }}</div>
                <div>
                    <div class="label">Plan PM W{{ $report->week_number }}</div>
                    <div class="note">Target periode</div>
                </div>
            </div>

            <div class="pm-kpi">
                <div class="value green">{{ $kpi->actual_units }}</div>
                <div>
                    <div class="label">Actual PM W{{ $report->week_number }}</div>
                    <div class="note">{{ $kpi->achievement_percent !== null ? number_format($kpi->achievement_percent, 1) . '% achieved' : 'Belum ada target' }}</div>
                </div>
            </div>

            <div class="pm-kpi">
                <div class="value green">{{ $kpi->achievement_percent !== null ? number_format($kpi->achievement_percent, 1) . '%' : '-' }}</div>
                <div>
                    <div class="label">Overall Achieve</div>
                    <div class="note">Report week {{ $report->week_number }} tahun {{ $report->report_year }}</div>
                </div>
            </div>

            <div class="pm-kpi">
                <div class="value gold">{{ $kpi->vendors }}</div>
                <div>
                    <div class="label">Vendors</div>
                    <div class="note">{{ $vendorCards->pluck('vendor_name')->implode(' · ') }}</div>
                </div>
            </div>

        </aside>

        <main class="pm-main">
            <div class="pm-header">{{ strtoupper($report->title) }} | WEEK {{ $report->week_number }} | SORTED: HIGHEST TO LOWEST</div>
            <div class="pm-section-title">WEEKLY TREND &amp; PLAN VS ACTUAL</div>

            <div class="pm-charts">
                <div class="pm-chart-box">
                    <div class="pm-chart-title">% Achieve Trend</div>
                    <canvas id="trendChart"></canvas>
                </div>
                <div class="pm-chart-box">
                    <div class="pm-chart-title">Plan vs Actual W{{ $report->week_number }} (Units)</div>
                    <canvas id="planActualChart"></canvas>
                </div>
            </div>

            <div class="pm-detail-title">DETAIL PER VENDOR - DAILY BREAKDOWN W{{ $report->week_number }}</div>

            <div class="pm-vendor-grid">
                @foreach($vendorCards as $vendor)
                    <div class="pm-vendor {{ $vendor['theme'] }}">
                        <div class="pm-vendor-head">{{ strtoupper($vendor['vendor_name']) }}</div>
                        <div class="pm-vendor-body">
                            <div class="pm-percent">{{ $vendor['achievement_percent'] !== null ? number_format($vendor['achievement_percent'], 1) . '%' : '-' }}</div>
                            <div class="pm-plan-actual">
                                <div>Plan</div><div class="num">{{ $vendor['plan_units'] }}</div>
                                <div>Actual</div><div class="num">{{ $vendor['actual_units'] }}</div>
                            </div>
                            <div class="pm-status">{{ $vendor['status_ok'] ? 'OK ' : '' }}{{ $vendor['status_text'] }}</div>
                        </div>
                        <div class="pm-daily-divider"></div>
                        <div class="pm-daily-title">DAILY W{{ $report->week_number }}</div>
                        <div class="pm-daily-bars">
                            @foreach($vendor['daily_bars'] as $bar)
                                <span class="{{ $bar['low'] ? 'low' : '' }}" style="height: {{ $bar['height'] }}px" title="{{ $bar['value'] }}"></span>
                            @endforeach
                        </div>
                        <div class="pm-days">
                            @for($i = 0; $i < 7; $i++)
                                <span>{{ $report->start_date?->copy()->addDays($i)->format('d') }}</span>
                            @endfor
                        </div>
                        <div class="pm-vendor-total">{{ $vendor['total_units'] }} units total</div>
                    </div>
                @endforeach
            </div>
        </main>
    </section>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.font.family = 'Arial, Helvetica, sans-serif';
Chart.defaults.color = '#21344c';

const trendPalette = ['#11765d', '#f0aa00', '#155f94', '#9c6ade', '#4f8a10'];
const trendData = @json($trendChart);
const trendDatasets = trendData.datasets.map((dataset, index) => ({
    label: dataset.label,
    data: dataset.data,
    borderColor: trendPalette[index % trendPalette.length],
    backgroundColor: trendPalette[index % trendPalette.length],
    tension: 0,
    pointRadius: 4,
    pointHoverRadius: 4,
    borderWidth: 3,
    spanGaps: true,
}));

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendData.labels,
        datasets: trendDatasets,
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { boxWidth: 28, usePointStyle: true, pointStyle: 'line', font: { size: 11, weight: 'bold' } }
            }
        },
        scales: {
            y: {
                min: 0,
                max: 105,
                ticks: { stepSize: 10, color: '#5b7794' },
                grid: { color: '#e5e7ea' }
            },
            x: { grid: { display: false }, ticks: { color: '#333', font: { size: 14 } } }
        }
    }
});

const planActualData = @json($planActualChart);
new Chart(document.getElementById('planActualChart'), {
    type: 'bar',
    data: {
        labels: planActualData.labels,
        datasets: [
            { label: 'Plan', data: planActualData.plan, backgroundColor: '#9aa6b3', borderWidth: 0 },
            { label: 'Actual', data: planActualData.actual, backgroundColor: '#2f8b63', borderWidth: 0 }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 5, color: '#5b7794' },
                grid: { color: '#e5e7ea' }
            },
            x: { grid: { display: false }, ticks: { color: '#333', font: { size: 14 } } }
        }
    }
});
</script>
@endpush
