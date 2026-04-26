@extends('layouts.fleet-adminlte', ['title' => 'PM Schedule', 'selectedDate' => now()->toDateString()])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="page-title">PM Schedule <span class="badge badge-danger ml-2" style="font-size:.7em">{{ $overdueCount }} Overdue</span></div>
                <div class="text-muted">Jadwal preventive maintenance bi-weekly dengan notifikasi WhatsApp.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('fleet.pm-reports.index') }}" class="btn btn-outline-dark">
                    <i class="fas fa-clipboard-list mr-1"></i> Report PM Check
                </a>
                @if($overdueCount > 0)
                <form method="POST" action="{{ route('fleet.pm.overdue-notif') }}">
                    @csrf
                    <button class="btn btn-danger" onclick="return confirm('Kirim WA ke semua unit overdue PM?')">
                        <i class="fas fa-bell mr-1"></i> Kirim WA Overdue ({{ $overdueCount }})
                    </button>
                </form>
                @endif
                <button class="btn btn-warning" data-toggle="modal" data-target="#modalBiweekly">
                    <i class="fas fa-calendar-plus mr-1"></i> Generate Bi-Weekly
                </button>
                <a href="{{ route('fleet.pm.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Tambah Jadwal
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">

        {{-- Filter --}}
        <div class="card filter-card mb-4">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Unit</label>
                        <select name="unit_code" class="form-control">
                            <option value="">Semua Unit</option>
                            @foreach($units as $u)
                                <option value="{{ $u }}" {{ request('unit_code') == $u ? 'selected' : '' }}>{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Filter</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title font-weight-bold">Daftar Jadwal PM ({{ $schedules->total() }})</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Unit Code</th>
                                <th>Vendor / Tipe</th>
                                <th>Jadwal PM</th>
                                <th>Selesai</th>
                                <th>Status</th>
                                <th>PIC</th>
                                <th>No HP</th>
                                <th>WA Terkirim</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $i => $pm)
                            <tr class="{{ $pm->status === 'overdue' ? 'table-danger' : '' }}">
                                <td>{{ $schedules->firstItem() + $i }}</td>
                                <td><strong>{{ $pm->unit_code }}</strong></td>
                                <td>
                                    <span class="text-muted small">{{ $pm->unit?->vendor }} — {{ $pm->unit?->type_model }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($pm->scheduled_date)->format('d M Y') }}</td>
                                <td>{{ $pm->completed_date ? \Carbon\Carbon::parse($pm->completed_date)->format('d M Y') : '-' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $pm->status }}">
                                        @if($pm->status === 'overdue') 🚨 @elseif($pm->status === 'done') ✅ @else 🔔 @endif
                                        {{ ucfirst($pm->status) }}
                                    </span>
                                </td>
                                <td>{{ $pm->pic_name ?? '-' }}</td>
                                <td>{{ $pm->pic_phone ?? '-' }}</td>
                                <td>
                                    @if($pm->notif_sent)
                                        <span class="text-success small"><i class="fas fa-check-circle"></i> {{ $pm->notif_sent_at?->format('d/m H:i') }}</span>
                                    @else
                                        <span class="text-muted small">Belum</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        @if($pm->status !== 'done')
                                        <form method="POST" action="{{ route('fleet.pm.done', $pm) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success" title="Tandai Selesai">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('fleet.pm.notif', $pm) }}" class="d-inline"
                                              onsubmit="return confirm('Kirim WA ke {{ $pm->pic_phone }}?')">
                                            @csrf
                                            <button class="btn btn-sm btn-info" title="Kirim WA">
                                                <i class="fab fa-whatsapp"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <a href="{{ route('fleet.pm.edit', $pm) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('fleet.pm.destroy', $pm) }}" class="d-inline"
                                              onsubmit="return confirm('Hapus jadwal ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Tidak ada jadwal PM ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                {{ $schedules->links() }}
            </div>
        </div>
    </div>
</section>

{{-- Modal Generate Bi-Weekly --}}
<div class="modal fade" id="modalBiweekly" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-calendar-plus mr-2"></i>Generate Jadwal PM Bi-Weekly</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('fleet.pm.generate') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info border-0 rounded-lg small">
                        Sistem akan membuat 6 jadwal PM (setiap 2 minggu = ±3 bulan ke depan) untuk unit yang dipilih.
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Pilih Unit <span class="text-danger">*</span></label>
                        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: .75rem;">
                            <div class="mb-2">
                                <a href="#" onclick="toggleAll(true)">Pilih Semua</a> |
                                <a href="#" onclick="toggleAll(false)">Batal Semua</a>
                            </div>
                            @foreach($units as $u)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input unit-cb" id="u_{{ $loop->index }}" name="unit_codes[]" value="{{ $u }}">
                                <label class="custom-control-label" for="u_{{ $loop->index }}">{{ $u }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="font-weight-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold">Nama PIC <span class="text-danger">*</span></label>
                            <input type="text" name="pic_name" class="form-control" placeholder="Nama PIC" required>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold">No HP PIC <span class="text-danger">*</span></label>
                            <input type="text" name="pic_phone" class="form-control" value="081250653005" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-calendar-plus mr-1"></i> Generate Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleAll(state) {
    document.querySelectorAll('.unit-cb').forEach(cb => cb.checked = state);
    return false;
}
</script>
@endpush
