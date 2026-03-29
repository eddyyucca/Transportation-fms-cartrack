<?php

namespace App\Http\Controllers;

use App\Models\FleetUnit;
use App\Models\FleetUnitDailyMetric;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FleetMetricController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(6)->format('Y-m-d'));
        $dateTo   = $request->get('date_to', now()->format('Y-m-d'));

        $monitoredUnits = FleetUnit::where('is_monitored', true)->pluck('unit_code');

        $q = FleetUnitDailyMetric::with('unit')
            ->whereIn('unit_code', $monitoredUnits)
            ->whereBetween('report_date', [$dateFrom, $dateTo]);

        if ($request->filled('unit_code')) {
            $q->where('unit_code', $request->unit_code);
        }

        $metrics = $q->orderByDesc('report_date')->orderBy('unit_code')->paginate(40)->withQueryString();
        $units   = FleetUnit::where('is_monitored', true)->orderBy('unit_code')->pluck('unit_code');

        return view('fleet.metrics.index', compact('metrics', 'units', 'dateFrom', 'dateTo'));
    }

    public function create()
    {
        $units = FleetUnit::where('is_monitored', true)->orderBy('unit_code')->get();
        return view('fleet.metrics.form', ['metric' => null, 'units' => $units]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'unit_code'        => 'required|exists:fleet_units,unit_code',
            'report_date'      => 'required|date',
            'idle_hours'       => 'required|numeric|min:0',
            'hm_start'         => 'nullable|numeric|min:0',
            'hm_end'           => 'nullable|numeric|min:0',
            'distance_km'      => 'required|numeric|min:0',
            'ua_percent'       => 'required|numeric|min:0|max:100',
            'standby_hours'    => 'required|numeric|min:0',
            'fuel_consumption' => 'required|numeric|min:0',
            'notes'            => 'nullable|string',
        ]);

        if (!empty($data['hm_start']) && !empty($data['hm_end'])) {
            $data['hm_usage'] = max(0, (float)$data['hm_end'] - (float)$data['hm_start']);
        }

        FleetUnitDailyMetric::updateOrCreate(
            ['unit_code' => $data['unit_code'], 'report_date' => $data['report_date']],
            $data
        );

        return redirect()->route('fleet.metrics.index')->with('success', 'Data metrik berhasil disimpan.');
    }

    public function edit(FleetUnitDailyMetric $metric)
    {
        $units = FleetUnit::where('is_monitored', true)->orderBy('unit_code')->get();
        return view('fleet.metrics.form', compact('metric', 'units'));
    }

    public function update(Request $request, FleetUnitDailyMetric $metric)
    {
        $data = $request->validate([
            'unit_code'        => 'required|exists:fleet_units,unit_code',
            'report_date'      => 'required|date',
            'idle_hours'       => 'required|numeric|min:0',
            'hm_start'         => 'nullable|numeric|min:0',
            'hm_end'           => 'nullable|numeric|min:0',
            'distance_km'      => 'required|numeric|min:0',
            'ua_percent'       => 'required|numeric|min:0|max:100',
            'standby_hours'    => 'required|numeric|min:0',
            'fuel_consumption' => 'required|numeric|min:0',
            'notes'            => 'nullable|string',
        ]);

        if (!empty($data['hm_start']) && !empty($data['hm_end'])) {
            $data['hm_usage'] = max(0, (float)$data['hm_end'] - (float)$data['hm_start']);
        }

        $metric->update($data);

        return redirect()->route('fleet.metrics.index')->with('success', 'Data metrik berhasil diupdate.');
    }

    public function destroy(FleetUnitDailyMetric $metric)
    {
        $metric->delete();
        return redirect()->route('fleet.metrics.index')->with('success', 'Data metrik dihapus.');
    }

    public function export(Request $request): StreamedResponse
    {
        $dateFrom = $request->get('date_from', now()->subDays(6)->format('Y-m-d'));
        $dateTo   = $request->get('date_to', now()->format('Y-m-d'));

        $monitoredUnits = FleetUnit::where('is_monitored', true)->pluck('unit_code');

        $q = FleetUnitDailyMetric::with('unit')
            ->whereIn('unit_code', $monitoredUnits)
            ->whereBetween('report_date', [$dateFrom, $dateTo]);

        if ($request->filled('unit_code')) {
            $q->where('unit_code', $request->unit_code);
        }

        $rows     = $q->orderBy('report_date')->orderBy('unit_code')->get();
        $filename = 'fleet-metrics-' . $dateFrom . '-to-' . $dateTo . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Tanggal', 'Unit Code', 'Vendor', 'Departemen', 'Merek', 'Tipe',
                'Idle (Jam)', 'HM Awal', 'HM Akhir', 'HM Usage',
                'Jarak KM', 'UA %', 'Standby (Jam)', 'Fuel (Liter)', 'Catatan'
            ]);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->report_date?->format('Y-m-d'),
                    $row->unit_code,
                    $row->unit?->vendor,
                    $row->unit?->department,
                    $row->unit?->brand,
                    $row->unit?->type_model,
                    $row->idle_hours,
                    $row->hm_start,
                    $row->hm_end,
                    $row->hm_usage,
                    $row->distance_km,
                    $row->ua_percent,
                    $row->standby_hours,
                    $row->fuel_consumption,
                    $row->notes,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
