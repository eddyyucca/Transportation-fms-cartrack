@extends('layouts.fleet-adminlte', ['title' => 'Detail Unit ' . $registration, 'selectedDate' => $dateTo])

@php
    $fmtHour = fn($min) => number_format(($min ?? 0) / 60, 2) . ' h';
@endphp

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="page-title">Detail Unit {{ $registration }}</div>
                <div class="text-muted">Histori performa unit untuk analisis availability, idle, PA, dan produktivitas.</div>
            </div>
            <div>
                <a href="{{ route('fleet.dashboard', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="card filter-card mb-4">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Sampai Tanggal</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Tampilkan Histori</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4"><div class="small-box bg-fleet-primary"><div class="inner"><h3>{{ $summary['days'] }}</h3><p>Hari Terekam</p><div class="metric-note">Jumlah hari data pada periode ini</div></div><div class="icon"><i class="fas fa-calendar-day"></i></div></div></div>
            <div class="col-xl-3 col-md-6 mb-4"><div class="small-box bg-fleet-success"><div class="inner"><h3>{{ number_format($summary['total_trips']) }}</h3><p>Total Trips</p><div class="metric-note">Total perjalanan unit</div></div><div class="icon"><i class="fas fa-route"></i></div></div></div>
            <div class="col-xl-3 col-md-6 mb-4"><div class="small-box bg-fleet-warning"><div class="inner"><h3>{{ number_format($summary['distance_km'], 2) }}</h3><p>Total Distance (KM)</p><div class="metric-note">Akumulasi jarak unit</div></div><div class="icon"><i class="fas fa-road"></i></div></div></div>
            <div class="col-xl-3 col-md-6 mb-4"><div class="small-box bg-fleet-danger"><div class="inner"><h3>{{ $latest->status }}</h3><p>Status Terakhir</p><div class="metric-note">Status dari tanggal {{ optional($latest->report_date)->format('Y-m-d') }}</div></div><div class="icon"><i class="fas fa-exclamation-circle"></i></div></div></div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3"><div class="mini-kpi"><div class="label">Average PA</div><div class="value">{{ number_format($summary['avg_pa_score'], 2) }}%</div></div></div>
            <div class="col-lg-3 col-md-6 mb-3"><div class="mini-kpi"><div class="label">Average Idle Ratio</div><div class="value">{{ number_format($summary['avg_idle_ratio'], 2) }}%</div></div></div>
            <div class="col-lg-3 col-md-6 mb-3"><div class="mini-kpi"><div class="label">Average Utilization</div><div class="value">{{ number_format($summary['avg_utilization_ratio'], 2) }}%</div></div></div>
            <div class="col-lg-3 col-md-6 mb-3"><div class="mini-kpi"><div class="label">Average Performance</div><div class="value">{{ number_format($summary['avg_performance_score'], 2) }}</div></div></div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header border-0 bg-white"><h3 class="card-title font-weight-bold">Trend PA, Idle Ratio, Utilization</h3></div>
                    <div class="card-body"><canvas id="vehicleTrendChart" height="125"></canvas></div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header border-0 bg-white"><h3 class="card-title font-weight-bold">Ringkasan Tindakan</h3></div>
                    <div class="card-body">
                        <ul class="recommendation-list pl-3 mb-0">
                            <li>Jika idle ratio tinggi, cek apakah unit banyak standby dengan engine hidup saat menunggu tugas.</li>
                            <li>Jika PA turun tapi safety bagus, kemungkinan isu utamanya ada di dispatch, antrean, atau utilisasi alat.</li>
                            <li>Jika PA dan safety sama-sama rendah, prioritaskan unit ini untuk review operator dan pengawasan aktivitas harian.</li>
                            <li>Bandingkan unit ini dengan unit benchmark yang PA-nya tinggi pada tipe operasi yang sama.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4"><div class="card"><div class="card-header border-0 bg-white"><h3 class="card-title font-weight-bold">Distance per Day</h3></div><div class="card-body"><canvas id="distanceChart" height="120"></canvas></div></div></div>
            <div class="col-lg-6 mb-4"><div class="card"><div class="card-header border-0 bg-white"><h3 class="card-title font-weight-bold">Trips per Day</h3></div><div class="card-body"><canvas id="tripChart" height="120"></canvas></div></div></div>
        </div>

        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header border-0 bg-white"><h3 class="card-title font-weight-bold">Histori Harian Unit</h3></div>
                    <div class="card-body table-responsive p-0" style="max-height: 650px;">
                        <table class="table table-hover table-head-fixed text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
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
                                    <th>Harsh</th>
                                    <th>Speeding</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $row)
                                    <tr>
                                        <td>{{ optional($row->report_date)->format('Y-m-d') }}</td>
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
                                        <td>{{ number_format($row->total_harsh_events) }}</td>
                                        <td>{{ number_format($row->total_speeding_events) }}</td>
                                        <td>{{ $row->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
const labels = @json($trend['labels']);
const pa = @json($trend['pa']);
const idleRatio = @json($trend['idle_ratio']);
const utilization = @json($trend['utilization']);
const distance = @json($trend['distance']);
const trips = @json($trend['trips']);

new Chart(document.getElementById('vehicleTrendChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            { label: 'PA', data: pa, borderColor: '#2563eb', tension: .35, fill: false },
            { label: 'Idle Ratio', data: idleRatio, borderColor: '#f59e0b', tension: .35, fill: false },
            { label: 'Utilization', data: utilization, borderColor: '#16a34a', tension: .35, fill: false }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

new Chart(document.getElementById('distanceChart'), {
    type: 'bar',
    data: { labels, datasets: [{ data: distance, backgroundColor: 'rgba(37,99,235,.8)', borderRadius: 8 }] },
    options: { plugins: { legend: { display: false } } }
});

new Chart(document.getElementById('tripChart'), {
    type: 'bar',
    data: { labels, datasets: [{ data: trips, backgroundColor: 'rgba(22,163,74,.8)', borderRadius: 8 }] },
    options: { plugins: { legend: { display: false } } }
});
</script>
@endpush
