@extends('layouts.fleet-adminlte', ['title' => 'Master Unit', 'selectedDate' => now()->toDateString()])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="page-title">Master Unit</div>
                <div class="text-muted">Kelola daftar unit yang dimonitor dari data Cartrack.</div>
            </div>
            <a href="{{ route('fleet.units.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Tambah Unit
            </a>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="card filter-card mb-4">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Cari Unit / Vendor / Departemen</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="SCM LV 01 / Trac ...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Vendor</label>
                        <select name="vendor" class="form-control">
                            <option value="">Semua Vendor</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v }}" {{ request('vendor') == $v ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Status Monitor</label>
                        <select name="monitored" class="form-control">
                            <option value="">Semua</option>
                            <option value="1" {{ request('monitored') === '1' ? 'selected' : '' }}>Dimonitor</option>
                            <option value="0" {{ request('monitored') === '0' ? 'selected' : '' }}>Tidak Dimonitor</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">Daftar Unit ({{ $units->total() }})</h3>
                <span class="text-muted small">Unit dimonitor = akan muncul di data harian & PM</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Unit Code</th>
                                <th>Vendor</th>
                                <th>Departemen</th>
                                <th>Merek</th>
                                <th>Tipe</th>
                                <th>Registration (Cartrack)</th>
                                <th>Monitor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($units as $i => $unit)
                            <tr>
                                <td>{{ $units->firstItem() + $i }}</td>
                                <td><strong>{{ $unit->unit_code }}</strong></td>
                                <td>{{ $unit->vendor ?? '-' }}</td>
                                <td>{{ $unit->department ?? '-' }}</td>
                                <td>{{ $unit->brand ?? '-' }}</td>
                                <td>{{ $unit->type_model ?? '-' }}</td>
                                <td>
                                    @if($unit->registration)
                                        <span class="badge badge-info">{{ $unit->registration }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('fleet.units.toggle', $unit) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $unit->is_monitored ? 'btn-success' : 'btn-secondary' }}">
                                            <i class="fas fa-{{ $unit->is_monitored ? 'eye' : 'eye-slash' }}"></i>
                                            {{ $unit->is_monitored ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('fleet.units.edit', $unit) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('fleet.units.destroy', $unit) }}" class="d-inline"
                                          onsubmit="return confirm('Hapus unit {{ $unit->unit_code }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Tidak ada unit ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                {{ $units->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
