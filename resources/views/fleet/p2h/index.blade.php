@extends('layouts.fleet-adminlte', ['title' => 'P2H Online', 'selectedDate' => $selectedDate])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="page-title">P2H Online</div>
                <div class="text-muted">Tabel utama hasil report P2H per minggu. Setiap week bisa dibuka dashboard laporannya.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('fleet.p2h.daily.index') }}" class="btn btn-outline-primary"><i class="fas fa-upload mr-1"></i>Daily Checklist</a>
                <a href="{{ route('fleet.p2h.units.index') }}" class="btn btn-outline-secondary"><i class="fas fa-cog mr-1"></i>Master Data</a>
            </div>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title font-weight-bold">Daftar Weekly Report P2H</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Week</th>
                                <th>Periode</th>
                                <th>Overall</th>
                                <th>Total Unit</th>
                                <th>Achieve</th>
                                <th>Not Achieve</th>
                                <th>Department</th>
                                <th>Update Terakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $index => $report)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="badge badge-info">W{{ $report['week_number'] }}</span> <small class="text-muted">{{ $report['report_year'] }}</small></td>
                                    <td>{{ $report['start_date']->format('d M Y') }} - {{ $report['end_date']->format('d M Y') }}</td>
                                    <td class="font-weight-bold">{{ number_format($report['overall_percent'], 1) }}%</td>
                                    <td>{{ $report['total_units'] }}</td>
                                    <td>{{ $report['achieve_units'] }}</td>
                                    <td>{{ $report['not_achieve_units'] }}</td>
                                    <td>{{ $report['departments'] }}</td>
                                    <td>{{ $report['updated_at']?->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('fleet.p2h.dashboard', ['week' => $report['week_number'], 'year' => $report['report_year']]) }}" class="btn btn-sm btn-primary" title="Lihat Dashboard">
                                            <i class="fas fa-chart-pie"></i>
                                        </a>
                                        <a href="{{ route('fleet.p2h.daily.index', ['week' => $report['week_number'], 'year' => $report['report_year']]) }}" class="btn btn-sm btn-outline-primary" title="Lihat Data Mingguan">
                                            <i class="fas fa-table"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">Belum ada weekly report P2H.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
