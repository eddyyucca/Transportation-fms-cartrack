@extends('layouts.fleet-adminlte', ['title' => ($unit ? 'Edit' : 'Tambah') . ' Unit', 'selectedDate' => now()->toDateString()])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-title">{{ $unit ? 'Edit Unit: ' . $unit->unit_code : 'Tambah Unit Baru' }}</div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="card" style="max-width: 700px;">
            <div class="card-body">
                <form method="POST" action="{{ $unit ? route('fleet.units.update', $unit) : route('fleet.units.store') }}">
                    @csrf
                    @if($unit) @method('PUT') @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Unit Code <span class="text-danger">*</span></label>
                            <input type="text" name="unit_code" class="form-control" value="{{ old('unit_code', $unit?->unit_code) }}" placeholder="SCM LV 01" required {{ $unit ? 'readonly' : '' }}>
                            <small class="text-muted">Kode unik unit (tidak bisa diubah setelah dibuat)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Registration (Cartrack)</label>
                            <input type="text" name="registration" class="form-control" value="{{ old('registration', $unit?->registration) }}" placeholder="Nomor polisi / ID di Cartrack">
                            <small class="text-muted">Digunakan untuk mapping data Cartrack</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Vendor</label>
                            <input type="text" name="vendor" class="form-control" value="{{ old('vendor', $unit?->vendor) }}" placeholder="Trac / Bagong / SCM">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Merek</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand', $unit?->brand) }}" placeholder="Toyota Hilux">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Tipe Model</label>
                            <input type="text" name="type_model" class="form-control" value="{{ old('type_model', $unit?->type_model) }}" placeholder="Double Cabin / BUS Medium">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Departemen</label>
                            <input type="text" name="department" class="form-control" value="{{ old('department', $unit?->department) }}" placeholder="Mine Operation">
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_monitored" value="0">
                                <input type="checkbox" class="custom-control-input" id="is_monitored" name="is_monitored" value="1"
                                    {{ old('is_monitored', $unit?->is_monitored ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="is_monitored">
                                    Aktifkan Monitoring
                                </label>
                                <div class="text-muted small">Unit ini akan muncul di data harian dan PM schedule.</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <a href="{{ route('fleet.units.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
