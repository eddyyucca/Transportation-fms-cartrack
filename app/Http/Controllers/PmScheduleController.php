<?php

namespace App\Http\Controllers;

use App\Models\FleetUnit;
use App\Models\PmSchedule;
use App\Services\FleetWhatsappService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class PmScheduleController extends Controller
{
    public function __construct(protected FleetWhatsappService $wa) {}

    public function index(Request $request)
    {
        PmSchedule::refreshStatuses();

        $q = PmSchedule::with('unit');

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        if ($request->filled('unit_code')) {
            $q->where('unit_code', $request->unit_code);
        }

        $schedules = $q->orderBy('scheduled_date')->paginate(30)->withQueryString();
        $units     = FleetUnit::where('is_monitored', true)->orderBy('unit_code')->pluck('unit_code');

        $overdueCount = PmSchedule::where('status', 'overdue')->count();

        return view('fleet.pm.index', compact('schedules', 'units', 'overdueCount'));
    }

    public function create()
    {
        $units = FleetUnit::where('is_monitored', true)->orderBy('unit_code')->get();
        return view('fleet.pm.form', ['schedule' => null, 'units' => $units]);
    }

    public function report(Request $request)
    {
        PmSchedule::refreshStatuses();

        $selectedMonth = $request->get('month');
        if (!$selectedMonth) {
            $latestDate = PmSchedule::query()
                ->selectRaw('MAX(COALESCE(completed_date, scheduled_date)) as latest_date')
                ->value('latest_date');

            $selectedMonth = Carbon::parse($latestDate ?: now())->format('Y-m');
        }

        $startDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $records = PmSchedule::with('unit')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhereBetween('completed_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->orderBy('scheduled_date')
            ->orderBy('unit_code')
            ->get();

        $days = collect(CarbonPeriod::create($startDate, $endDate))
            ->map(fn (Carbon $date) => $date->copy())
            ->values();

        $dailyStats = $days->map(function (Carbon $date) use ($records) {
            $dateString = $date->toDateString();
            $target = $records->filter(fn (PmSchedule $pm) => optional($pm->scheduled_date)->toDateString() === $dateString)->count();
            $actual = $records->filter(function (PmSchedule $pm) use ($dateString) {
                if ($pm->status !== 'done') {
                    return false;
                }

                $actualDate = $pm->completed_date ?: $pm->scheduled_date;

                return optional($actualDate)->toDateString() === $dateString;
            })->count();

            return (object) [
                'date' => $date,
                'label' => $date->day,
                'target' => $target,
                'actual' => $actual,
                'achievement' => $target > 0 ? round($actual / $target, 4) : null,
            ];
        });

        $weeklyStats = $dailyStats
            ->groupBy(fn ($day) => $day->date->isoWeek())
            ->map(function ($items, $weekNumber) {
                $plan = $items->sum('target');
                $actual = $items->sum('actual');
                $achievement = $plan > 0 ? round($actual / $plan, 4) : null;

                return (object) [
                    'week_label' => 'W' . $weekNumber,
                    'plan' => $plan,
                    'actual' => $actual,
                    'achievement' => $achievement,
                    'deviation' => $achievement !== null ? round(max(0, 1 - $achievement), 4) : null,
                ];
            })
            ->values();

        $monthOptions = PmSchedule::query()
            ->selectRaw("DATE_FORMAT(scheduled_date, '%Y-%m') as month_key")
            ->whereNotNull('scheduled_date')
            ->groupBy('month_key')
            ->orderByDesc('month_key')
            ->pluck('month_key');

        if (!$monthOptions->contains($selectedMonth)) {
            $monthOptions = $monthOptions->prepend($selectedMonth)->unique()->values();
        }

        $summary = (object) [
            'target' => $dailyStats->sum('target'),
            'actual' => $dailyStats->sum('actual'),
            'achievement' => $dailyStats->sum('target') > 0
                ? round($dailyStats->sum('actual') / $dailyStats->sum('target'), 4)
                : null,
            'done' => $records->where('status', 'done')->count(),
            'overdue' => $records->where('status', 'overdue')->count(),
            'scheduled' => $records->where('status', 'scheduled')->count(),
        ];

        return view('fleet.pm.report', [
            'selectedDate' => $endDate->toDateString(),
            'selectedMonth' => $selectedMonth,
            'monthOptions' => $monthOptions,
            'periodLabel' => $startDate->translatedFormat('F Y'),
            'dailyStats' => $dailyStats,
            'weeklyStats' => $weeklyStats,
            'summary' => $summary,
            'records' => $records,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'unit_code'      => 'required|exists:fleet_units,unit_code',
            'scheduled_date' => 'required|date',
            'pic_name'       => 'required|string|max:100',
            'pic_phone'      => 'required|string|max:20',
            'notes'          => 'nullable|string',
        ]);

        $data['status'] = Carbon::parse($data['scheduled_date'])->isPast() ? 'overdue' : 'scheduled';

        PmSchedule::create($data);

        return redirect()->route('fleet.pm.index')->with('success', 'Jadwal PM berhasil ditambahkan.');
    }

    public function edit(PmSchedule $pm)
    {
        $units = FleetUnit::where('is_monitored', true)->orderBy('unit_code')->get();
        return view('fleet.pm.form', ['schedule' => $pm, 'units' => $units]);
    }

    public function update(Request $request, PmSchedule $pm)
    {
        $data = $request->validate([
            'unit_code'      => 'required|exists:fleet_units,unit_code',
            'scheduled_date' => 'required|date',
            'completed_date' => 'nullable|date',
            'status'         => 'required|in:scheduled,done,overdue',
            'pic_name'       => 'required|string|max:100',
            'pic_phone'      => 'required|string|max:20',
            'notes'          => 'nullable|string',
        ]);

        $pm->update($data);

        return redirect()->route('fleet.pm.index')->with('success', 'Jadwal PM berhasil diupdate.');
    }

    public function destroy(PmSchedule $pm)
    {
        $pm->delete();
        return redirect()->route('fleet.pm.index')->with('success', 'Jadwal PM dihapus.');
    }

    public function markDone(PmSchedule $pm)
    {
        $pm->update([
            'status'         => 'done',
            'completed_date' => now()->toDateString(),
        ]);

        return back()->with('success', 'PM Unit ' . $pm->unit_code . ' ditandai selesai.');
    }

    public function sendNotif(PmSchedule $pm)
    {
        $result = $this->wa->sendPmReminder($pm);

        if ($result['success']) {
            $pm->update(['notif_sent' => true, 'notif_sent_at' => now()]);
            return back()->with('success', 'Notifikasi WA berhasil dikirim ke ' . $pm->pic_phone);
        }

        return back()->with('error', 'Gagal kirim WA: ' . $result['response']);
    }

    public function sendOverdueNotif()
    {
        PmSchedule::refreshStatuses();

        $overdues = PmSchedule::where('status', 'overdue')
            ->where('notif_sent', false)
            ->get();

        if ($overdues->isEmpty()) {
            return back()->with('success', 'Tidak ada overdue PM yang belum dinotifikasi.');
        }

        $sent = 0;
        foreach ($overdues as $pm) {
            $result = $this->wa->sendPmReminder($pm);
            if ($result['success']) {
                $pm->update(['notif_sent' => true, 'notif_sent_at' => now()]);
                $sent++;
            }
        }

        return back()->with('success', "Notifikasi WA terkirim ke $sent unit overdue PM.");
    }

    public function generateBiweekly(Request $request)
    {
        $data = $request->validate([
            'unit_codes'  => 'required|array',
            'unit_codes.*'=> 'exists:fleet_units,unit_code',
            'start_date'  => 'required|date',
            'pic_name'    => 'required|string|max:100',
            'pic_phone'   => 'required|string|max:20',
        ]);

        $start    = Carbon::parse($data['start_date']);
        $created  = 0;

        // Generate 6 jadwal bi-weekly (3 bulan ke depan)
        for ($i = 0; $i < 6; $i++) {
            $schedDate = $start->copy()->addWeeks($i * 2);
            foreach ($data['unit_codes'] as $unitCode) {
                $exists = PmSchedule::where('unit_code', $unitCode)
                    ->where('scheduled_date', $schedDate->toDateString())
                    ->exists();

                if (!$exists) {
                    PmSchedule::create([
                        'unit_code'      => $unitCode,
                        'scheduled_date' => $schedDate->toDateString(),
                        'status'         => $schedDate->isPast() ? 'overdue' : 'scheduled',
                        'pic_name'       => $data['pic_name'],
                        'pic_phone'      => $data['pic_phone'],
                    ]);
                    $created++;
                }
            }
        }

        return back()->with('success', "$created jadwal PM bi-weekly berhasil dibuat.");
    }
}
