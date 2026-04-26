@extends('layouts.fleet-adminlte', ['title' => ($report->exists ? 'Edit' : 'Buat') . ' Bus & LV Report', 'selectedDate' => now()->toDateString()])

@php
    $dayLabels = old('start_date')
        ? collect(range(0, 6))->map(fn ($i) => \Carbon\Carbon::parse(old('start_date'))->addDays($i)->format('d M'))->all()
        : $dayLabels;
@endphp

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-title">{{ $report->exists ? 'Edit' : 'Buat' }} Report Bus & LV</div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <form method="POST" action="{{ $report->exists ? route('fleet.bus-lv-reports.update', $report) : route('fleet.bus-lv-reports.store') }}">
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
                    <h3 class="card-title font-weight-bold">Informasi Mingguan</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold">Judul</label>
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
                            <input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', optional($report->start_date)->format('Y-m-d') ?: $report->start_date) }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold">Tanggal Selesai</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" value="{{ old('end_date', optional($report->end_date)->format('Y-m-d') ?: $report->end_date) }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold">Default Total Kapasitas Bus</label>
                            <div class="input-group">
                                <input type="number" id="default_bus_capacity" class="form-control" value="53" min="0">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-primary" onclick="fillRow('bus_capacity_daily', 'default_bus_capacity')">Isi 7 Hari</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold">Default Total Kapasitas LV</label>
                            <div class="input-group">
                                <input type="number" id="default_lv_capacity" class="form-control" value="4" min="0">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-primary" onclick="fillRow('lv_capacity_daily', 'default_lv_capacity')">Isi 7 Hari</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info border-0 mb-0">
                        `Kapasitas` diisi sebagai total kapasitas harian dari kombinasi beberapa bus/LV. `Extra Bus` boleh lebih dari 1 jika ada tambahan 2-3 unit atau lebih.
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header border-0 bg-white">
                    <h3 class="card-title font-weight-bold">Input Bus SCM</h3>
                </div>
                <div class="card-body p-0">
                    @include('fleet.bus-lv-reports.partials.week-table', [
                        'title' => 'Bus SCM - Departure & Arrival',
                        'labels' => $dayLabels,
                        'rows' => [
                            ['key' => 'bus_unit_count_daily', 'label' => 'Jumlah Bus Reguler', 'values' => old('bus_unit_count_daily', $report->bus_unit_count_daily ?? [2,2,2,2,2,2,2]), 'sum' => true, 'avg' => true],
                            ['key' => 'bus_co_daily', 'label' => 'CO (Departure)', 'values' => old('bus_co_daily', $report->bus_co_daily ?? [0,0,0,0,0,0,0]), 'sum' => true, 'avg' => true],
                            ['key' => 'bus_ci_daily', 'label' => 'CI (Arrival)', 'values' => old('bus_ci_daily', $report->bus_ci_daily ?? [0,0,0,0,0,0,0]), 'sum' => true, 'avg' => true],
                            ['key' => 'bus_capacity_daily', 'label' => 'Total Kapasitas Bus', 'values' => old('bus_capacity_daily', $report->bus_capacity_daily ?? [53,53,53,53,53,53,53]), 'sum' => true, 'avg' => true],
                            ['key' => 'bus_extra_daily', 'label' => 'Extra Bus (Unit)', 'values' => old('bus_extra_daily', $report->bus_extra_daily ?? [0,0,0,0,0,0,0]), 'sum' => true, 'avg' => false],
                        ],
                    ])
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header border-0 bg-white">
                    <h3 class="card-title font-weight-bold">Input LV SCM</h3>
                </div>
                <div class="card-body p-0">
                    @include('fleet.bus-lv-reports.partials.week-table', [
                        'title' => 'LV SCM - Data Parsial',
                        'labels' => $dayLabels,
                        'rows' => [
                            ['key' => 'lv_unit_count_daily', 'label' => 'Jumlah LV Aktif', 'values' => old('lv_unit_count_daily', $report->lv_unit_count_daily ?? [1,1,1,1,1,1,1]), 'sum' => true, 'avg' => true],
                            ['key' => 'lv_co_daily', 'label' => 'CO (LV)', 'values' => old('lv_co_daily', $report->lv_co_daily ?? [0,0,0,0,0,0,0]), 'sum' => true, 'avg' => true],
                            ['key' => 'lv_ci_daily', 'label' => 'CI (LV)', 'values' => old('lv_ci_daily', $report->lv_ci_daily ?? [0,0,0,0,0,0,0]), 'sum' => true, 'avg' => true],
                            ['key' => 'lv_capacity_daily', 'label' => 'Total Kapasitas LV', 'values' => old('lv_capacity_daily', $report->lv_capacity_daily ?? [4,4,4,4,4,4,4]), 'sum' => true, 'avg' => true],
                        ],
                    ])
                </div>
            </div>

            <div class="card">
                <div class="card-header border-0 bg-white">
                    <h3 class="card-title font-weight-bold">Catatan Dashboard</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach(range(0, 3) as $noteIndex)
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Catatan {{ $noteIndex + 1 }}</label>
                                <input type="text" name="notes[]" class="form-control" value="{{ old('notes.' . $noteIndex, ($report->notes[$noteIndex] ?? '')) }}" placeholder="Isi ringkasan / insight">
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i>Simpan Report</button>
                    <a href="{{ route('fleet.bus-lv-reports.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
function updateRowTotals(rowKey) {
    let total = 0;
    document.querySelectorAll(`[data-row="${rowKey}"]`).forEach((input) => {
        total += Number(input.value || 0);
    });
    const totalTarget = document.querySelector(`[data-total="${rowKey}"]`);
    if (totalTarget) totalTarget.textContent = total;
    const avgTarget = document.querySelector(`[data-avg="${rowKey}"]`);
    if (avgTarget) avgTarget.textContent = (total / 7).toFixed(1);
}

function bindRowInputs() {
    document.querySelectorAll('[data-row]').forEach((input) => {
        input.addEventListener('input', () => updateRowTotals(input.dataset.row));
    });
    document.querySelectorAll('[data-row-name]').forEach((el) => updateRowTotals(el.dataset.rowName));
}

function fillRow(rowKey, sourceId) {
    const value = Number(document.getElementById(sourceId)?.value || 0);
    document.querySelectorAll(`[data-row="${rowKey}"]`).forEach((input) => input.value = value);
    updateRowTotals(rowKey);
}

function updateDayLabels() {
    const startDate = document.getElementById('start_date')?.value;
    if (!startDate) return;
    const base = new Date(startDate + 'T00:00:00');
    document.querySelectorAll('[data-day-index]').forEach((el) => {
        const idx = Number(el.dataset.dayIndex || 0);
        const date = new Date(base);
        date.setDate(base.getDate() + idx);
        el.textContent = date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
    });
    const endDate = document.getElementById('end_date');
    if (endDate) {
        const date = new Date(base);
        date.setDate(base.getDate() + 6);
        endDate.value = date.toISOString().slice(0, 10);
    }
}

document.getElementById('start_date')?.addEventListener('change', updateDayLabels);
bindRowInputs();
updateDayLabels();
</script>
@endpush
