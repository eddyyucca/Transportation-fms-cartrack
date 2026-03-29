@extends('layouts.fleet-adminlte', ['title' => ($schedule ? 'Edit' : 'Tambah') . ' Jadwal PM', 'selectedDate' => now()->toDateString()])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-title">{{ $schedule ? 'Edit Jadwal PM' : 'Tambah Jadwal PM' }}</div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="card" style="max-width: 640px;">
            <div class="card-body">
                <form method="POST" action="{{ $schedule ? route('fleet.pm.update', $schedule) : route('fleet.pm.store') }}">
                    @csrf
                    @if($schedule) @method('PUT') @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-lg">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold">Unit <span class="text-danger">*</span></label>
                            <select name="unit_code" class="form-control" required>
                                <option value="">-- Pilih Unit --</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->unit_code }}"
                                        {{ old('unit_code', $schedule?->unit_code) == $u->unit_code ? 'selected' : '' }}>
                                        {{ $u->unit_code }} — {{ $u->type_model }} ({{ $u->vendor }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Tanggal Jadwal PM <span class="text-danger">*</span></label>
                            <input type="date" name="scheduled_date" class="form-control"
                                   value="{{ old('scheduled_date', $schedule?->scheduled_date?->format('Y-m-d')) }}" required>
                        </div>
                        @if($schedule)
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Status</label>
                            <select name="status" class="form-control">
                                <option value="scheduled" {{ $schedule->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="done" {{ $schedule->status === 'done' ? 'selected' : '' }}>Done</option>
                                <option value="overdue" {{ $schedule->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Tanggal Selesai</label>
                            <input type="date" name="completed_date" class="form-control"
                                   value="{{ old('completed_date', $schedule?->completed_date?->format('Y-m-d')) }}">
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Nama PIC <span class="text-danger">*</span></label>
                            <input type="text" name="pic_name" class="form-control"
                                   value="{{ old('pic_name', $schedule?->pic_name) }}" placeholder="Nama PIC" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">No HP PIC <span class="text-danger">*</span></label>
                            <input type="text" name="pic_phone" class="form-control"
                                   value="{{ old('pic_phone', $schedule?->pic_phone ?? '081250653005') }}"
                                   placeholder="081250653005" required>
                            <small class="text-muted">Notifikasi WA akan dikirim ke nomor ini</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan PM...">{{ old('notes', $schedule?->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <a href="{{ route('fleet.pm.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
