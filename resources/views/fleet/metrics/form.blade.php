@extends('layouts.fleet-adminlte', ['title' => ($metric ? 'Edit' : 'Input') . ' Data Harian', 'selectedDate' => now()->toDateString()])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-title">{{ $metric ? 'Edit Data Harian' : 'Input Data Harian Unit' }}</div>
        <div class="text-muted">Idle, HM, Jarak, UA, Standby, Fuel Consumption</div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="card" style="max-width: 780px;">
            <div class="card-body">
                <form method="POST" action="{{ $metric ? route('fleet.metrics.update', $metric) : route('fleet.metrics.store') }}">
                    @csrf
                    @if($metric) @method('PUT') @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-lg">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Unit <span class="text-danger">*</span></label>
                            <select name="unit_code" class="form-control" required>
                                <option value="">-- Pilih Unit --</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->unit_code }}"
                                        {{ old('unit_code', $metric?->unit_code) == $u->unit_code ? 'selected' : '' }}>
                                        {{ $u->unit_code }} — {{ $u->type_model }} ({{ $u->vendor }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Tanggal Laporan <span class="text-danger">*</span></label>
                            <input type="date" name="report_date" class="form-control"
                                   value="{{ old('report_date', $metric?->report_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                        </div>

                        <div class="col-12"><hr><h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-clock mr-1"></i> Waktu Operasi</h6></div>

                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold">Idle (Jam) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="idle_hours" class="form-control"
                                   value="{{ old('idle_hours', $metric?->idle_hours ?? 0) }}" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold">Standby (Jam) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="standby_hours" class="form-control"
                                   value="{{ old('standby_hours', $metric?->standby_hours ?? 0) }}" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold">UA % <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="ua_percent" class="form-control"
                                   value="{{ old('ua_percent', $metric?->ua_percent ?? 0) }}" min="0" max="100" required>
                            <small class="text-muted">Unit Availability 0–100%</small>
                        </div>

                        <div class="col-12"><hr><h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-tachometer-alt mr-1"></i> Hour Meter (HM) — isi jika ada HM</h6></div>

                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold">HM Awal</label>
                            <input type="number" step="0.1" name="hm_start" class="form-control"
                                   value="{{ old('hm_start', $metric?->hm_start) }}" min="0" placeholder="Opsional">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold">HM Akhir</label>
                            <input type="number" step="0.1" name="hm_end" class="form-control"
                                   value="{{ old('hm_end', $metric?->hm_end) }}" min="0" placeholder="Opsional">
                            <small class="text-muted">HM Usage akan dihitung otomatis</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold">HM Usage (auto)</label>
                            <input type="text" class="form-control bg-light" readonly
                                   value="{{ $metric?->hm_usage !== null ? number_format($metric->hm_usage, 1) . ' HM' : 'Dihitung otomatis' }}"
                                   id="hm_usage_display">
                        </div>

                        <div class="col-12"><hr><h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-road mr-1"></i> Jarak & Bahan Bakar</h6></div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Jarak (KM) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="distance_km" class="form-control"
                                   value="{{ old('distance_km', $metric?->distance_km ?? 0) }}" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Fuel Consumption (Liter) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="fuel_consumption" class="form-control"
                                   value="{{ old('fuel_consumption', $metric?->fuel_consumption ?? 0) }}" min="0" required>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="font-weight-bold">Catatan</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan opsional">{{ old('notes', $metric?->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <a href="{{ route('fleet.metrics.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
const hmStart = document.querySelector('[name="hm_start"]');
const hmEnd   = document.querySelector('[name="hm_end"]');
const display = document.getElementById('hm_usage_display');

function updateHmUsage() {
    const s = parseFloat(hmStart.value);
    const e = parseFloat(hmEnd.value);
    if (!isNaN(s) && !isNaN(e) && e >= s) {
        display.value = (e - s).toFixed(1) + ' HM';
    } else {
        display.value = 'Dihitung otomatis';
    }
}

hmStart.addEventListener('input', updateHmUsage);
hmEnd.addEventListener('input', updateHmUsage);
</script>
@endpush
