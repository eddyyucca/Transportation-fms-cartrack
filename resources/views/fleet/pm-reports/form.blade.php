@extends('layouts.fleet-adminlte', ['title' => ($report->exists ? 'Edit' : 'Buat') . ' Report PM Check', 'selectedDate' => now()->toDateString()])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-title">{{ $report->exists ? 'Edit Report PM Check' : 'Buat Report PM Check' }}</div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <form method="POST" action="{{ $report->exists ? route('fleet.pm-reports.update', $report) : route('fleet.pm-reports.store') }}">
            @csrf
            @if($report->exists)
                @method('PUT')
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header border-0 bg-white">
                    <h3 class="card-title font-weight-bold">Informasi Report</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold">Judul Report</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $report->title) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold">Nama Perusahaan</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $report->company_name) }}" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="font-weight-bold">Week</label>
                            <input type="number" name="week_number" min="1" max="53" class="form-control" value="{{ old('week_number', $report->week_number) }}" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="font-weight-bold">Tahun</label>
                            <input type="number" name="report_year" min="2024" max="2100" class="form-control" value="{{ old('report_year', $report->report_year) }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($report->start_date)->format('Y-m-d') ?: $report->start_date) }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', optional($report->end_date)->format('Y-m-d') ?: $report->end_date) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan">{{ old('notes', $report->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">Input Per Vendor</h3>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-vendor-row">
                        <i class="fas fa-plus mr-1"></i> Tambah Vendor
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info border-0 small">
                        Isi plan mingguan, total unit vendor, dan actual harian 7 hari. Nilai actual total akan dihitung otomatis dari data harian.
                    </div>

                    <div id="vendor-rows">
                        @foreach(old('vendors', $vendorRows) as $index => $vendor)
                            <div class="border rounded p-3 mb-4 vendor-row" data-index="{{ $index }}">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0 font-weight-bold">Vendor {{ $index + 1 }}</h5>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-vendor-row">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold">Nama Vendor</label>
                                        <input list="vendor-options" type="text" name="vendors[{{ $index }}][vendor_name]" class="form-control vendor-name" value="{{ $vendor['vendor_name'] ?? '' }}" required>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="font-weight-bold">Theme</label>
                                        <select name="vendors[{{ $index }}][theme]" class="form-control">
                                            @foreach(['bagong' => 'Bagong', 'transkon' => 'Transkon', 'trac' => 'Trac', 'default' => 'Default'] as $value => $label)
                                                <option value="{{ $value }}" {{ ($vendor['theme'] ?? 'default') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="font-weight-bold">Total Unit</label>
                                        <input type="number" min="0" name="vendors[{{ $index }}][total_units]" class="form-control total-units" value="{{ $vendor['total_units'] ?? 0 }}" required>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="font-weight-bold">Plan</label>
                                        <input type="number" min="0" name="vendors[{{ $index }}][plan_units]" class="form-control" value="{{ $vendor['plan_units'] ?? 0 }}" required>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="font-weight-bold">Actual Total</label>
                                        <input type="text" class="form-control actual-total" value="{{ array_sum($vendor['daily_actual'] ?? []) }}" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach(range(0, 6) as $dayIndex)
                                        <div class="col-md mb-3">
                                            <label class="font-weight-bold">Hari {{ $dayIndex + 1 }}</label>
                                            <input type="number" min="0" name="vendors[{{ $index }}][daily_actual][{{ $dayIndex }}]" class="form-control daily-input" value="{{ $vendor['daily_actual'][$dayIndex] ?? 0 }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <datalist id="vendor-options">
                        @foreach($vendorOptions as $vendorOption)
                            <option value="{{ $vendorOption->vendor }}">{{ $vendorOption->vendor }} ({{ $vendorOption->total_units }} unit)</option>
                        @endforeach
                    </datalist>
                </div>
                <div class="card-footer bg-white">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Simpan Report
                    </button>
                    <a href="{{ route('fleet.pm-reports.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
const vendorUnitLookup = @json($vendorOptions->mapWithKeys(fn($vendor) => [strtolower($vendor->vendor) => (int) $vendor->total_units]));

function attachRowEvents(row) {
    row.querySelectorAll('.daily-input').forEach((input) => {
        input.addEventListener('input', () => updateActualTotal(row));
    });

    const vendorNameInput = row.querySelector('.vendor-name');
    vendorNameInput?.addEventListener('change', () => {
        const key = vendorNameInput.value.trim().toLowerCase();
        const totalUnits = row.querySelector('.total-units');
        if (totalUnits && vendorUnitLookup[key] !== undefined && (!totalUnits.value || Number(totalUnits.value) === 0)) {
            totalUnits.value = vendorUnitLookup[key];
        }
    });

    row.querySelector('.remove-vendor-row')?.addEventListener('click', () => {
        if (document.querySelectorAll('.vendor-row').length > 1) {
            row.remove();
            refreshVendorTitles();
        }
    });

    updateActualTotal(row);
}

function updateActualTotal(row) {
    let total = 0;
    row.querySelectorAll('.daily-input').forEach((input) => {
        total += Number(input.value || 0);
    });
    const actualTotal = row.querySelector('.actual-total');
    if (actualTotal) {
        actualTotal.value = total;
    }
}

function refreshVendorTitles() {
    document.querySelectorAll('.vendor-row').forEach((row, index) => {
        row.dataset.index = index;
        const title = row.querySelector('h5');
        if (title) {
            title.textContent = 'Vendor ' + (index + 1);
        }
        row.querySelectorAll('input, select, textarea').forEach((field) => {
            if (field.name) {
                field.name = field.name.replace(/vendors\[\d+\]/, `vendors[${index}]`);
            }
        });
    });
}

document.querySelectorAll('.vendor-row').forEach(attachRowEvents);

document.getElementById('add-vendor-row')?.addEventListener('click', () => {
    const wrapper = document.getElementById('vendor-rows');
    const index = wrapper.querySelectorAll('.vendor-row').length;
    const row = document.createElement('div');
    row.className = 'border rounded p-3 mb-4 vendor-row';
    row.dataset.index = index;
    row.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 font-weight-bold">Vendor ${index + 1}</h5>
            <button type="button" class="btn btn-sm btn-outline-danger remove-vendor-row">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="font-weight-bold">Nama Vendor</label>
                <input list="vendor-options" type="text" name="vendors[${index}][vendor_name]" class="form-control vendor-name" required>
            </div>
            <div class="col-md-2 mb-3">
                <label class="font-weight-bold">Theme</label>
                <select name="vendors[${index}][theme]" class="form-control">
                    <option value="bagong">Bagong</option>
                    <option value="transkon">Transkon</option>
                    <option value="trac">Trac</option>
                    <option value="default" selected>Default</option>
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label class="font-weight-bold">Total Unit</label>
                <input type="number" min="0" name="vendors[${index}][total_units]" class="form-control total-units" value="0" required>
            </div>
            <div class="col-md-2 mb-3">
                <label class="font-weight-bold">Plan</label>
                <input type="number" min="0" name="vendors[${index}][plan_units]" class="form-control" value="0" required>
            </div>
            <div class="col-md-2 mb-3">
                <label class="font-weight-bold">Actual Total</label>
                <input type="text" class="form-control actual-total" value="0" readonly>
            </div>
        </div>
        <div class="row">
            ${Array.from({length: 7}).map((_, dayIndex) => `
                <div class="col-md mb-3">
                    <label class="font-weight-bold">Hari ${dayIndex + 1}</label>
                    <input type="number" min="0" name="vendors[${index}][daily_actual][${dayIndex}]" class="form-control daily-input" value="0">
                </div>
            `).join('')}
        </div>
    `;
    wrapper.appendChild(row);
    attachRowEvents(row);
});
</script>
@endpush
