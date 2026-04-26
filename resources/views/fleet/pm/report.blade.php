@extends('layouts.fleet-adminlte', ['title' => 'Report PM Check', 'selectedDate' => $selectedDate])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="page-title">Report PM Check</div>
                <div class="text-muted">Struktur report mengikuti format Excel PM Check: ringkasan harian dan mingguan.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('fleet.pm.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-tools mr-1"></i> PM Schedule
                </a>
                <a href="{{ route('fleet.pm.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Tambah Jadwal
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <div class="card filter-card mb-4">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Periode Bulan</label>
                        <select name="month" class="form-control">
                            @foreach($monthOptions as $monthOption)
                                <option value="{{ $monthOption }}" {{ $selectedMonth === $monthOption ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $monthOption)->translatedFormat('F Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 mb-3 d-flex flex-wrap align-items-center">
                        <button class="btn btn-primary mr-2"><i class="fas fa-search mr-1"></i> Tampilkan Report</button>
                        <span class="text-muted">Periode aktif: <strong>{{ $periodLabel }}</strong></span>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-3 col-sm-6 mb-3">
                <div class="mini-kpi">
                    <div class="label">Total Target</div>
                    <div class="value">{{ number_format($summary->target) }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3">
                <div class="mini-kpi">
                    <div class="label">Total Actual</div>
                    <div class="value">{{ number_format($summary->actual) }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3">
                <div class="mini-kpi">
                    <div class="label">% Achievement</div>
                    <div class="value">{{ $summary->achievement !== null ? number_format($summary->achievement * 100, 1) . '%' : '-' }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3">
                <div class="mini-kpi">
                    <div class="label">Status Bulan Ini</div>
                    <div class="value">{{ $summary->done }} / {{ $summary->scheduled }} / {{ $summary->overdue }}</div>
                    <div class="text-muted small mt-1">Done / Scheduled / Overdue</div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title font-weight-bold">PM Check {{ $periodLabel }}</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 text-nowrap">
                        <tbody>
                            <tr class="bg-light">
                                <th class="font-weight-bold">PM Check</th>
                                @foreach($dailyStats as $day)
                                    <th class="text-center">{{ $day->label }}</th>
                                @endforeach
                                <th class="text-center">Total</th>
                                <th class="text-center">% Ach.</th>
                            </tr>
                            <tr>
                                <th>Target</th>
                                @foreach($dailyStats as $day)
                                    <td class="text-center">{{ $day->target }}</td>
                                @endforeach
                                <td class="text-center font-weight-bold">{{ $summary->target }}</td>
                                <td class="text-center font-weight-bold">100%</td>
                            </tr>
                            <tr>
                                <th>Actual</th>
                                @foreach($dailyStats as $day)
                                    <td class="text-center">{{ $day->actual }}</td>
                                @endforeach
                                <td class="text-center font-weight-bold">{{ $summary->actual }}</td>
                                <td class="text-center font-weight-bold">{{ $summary->achievement !== null ? number_format($summary->achievement * 100, 1) . '%' : '-' }}</td>
                            </tr>
                            <tr>
                                <th>% Ach.</th>
                                @foreach($dailyStats as $day)
                                    <td class="text-center">
                                        {{ $day->achievement !== null ? number_format($day->achievement * 100, 0) . '%' : '-' }}
                                    </td>
                                @endforeach
                                <td class="text-center font-weight-bold">{{ $summary->achievement !== null ? number_format($summary->achievement * 100, 1) . '%' : '-' }}</td>
                                <td class="text-center">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title font-weight-bold">Ringkasan Mingguan</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 text-nowrap">
                        <tbody>
                            <tr class="bg-light">
                                <th class="font-weight-bold">PM Check</th>
                                @foreach($weeklyStats as $week)
                                    <th class="text-center">{{ $week->week_label }}</th>
                                @endforeach
                                <th class="text-center">Avrg</th>
                            </tr>
                            <tr>
                                <th>Plan Unit</th>
                                @foreach($weeklyStats as $week)
                                    <td class="text-center">{{ $week->plan }}</td>
                                @endforeach
                                <td class="text-center font-weight-bold">{{ number_format(collect($weeklyStats)->avg('plan') ?? 0, 1) }}</td>
                            </tr>
                            <tr>
                                <th>Aktual</th>
                                @foreach($weeklyStats as $week)
                                    <td class="text-center">{{ $week->actual }}</td>
                                @endforeach
                                <td class="text-center font-weight-bold">{{ number_format(collect($weeklyStats)->avg('actual') ?? 0, 1) }}</td>
                            </tr>
                            <tr>
                                <th>Achive</th>
                                @foreach($weeklyStats as $week)
                                    <td class="text-center">{{ $week->achievement !== null ? number_format($week->achievement * 100, 1) . '%' : '-' }}</td>
                                @endforeach
                                <td class="text-center font-weight-bold">
                                    @php $avgAchievement = collect($weeklyStats)->filter(fn ($week) => $week->achievement !== null)->avg('achievement'); @endphp
                                    {{ $avgAchievement !== null ? number_format($avgAchievement * 100, 1) . '%' : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Deviasi</th>
                                @foreach($weeklyStats as $week)
                                    <td class="text-center">{{ $week->deviation !== null ? number_format($week->deviation * 100, 1) . '%' : '-' }}</td>
                                @endforeach
                                <td class="text-center font-weight-bold">
                                    @php $avgDeviation = collect($weeklyStats)->filter(fn ($week) => $week->deviation !== null)->avg('deviation'); @endphp
                                    {{ $avgDeviation !== null ? number_format($avgDeviation * 100, 1) . '%' : '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title font-weight-bold">Detail Jadwal PM Dalam Periode</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Unit Code</th>
                                <th>Vendor / Tipe</th>
                                <th>Scheduled Date</th>
                                <th>Completed Date</th>
                                <th>Status</th>
                                <th>PIC</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $pm)
                                <tr>
                                    <td><strong>{{ $pm->unit_code }}</strong></td>
                                    <td>{{ $pm->unit?->vendor ?? '-' }} / {{ $pm->unit?->type_model ?? '-' }}</td>
                                    <td>{{ optional($pm->scheduled_date)->format('d M Y') ?? '-' }}</td>
                                    <td>{{ optional($pm->completed_date)->format('d M Y') ?? '-' }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $pm->status }}">{{ ucfirst($pm->status) }}</span>
                                    </td>
                                    <td>{{ $pm->pic_name ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada data PM pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
