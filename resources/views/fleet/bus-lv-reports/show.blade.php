@extends('layouts.fleet-adminlte', ['title' => 'Bus & LV Dashboard', 'selectedDate' => $selectedDate])

@push('styles')
<style>
    .content-wrapper { overflow-x: auto; }
    .blv-wrap { padding: 0 0 1rem; overflow-x: auto; }
    .blv-slide { width: 1366px; height: 768px; margin: 0 auto; background: #e9eef4; display: grid; grid-template-columns: 290px 1fr; box-shadow: 0 18px 40px rgba(0,0,0,.15); overflow: hidden; }
    .blv-side { background: #1f2838; color: #fff; padding: 18px 18px 10px; border-left: 8px solid #f6ae00; }
    .blv-mini { color: #f6ae00; font-size: 11px; font-weight: 800; text-align: center; margin-top: 8px; }
    .blv-title { text-align: center; font-size: 26px; font-weight: 900; letter-spacing: 7px; margin-top: 40px; }
    .blv-subtitle { text-align: center; font-size: 10px; color: #dbe4ef; font-style: italic; margin-top: 18px; }
    .blv-week { margin: 28px auto 10px; width: 236px; height: 54px; background: #f6ae00; color: #0f1724; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 900; }
    .blv-period { text-align: center; color: #c8d5e5; font-size: 11px; margin-bottom: 10px; }
    .blv-divider { height: 4px; margin: 12px 12px 16px; background: #35527a; }
    .blv-section-label { text-align: center; color: #f6ae00; font-size: 12px; font-weight: 900; margin-bottom: 10px; }
    .blv-kpi { border: 1px solid #365072; background: #182434; padding: 10px 12px; display: grid; grid-template-columns: 84px 1fr; gap: 12px; margin-bottom: 8px; }
    .blv-kpi .value { font-size: 17px; font-weight: 900; line-height: 1.2; color: #22a399; }
    .blv-kpi .value.blue { color: #2f8bd0; }
    .blv-kpi .value.soft { color: #8ca2be; }
    .blv-kpi .label { font-size: 13px; font-weight: 800; color: #fff; }
    .blv-kpi .note { font-size: 10px; color: #92a3b9; margin-top: 8px; }
    .blv-main { background: #fff; display: grid; grid-template-rows: 48px 22px 274px 22px 328px 24px; }
    .blv-header { height: 48px; background: #18345a; color: #fff; display: flex; align-items: center; padding: 0 16px; font-size: 24px; font-weight: 900; letter-spacing: 2px; }
    .blv-header small { font-size: 14px; font-weight: 700; margin-left: 10px; letter-spacing: 1px; }
    .blv-strip { height: 22px; background: #314c70; color: #dce7f3; display: flex; align-items: center; padding: 0 14px; font-size: 10px; font-weight: 900; letter-spacing: 4px; }
    .blv-top { display: grid; grid-template-columns: 1.3fr .7fr; gap: 10px; padding: 0 10px; height: 274px; }
    .blv-bottom { display: grid; grid-template-columns: 1.3fr .7fr; gap: 10px; padding: 0 10px; height: 328px; }
    .blv-box { background: #f8fafc; border-top: 20px solid #dce6f1; padding: 8px 12px; position: relative; height: 100%; overflow: hidden; }
    .blv-box-title { position: absolute; top: -16px; left: 0; right: 0; text-align: center; font-size: 10px; font-weight: 900; color: #1e5689; }
    .blv-notes { background: #334f75; color: #fff; padding: 12px; height: 100%; overflow: hidden; }
    .blv-notes-title { color: #f6ae00; font-size: 12px; font-weight: 900; margin: 8px 0 14px; }
    .blv-note-item { border: 1px solid rgba(255,255,255,.18); background: #1a2636; padding: 14px 12px; margin-bottom: 10px; min-height: 54px; font-size: 12px; display: flex; align-items: center; gap: 10px; }
    .blv-footer { background: #173a66; color: #fff; font-size: 10px; text-align: center; padding: 4px 10px; }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="page-title">Bus & LV Dashboard</div>
            <div class="text-muted">Final report mingguan Bus dan LV mengikuti format presentasi.</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('fleet.bus-lv-reports.index') }}" class="btn btn-outline-secondary">Kembali</a>
            <a href="{{ route('fleet.bus-lv-reports.edit', $report) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Edit Report</a>
        </div>
    </div>
</div>

<section class="blv-wrap">
    <section class="blv-slide">
        <aside class="blv-side">
            <div class="blv-mini">{{ strtoupper($report->company_name) }}</div>
            <div class="blv-title">BUS & LV</div>
            <div class="blv-subtitle">SCM Transport Report</div>
            <div class="blv-week">WEEK {{ $report->week_number }}</div>
            <div class="blv-period">{{ $metrics['period_label'] }}</div>
            <div class="blv-divider"></div>

            <div class="blv-section-label">BUS SCM</div>
            <div class="blv-kpi">
                <div class="value">{{ $metrics['sidebar']['bus_co_total'] }}</div>
                <div>
                    <div class="label">Total CO</div>
                    <div class="note">Avg {{ rtrim(rtrim(number_format($metrics['sidebar']['bus_co_avg'], 1), '0'), '.') }}/hari</div>
                </div>
            </div>
            <div class="blv-kpi">
                <div class="value blue">{{ $metrics['sidebar']['bus_ci_total'] }}</div>
                <div>
                    <div class="label">Total CI</div>
                    <div class="note">Avg {{ rtrim(rtrim(number_format($metrics['sidebar']['bus_ci_avg'], 1), '0'), '.') }}/hari</div>
                </div>
            </div>
            <div class="blv-kpi">
                <div class="value soft">{{ $metrics['sidebar']['bus_extra_days'] }} hari</div>
                <div>
                    <div class="label">Extra Bus</div>
                    <div class="note">{{ $metrics['sidebar']['bus_extra_total'] }} unit tambahan dalam {{ $metrics['sidebar']['bus_extra_days'] }} hari</div>
                </div>
            </div>
            <div class="blv-kpi">
                <div class="value soft">{{ rtrim(rtrim(number_format($metrics['sidebar']['bus_units_avg'], 1), '0'), '.') }}</div>
                <div>
                    <div class="label">Avg Bus Reguler</div>
                    <div class="note">Rata-rata unit bus per hari</div>
                </div>
            </div>

            <div class="blv-divider"></div>
            <div class="blv-section-label">LV SCM</div>
            <div class="blv-kpi">
                <div class="value soft">{{ $metrics['sidebar']['lv_co_total'] }}</div>
                <div>
                    <div class="label">Total CO (LV)</div>
                    <div class="note">Total mingguan keberangkatan</div>
                </div>
            </div>
            <div class="blv-kpi">
                <div class="value soft">{{ $metrics['sidebar']['lv_ci_total'] }}</div>
                <div>
                    <div class="label">Total CI (LV)</div>
                    <div class="note">Total mingguan kedatangan</div>
                </div>
            </div>
            <div class="blv-kpi">
                <div class="value soft">{{ rtrim(rtrim(number_format($metrics['sidebar']['lv_units_avg'], 1), '0'), '.') }}</div>
                <div>
                    <div class="label">Avg LV Aktif</div>
                    <div class="note">Rata-rata unit LV per hari</div>
                </div>
            </div>
        </aside>

        <main class="blv-main">
            <div class="blv-header">
                {{ strtoupper($report->title) }}
                <small>| WEEK {{ $report->week_number }} | {{ strtoupper($metrics['period_label']) }}</small>
            </div>
            <div class="blv-strip">BUS SCM - DEPARTURE & ARRIVAL W{{ $report->week_number }}</div>

            <div class="blv-top">
                <div class="blv-box">
                    <div class="blv-box-title">CO vs CI per Hari (Passengers)</div>
                    <canvas id="dailyBusChart"></canvas>
                </div>
                <div class="blv-box">
                    <div class="blv-box-title">{{ $metrics['bus_compare_chart']['previous_label'] }} vs {{ $metrics['bus_compare_chart']['current_label'] }} BUS (Total)</div>
                    <canvas id="busCompareChart"></canvas>
                </div>
            </div>

            <div class="blv-strip">LV SCM W{{ $report->week_number }} - DATA PARSIAL</div>
            <div class="blv-bottom">
                <div class="blv-box">
                    <div class="blv-box-title">LV CO vs CI - Trend {{ $metrics['lv_trend_chart']['labels']->implode(' -> ') }}</div>
                    <canvas id="lvTrendChart"></canvas>
                </div>
                <div class="blv-notes">
                    <div class="blv-notes-title">Catatan LV W{{ $report->week_number }}</div>
                    @foreach($metrics['notes'] as $note)
                        <div class="blv-note-item">
                            <span>{{ $loop->iteration === 1 ? '📋' : ($loop->iteration === 2 ? '↑' : ($loop->iteration === 3 ? '↑' : '△')) }}</span>
                            <span>{{ $note !== '' ? $note : '-' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="blv-footer">
                BUS W{{ $report->week_number }}: CO {{ $metrics['sidebar']['bus_co_total'] }} | CI {{ $metrics['sidebar']['bus_ci_total'] }} | extra bus {{ $metrics['sidebar']['bus_extra_days'] }}
                @if($metrics['footer']['bus_co_delta'] !== null)
                    | {{ $metrics['bus_compare_chart']['previous_label'] }} -> W{{ $report->week_number }} CO: {{ $metrics['footer']['bus_co_delta'] >= 0 ? '+' : '' }}{{ $metrics['footer']['bus_co_delta'] }}
                    | CI: {{ $metrics['footer']['bus_ci_delta'] >= 0 ? '+' : '' }}{{ $metrics['footer']['bus_ci_delta'] }}
                @endif
                | LV data mingguan dari input manual
            </div>
        </main>
    </section>
</section>
@endsection

@push('scripts')
<script>
const busDaily = @json($metrics['daily_chart']);
const busCompare = @json($metrics['bus_compare_chart']);
const lvTrend = @json($metrics['lv_trend_chart']);

new Chart(document.getElementById('dailyBusChart'), {
    type: 'line',
    data: {
        labels: busDaily.labels,
        datasets: [
            { label: 'CO (Departure)', data: busDaily.co, borderColor: '#1d7d72', backgroundColor: '#1d7d72', borderWidth: 3, pointRadius: 4, tension: 0 },
            { label: 'CI (Arrival)', data: busDaily.ci, borderColor: '#275d8d', backgroundColor: '#275d8d', borderWidth: 3, pointRadius: 4, tension: 0 },
            { label: 'Reference', data: busDaily.target, borderColor: '#d43f3a', backgroundColor: '#d43f3a', borderWidth: 1, borderDash: [5, 5], pointRadius: 0, tension: 0 },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 16 } } },
        scales: { y: { beginAtZero: false }, x: { grid: { display: false } } }
    }
});

new Chart(document.getElementById('busCompareChart'), {
    type: 'bar',
    data: {
        labels: busCompare.labels,
        datasets: [
            { label: busCompare.previous_label, data: busCompare.previous, backgroundColor: '#4a6580' },
            { label: busCompare.current_label, data: busCompare.current, backgroundColor: '#2d6c99' },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } },
        scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
    }
});

new Chart(document.getElementById('lvTrendChart'), {
    type: 'bar',
    data: {
        labels: lvTrend.labels,
        datasets: [
            { label: 'CO', data: lvTrend.co, backgroundColor: '#278376' },
            { label: 'CI', data: lvTrend.ci, backgroundColor: '#2d6c99' },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } },
        scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
    }
});
</script>
@endpush
