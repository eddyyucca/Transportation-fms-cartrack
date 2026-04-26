@extends('layouts.fleet-adminlte', ['title' => 'P2H Dashboard', 'selectedDate' => $selectedDate])

@push('styles')
<style>
    .content-wrapper { overflow-x: auto; }
    .p2h-slide-wrap { padding: 0 0 1rem; overflow-x: auto; }
    .p2h-slide { width: 1366px; height: 768px; margin: 0 auto; background: #e7edf4; display: grid; grid-template-columns: 290px 1fr; box-shadow: 0 18px 40px rgba(0,0,0,.16); overflow: hidden; }
    .p2h-side { background: #1f2838; color: #fff; padding: 18px 18px 12px; border-left: 8px solid #f6ae00; }
    .p2h-mini { color: #f6ae00; font-size: 11px; font-weight: 800; text-align: center; margin-top: 8px; }
    .p2h-title { text-align: center; font-size: 23px; font-weight: 900; letter-spacing: 6px; margin-top: 38px; }
    .p2h-subtitle { text-align: center; font-size: 10px; color: #dbe4ef; margin-top: 16px; font-style: italic; }
    .p2h-week { margin: 26px auto 10px; width: 232px; height: 54px; background: #f6ae00; color: #0f1724; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 900; }
    .p2h-period { text-align: center; color: #c8d5e5; font-size: 11px; margin-bottom: 10px; }
    .p2h-divider { height: 4px; margin: 12px 10px 16px; background: #35527a; }
    .p2h-kpi { border: 1px solid #365072; background: #182434; padding: 12px; display: grid; grid-template-columns: 86px 1fr; gap: 12px; margin-bottom: 8px; }
    .p2h-kpi .value { font-size: 18px; font-weight: 900; line-height: 1.15; }
    .p2h-kpi .value.green { color: #2ca581; }
    .p2h-kpi .value.soft { color: #dce6f4; }
    .p2h-kpi .value.orange { color: #d28a17; }
    .p2h-kpi .value.blue { color: #5f9df7; }
    .p2h-kpi .label { font-size: 13px; font-weight: 800; color: #fff; }
    .p2h-kpi .note { font-size: 10px; color: #93a4b8; margin-top: 7px; }
    .p2h-main { background: #fff; display: grid; grid-template-rows: 48px 40px 486px 24px 170px; }
    .p2h-header { background: #18345a; color: #fff; display: flex; align-items: center; padding: 0 16px; font-size: 23px; font-weight: 900; letter-spacing: 2px; }
    .p2h-header small { font-size: 13px; font-weight: 700; margin-left: 10px; letter-spacing: 1px; }
    .p2h-summary { background: #314c70; color: #ffd34d; display: flex; align-items: center; justify-content: center; padding: 0 18px; font-size: 11px; font-weight: 800; text-align: center; line-height: 1.5; }
    .p2h-chart-box { background: #f8fafc; padding: 12px 16px 8px; }
    .p2h-chart-title { text-align: center; font-size: 13px; font-weight: 800; color: #274c77; margin-bottom: 10px; }
    .p2h-band { background: #1f2838; color: #fff; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; padding: 4px 10px; font-size: 10px; font-weight: 800; }
    .p2h-band span { display: flex; align-items: center; justify-content: center; }
    .p2h-band .good { background: #123c1b; }
    .p2h-band .mid { background: #1f567a; }
    .p2h-band .low { background: #75212b; }
    .p2h-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; background: #1f2838; padding: 8px; }
    .p2h-panel { border: 1px solid rgba(255,255,255,.14); background: #182434; color: #fff; }
    .p2h-panel-header { padding: 6px 10px; font-size: 12px; font-weight: 900; text-align: center; }
    .p2h-panel-header.green { background: #2f5a24; }
    .p2h-panel-header.orange { background: #5f3617; }
    .p2h-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; padding: 8px; }
    .p2h-card { min-height: 110px; border: 1px solid rgba(255,255,255,.12); display: flex; flex-direction: column; justify-content: space-between; padding: 10px 8px; text-align: center; }
    .p2h-card.green { background: #0b2a11; }
    .p2h-card.orange { background: #2d1607; }
    .p2h-card .dept { font-size: 12px; font-weight: 700; line-height: 1.3; min-height: 34px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .p2h-card .score { font-size: 28px; font-weight: 900; line-height: 1; }
    .p2h-card .delta { font-size: 11px; font-weight: 700; line-height: 1.4; }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="page-title">P2H Dashboard</div>
            <div class="text-muted">Dashboard weekly P2H dengan tampilan slide presentasi.</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('fleet.p2h.index') }}" class="btn btn-outline-secondary"><i class="fas fa-table mr-1"></i>Weekly Table</a>
            <a href="{{ route('fleet.p2h.daily.index') }}" class="btn btn-outline-primary"><i class="fas fa-upload mr-1"></i>Daily Checklist</a>
            <a href="{{ route('fleet.p2h.units.index') }}" class="btn btn-outline-secondary"><i class="fas fa-cog mr-1"></i>Master Data</a>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="p2h-slide-wrap">
            <section class="p2h-slide">
                <aside class="p2h-side">
                    <div class="p2h-mini">PT SULAWESI CAHAYA MINERAL</div>
                    <div class="p2h-title">P2H ONLINE</div>
                    <div class="p2h-subtitle">Pre-Start Vehicle Inspection</div>
                    <div class="p2h-week">WEEK {{ $selectedWeek }}</div>
                    <div class="p2h-period">{{ $startDate->format('d M') }} - {{ $endDate->format('d M Y') }}</div>
                    <div class="p2h-divider"></div>

                    <div class="p2h-kpi">
                        <div class="value green">{{ number_format($overallPercent, 1) }}%</div>
                        <div>
                            <div class="label">Overall W{{ $selectedWeek }}</div>
                            <div class="note">vs W{{ $selectedWeek - 1 }} {{ number_format($departmentStats->avg('w_prev1') ?? 0, 1) }}%</div>
                        </div>
                    </div>
                    <div class="p2h-kpi">
                        <div class="value soft">{{ $totalUnits }}</div>
                        <div>
                            <div class="label">Total Units</div>
                            <div class="note">{{ $totalDepartments }} departemen aktif</div>
                        </div>
                    </div>
                    <div class="p2h-kpi">
                        <div class="value green">{{ $achieveUnits }}</div>
                        <div>
                            <div class="label">Achieve</div>
                            <div class="note">Unit comply penuh 7 hari</div>
                        </div>
                    </div>
                    <div class="p2h-kpi">
                        <div class="value orange">{{ $notAchieveUnits }}</div>
                        <div>
                            <div class="label">Not Achieve</div>
                            <div class="note">Unit masih belum lengkap</div>
                        </div>
                    </div>
                    <div class="p2h-kpi">
                        <div class="value blue">{{ $improved }} Dept</div>
                        <div>
                            <div class="label">Movement</div>
                            <div class="note">{{ $declined }} turun | {{ $stable }} stabil</div>
                        </div>
                    </div>
                </aside>

                <main class="p2h-main">
                    <div class="p2h-header">
                        P2H ONLINE
                        <small>| WEEK {{ $selectedWeek }} | COMPLIANCE PER SECTION (Sorted: Highest -> Lowest)</small>
                    </div>
                    <div class="p2h-summary">
                        Overall W{{ $selectedWeek }}: {{ number_format($overallPercent, 1) }}% | W{{ $selectedWeek - 1 }}: {{ number_format($departmentStats->avg('w_prev1') ?? 0, 1) }}% | W{{ $selectedWeek - 2 }}: {{ number_format($departmentStats->avg('w_prev2') ?? 0, 1) }}% | {{ $achieveUnits }} Achieve | {{ $notAchieveUnits }} Not Achieve | {{ $totalUnits }} Units | {{ $improved }} Dept Naik | {{ $declined }} Turun | {{ $stable }} Stabil
                    </div>

                    <div class="p2h-chart-box">
                        <div class="p2h-chart-title"></div>
                        <canvas id="p2hComplianceChart"></canvas>
                    </div>

                    <div class="p2h-band">
                        <span class="good">&gt;= 85% Top Performer</span>
                        <span class="mid">70 - 84% Good</span>
                        <span class="low">&lt; 70% Needs Action</span>
                    </div>

                    <div class="p2h-bottom">
                        <section class="p2h-panel">
                            <div class="p2h-panel-header green">TOP PERFORMERS W{{ $selectedWeek }}</div>
                            <div class="p2h-cards">
                                @foreach($topPerformers as $performer)
                                    @php $delta = round($performer['w_current'] - $performer['w_prev1'], 1); @endphp
                                    <article class="p2h-card green">
                                        <div class="dept">{{ $performer['department'] }}</div>
                                        <div class="score">{{ number_format($performer['w_current'], 0) }}%</div>
                                        <div class="delta">{{ $delta > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($delta, 1), '0'), '.') }} vs W{{ $selectedWeek - 1 }}</div>
                                    </article>
                                @endforeach
                            </div>
                        </section>

                        <section class="p2h-panel">
                            <div class="p2h-panel-header orange">NEEDS ACTION W{{ $selectedWeek }}</div>
                            <div class="p2h-cards">
                                @foreach($needsAction as $performer)
                                    @php $delta = round($performer['w_current'] - $performer['w_prev1'], 1); @endphp
                                    <article class="p2h-card orange">
                                        <div class="dept">{{ $performer['department'] }}</div>
                                        <div class="score">{{ number_format($performer['w_current'], 0) }}%</div>
                                        <div class="delta">{{ $delta > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($delta, 1), '0'), '.') }} vs W{{ $selectedWeek - 1 }}</div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    </div>
                </main>
            </section>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
const p2hChart = @json($chart);

new Chart(document.getElementById('p2hComplianceChart'), {
    type: 'bar',
    data: {
        labels: p2hChart.labels,
        datasets: [
            { label: p2hChart.week_labels[0], data: p2hChart.w_prev2, backgroundColor: '#cfd6df', borderRadius: 0, maxBarThickness: 16 },
            { label: p2hChart.week_labels[1], data: p2hChart.w_prev1, backgroundColor: '#4c647d', borderRadius: 0, maxBarThickness: 16 },
            { label: p2hChart.week_labels[2], data: p2hChart.w_current, backgroundColor: '#2d6c99', borderRadius: 0, maxBarThickness: 16 },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: { boxWidth: 10, color: '#334155', font: { size: 11 }, usePointStyle: true, pointStyle: 'rect' }
            }
        },
        layout: {
            padding: { left: 0, right: 0, top: 0, bottom: 0 }
        },
        scales: {
            x: {
                ticks: {
                    color: '#475569',
                    font: { size: 10 },
                    maxRotation: 45,
                    minRotation: 45,
                    autoSkip: false,
                },
                grid: { display: false }
            },
            y: {
                beginAtZero: true,
                max: 100,
                ticks: { color: '#64748b', font: { size: 11 } },
                grid: { color: '#dbe3ee' }
            }
        }
    }
});
</script>
@endpush
