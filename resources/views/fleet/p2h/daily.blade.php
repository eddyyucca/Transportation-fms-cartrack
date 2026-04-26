@extends('layouts.fleet-adminlte', ['title' => 'P2H Daily Checklist', 'selectedDate' => $selectedDate])

@section('content')
<div class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="page-title">P2H Daily Checklist</div>
            <div class="text-muted">Tabel data harian hasil upload dan input manual. Data di sini bisa dikoreksi/edit manual.</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('fleet.p2h.dashboard') }}" class="btn btn-outline-secondary"><i class="fas fa-chart-bar mr-1"></i>Dashboard</a>
            <a href="{{ route('fleet.p2h.units.index') }}" class="btn btn-outline-primary"><i class="fas fa-cog mr-1"></i>Master Data</a>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        @if($errors->any())
            <div class="alert alert-danger border-0">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-info border-0">
            Hanya unit yang ada di master dan memiliki `department` valid yang akan diproses sebagai data P2H. Unit yang tidak ada di master atau unit spare dengan department kosong / `Unassigned` akan otomatis di-skip saat upload.
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="mini-kpi">
                    <div class="value">{{ number_format($summary['total_checklists']) }}</div>
                    <div class="label">Total Checklist</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="mini-kpi">
                    <div class="value">{{ number_format($summary['today_checklists']) }}</div>
                    <div class="label">Checklist Hari Ini</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="mini-kpi">
                    <div class="value">{{ number_format($summary['upload_count']) }}</div>
                    <div class="label">Data dari Upload</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="mini-kpi">
                    <div class="value">{{ number_format($summary['manual_count']) }}</div>
                    <div class="label">Data Manual</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header border-0 bg-white">
                        <h3 class="card-title font-weight-bold">Upload File Checklist</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('fleet.p2h.upload') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="font-weight-bold">Excel Checklist</label>
                                <input type="file" name="checklist_file" class="form-control-file" accept=".xls,.xlsx" required>
                                <small class="text-muted">Gunakan file export daily checklist.</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-upload mr-1"></i>Upload Checklist</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header border-0 bg-white">
                        <h3 class="card-title font-weight-bold">{{ $editChecklist ? 'Edit Checklist Manual' : 'Input Manual Checklist' }}</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ $editChecklist ? route('fleet.p2h.daily.update', $editChecklist) : route('fleet.p2h.manual.store') }}">
                            @csrf
                            @if($editChecklist)
                                @method('PUT')
                            @endif
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Unit</label>
                                    <select name="p2h_unit_id" class="form-control" required>
                                        <option value="">Pilih unit</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" @selected(old('p2h_unit_id', $editChecklist?->p2h_unit_id) == $unit->id)>{{ $unit->unit_code }} - {{ $unit->department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="font-weight-bold">Tanggal</label>
                                    <input type="date" name="checklist_date" class="form-control" value="{{ old('checklist_date', optional($editChecklist?->checklist_date)->format('Y-m-d') ?: now()->toDateString()) }}" required>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="font-weight-bold">Kilometer</label>
                                    <input type="number" name="kilometer" class="form-control" min="0" value="{{ old('kilometer', $editChecklist?->kilometer) }}">
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="font-weight-bold">Safe</label>
                                    <select name="safe_to_use" class="form-control" required>
                                        <option value="1" @selected((string) old('safe_to_use', $editChecklist?->safe_to_use ?? '1') === '1')>Yes</option>
                                        <option value="0" @selected((string) old('safe_to_use', $editChecklist?->safe_to_use ?? '1') === '0')>No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="font-weight-bold">Maintenance</label>
                                    <select name="maintenance_required" class="form-control" required>
                                        <option value="0" @selected((string) old('maintenance_required', $editChecklist?->maintenance_required ?? '0') === '0')>No</option>
                                        <option value="1" @selected((string) old('maintenance_required', $editChecklist?->maintenance_required ?? '0') === '1')>Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row align-items-end">
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Created By</label>
                                    <input type="text" name="created_by_name" class="form-control" value="{{ old('created_by_name', $editChecklist?->created_by_name) }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <button type="submit" class="btn btn-outline-primary btn-block"><i class="fas fa-save mr-1"></i>{{ $editChecklist ? 'Update Checklist' : 'Simpan Manual' }}</button>
                                    @if($editChecklist)
                                        <a href="{{ route('fleet.p2h.daily.index') }}" class="btn btn-link btn-block">Batal edit</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('fleet.p2h.daily.index') }}" class="form-row align-items-end">
                    <div class="col-md-3">
                        <label class="font-weight-bold">Week</label>
                        <input type="number" name="week" class="form-control" min="1" max="53" value="{{ request('week') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="font-weight-bold">Year</label>
                        <input type="number" name="year" class="form-control" min="2024" max="2100" value="{{ request('year') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="font-weight-bold">Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold">Unit</label>
                        <select name="unit_id" class="form-control">
                            <option value="">Semua unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" @selected((string) request('unit_id') === (string) $unit->id)>{{ $unit->unit_code }} - {{ $unit->department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="font-weight-bold">Sumber</label>
                        <select name="source_type" class="form-control">
                            <option value="">Semua</option>
                            <option value="upload" @selected(request('source_type') === 'upload')>Upload</option>
                            <option value="manual" @selected(request('source_type') === 'manual')>Manual</option>
                        </select>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i>Filter</button>
                        <a href="{{ route('fleet.p2h.daily.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title font-weight-bold">Daftar Checklist Harian</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Unit</th>
                                <th>Department</th>
                                <th>KM</th>
                                <th>Safe</th>
                                <th>Maintenance</th>
                                <th>Input By</th>
                                <th>Sumber</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($checklists as $item)
                                <tr>
                                    <td>{{ $item->checklist_date?->format('d M Y') }}</td>
                                    <td class="font-weight-bold">{{ $item->unit?->unit_code }}</td>
                                    <td>{{ $item->unit?->department ?: '-' }}</td>
                                    <td>{{ $item->kilometer ? number_format($item->kilometer) : '-' }}</td>
                                    <td><span class="status-badge {{ $item->safe_to_use ? 'status-good' : 'status-critical' }}">{{ $item->safe_to_use ? 'Yes' : 'No' }}</span></td>
                                    <td><span class="status-badge {{ $item->maintenance_required ? 'status-monitor' : 'status-excellent' }}">{{ $item->maintenance_required ? 'Yes' : 'No' }}</span></td>
                                    <td>{{ $item->created_by_name ?: '-' }}</td>
                                    <td><span class="status-badge {{ $item->source_type === 'upload' ? 'status-good' : 'status-monitor' }}">{{ ucfirst($item->source_type) }}</span></td>
                                    <td><a href="{{ route('fleet.p2h.daily.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Belum ada checklist harian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">{{ $checklists->links() }}</div>
        </div>
    </div>
</section>
@endsection
