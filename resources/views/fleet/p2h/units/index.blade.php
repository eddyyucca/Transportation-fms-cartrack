@extends('layouts.fleet-adminlte', ['title' => 'Master Unit P2H', 'selectedDate' => now()->toDateString()])

@section('content')
<div class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="page-title">Master Unit P2H</div>
            <div class="text-muted">Pusat data dan setting unit P2H untuk dashboard dan checklist harian.</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form method="POST" action="{{ route('fleet.p2h.import-master') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-success"><i class="fas fa-file-import mr-1"></i>Import Master</button>
            </form>
            <a href="{{ route('fleet.p2h.daily.index') }}" class="btn btn-outline-secondary">Daily Checklist</a>
            <a href="{{ route('fleet.p2h.units.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i>Tambah Unit</a>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="mini-kpi">
                    <div class="value">{{ number_format($summary['total_units']) }}</div>
                    <div class="label">Total Unit Master</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="mini-kpi">
                    <div class="value">{{ number_format($summary['active_units']) }}</div>
                    <div class="label">Unit Aktif</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="mini-kpi">
                    <div class="value">{{ number_format($summary['departments']) }}</div>
                    <div class="label">Department</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="mini-kpi">
                    <div class="value">{{ number_format($summary['vendors']) }}</div>
                    <div class="label">Vendor</div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('fleet.p2h.units.index') }}" class="form-row align-items-end">
                    <div class="col-md-4">
                        <label class="font-weight-bold">Cari</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Unit code / dept / plate / vendor">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i>Cari</button>
                        <a href="{{ route('fleet.p2h.units.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title font-weight-bold">Daftar Unit</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vendor</th>
                                <th>Unit Code</th>
                                <th>Head</th>
                                <th>Department</th>
                                <th>Plate No</th>
                                <th>PIC</th>
                                <th>Model</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($units as $index => $unit)
                                <tr>
                                    <td>{{ $units->firstItem() + $index }}</td>
                                    <td>{{ $unit->vendor ?: '-' }}</td>
                                    <td class="font-weight-bold">{{ $unit->unit_code }}</td>
                                    <td>{{ $unit->head ?: '-' }}</td>
                                    <td>{{ $unit->department ?: '-' }}</td>
                                    <td>{{ $unit->plate_no ?: '-' }}</td>
                                    <td>{{ $unit->pic_name ?: '-' }}</td>
                                    <td>{{ $unit->model_name ?: '-' }}</td>
                                    <td><span class="status-badge {{ $unit->is_active ? 'status-excellent' : 'status-monitor' }}">{{ $unit->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td><a href="{{ route('fleet.p2h.units.edit', $unit) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">Belum ada master unit P2H.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">{{ $units->links() }}</div>
        </div>
    </div>
</section>
@endsection
