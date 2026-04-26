@extends('layouts.fleet-adminlte', ['title' => 'Report PM Check', 'selectedDate' => now()->toDateString()])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="page-title">Report PM Check</div>
                <div class="text-muted">CRUD report mingguan PM Check per vendor beserta dashboard hasil report.</div>
            </div>
            <a href="{{ route('fleet.pm-reports.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Buat Report
            </a>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title font-weight-bold">Daftar Report</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Periode</th>
                                <th>Week</th>
                                <th>Judul</th>
                                <th>Company</th>
                                <th>Vendor</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $index => $report)
                                <tr>
                                    <td>{{ $reports->firstItem() + $index }}</td>
                                    <td>{{ $report->start_date?->format('d M Y') }} - {{ $report->end_date?->format('d M Y') }}</td>
                                    <td><span class="badge badge-info">W{{ $report->week_number }}</span></td>
                                    <td>{{ $report->title }}</td>
                                    <td>{{ $report->company_name }}</td>
                                    <td>{{ $report->vendor_reports_count }}</td>
                                    <td>{{ $report->created_at?->format('d M Y H:i') }}</td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('fleet.pm-reports.show', $report) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-chart-pie"></i>
                                        </a>
                                        <a href="{{ route('fleet.pm-reports.edit', $report) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('fleet.pm-reports.destroy', $report) }}" class="d-inline" onsubmit="return confirm('Hapus report week {{ $report->week_number }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Belum ada report PM Check.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
