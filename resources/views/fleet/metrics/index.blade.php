@extends('layouts.fleet-adminlte', ['title' => 'Data Harian Unit', 'selectedDate' => $dateTo])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="page-title">Data Harian Unit</div>
                <div class="text-muted">Idle, HM, Jarak KM, UA, Standby, dan Fuel Consumption per unit per hari.</div>
            </div>
            <a href="{{ route('fleet.metrics.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Input Data
            </a>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="card filter-card mb-4">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Sampai Tanggal</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Unit</label>
                        <select name="unit_code" class="form-control">
                            <option value="">Semua Unit</option>
                            @foreach($units as $u)
                                <option value="{{ $u }}" {{ request('unit_code') == $u ? 'selected' : '' }}>{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 d-flex gap-2">
                        <button class="btn btn-primary flex-fill"><i class="fas fa-search mr-1"></i> Tampilkan</button>
                        <a href="{{ route('fleet.metrics.export', request()->query()) }}" class="btn btn-outline-success flex-fill">
                            <i class="fas fa-file-csv mr-1"></i> Export
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title font-weight-bold">Data Metrik ({{ $metrics->total() }} record)</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-nowrap">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Unit Code</th>
                                <th>Vendor</th>
                                <th>Idle (Jam)</th>
                                <th>HM Awal</th>
                                <th>HM Akhir</th>
                                <th>HM Usage</th>
                                <th>Jarak (KM)</th>
                                <th>UA (%)</th>
                                <th>Standby (Jam)</th>
                                <th>Fuel (Liter)</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metrics as $m)
                            <tr>
                                <td>{{ $m->report_date?->format('Y-m-d') }}</td>
                                <td><strong>{{ $m->unit_code }}</strong></td>
                                <td>{{ $m->unit?->vendor ?? '-' }}</td>
                                <td>{{ number_format($m->idle_hours, 2) }}</td>
                                <td>{{ $m->hm_start !== null ? number_format($m->hm_start, 1) : '-' }}</td>
                                <td>{{ $m->hm_end !== null ? number_format($m->hm_end, 1) : '-' }}</td>
                                <td>
                                    @if($m->hm_usage !== null)
                                        <span class="badge badge-info">{{ number_format($m->hm_usage, 1) }} HM</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ number_format($m->distance_km, 2) }}</td>
                                <td>
                                    @php $ua = (float)$m->ua_percent; @endphp
                                    <span class="{{ $ua >= 85 ? 'text-success' : ($ua >= 70 ? 'text-warning' : 'text-danger') }} font-weight-bold">
                                        {{ number_format($ua, 1) }}%
                                    </span>
                                </td>
                                <td>{{ number_format($m->standby_hours, 2) }}</td>
                                <td>{{ number_format($m->fuel_consumption, 2) }}</td>
                                <td>{{ $m->notes ? \Str::limit($m->notes, 30) : '-' }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('fleet.metrics.edit', $m) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{ route('fleet.metrics.destroy', $m) }}" class="d-inline"
                                          onsubmit="return confirm('Hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted py-4">
                                    Tidak ada data untuk filter ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                {{ $metrics->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
