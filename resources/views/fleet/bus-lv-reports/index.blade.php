@extends('layouts.fleet-adminlte', ['title' => 'Bus & LV Report', 'selectedDate' => now()->toDateString()])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="page-title">Bus & LV Report</div>
                <div class="text-muted">Report mingguan transport bus dan light vehicle dengan format dashboard presentasi.</div>
            </div>
            <a href="{{ route('fleet.bus-lv-reports.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Buat Report
            </a>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title font-weight-bold">Daftar Report Bus & LV</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Week</th>
                                <th>Periode</th>
                                <th>Judul</th>
                                <th>Bus Total</th>
                                <th>LV Total</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $index => $report)
                                @php
                                    $busTotal = collect($report->bus_co_daily ?? [])->sum() + collect($report->bus_ci_daily ?? [])->sum();
                                    $lvTotal = collect($report->lv_co_daily ?? [])->sum() + collect($report->lv_ci_daily ?? [])->sum();
                                @endphp
                                <tr>
                                    <td>{{ $reports->firstItem() + $index }}</td>
                                    <td><span class="badge badge-info">W{{ $report->week_number }}</span></td>
                                    <td>{{ $report->start_date?->format('d M Y') }} - {{ $report->end_date?->format('d M Y') }}</td>
                                    <td>{{ $report->title }}</td>
                                    <td>{{ $busTotal }}</td>
                                    <td>{{ $lvTotal }}</td>
                                    <td>{{ $report->created_at?->format('d M Y H:i') }}</td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('fleet.bus-lv-reports.show', $report) }}" class="btn btn-sm btn-primary"><i class="fas fa-chart-bar"></i></a>
                                        <a href="{{ route('fleet.bus-lv-reports.edit', $report) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <form method="POST" action="{{ route('fleet.bus-lv-reports.destroy', $report) }}" class="d-inline" onsubmit="return confirm('Hapus report week {{ $report->week_number }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Belum ada report Bus & LV.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">{{ $reports->links() }}</div>
        </div>
    </div>
</section>
@endsection
