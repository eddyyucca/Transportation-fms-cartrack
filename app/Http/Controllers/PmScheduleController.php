<?php

namespace App\Http\Controllers;

use App\Models\FleetUnit;
use App\Models\PmSchedule;
use App\Services\FleetWhatsappService;
use Carbon\Carbon;
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
