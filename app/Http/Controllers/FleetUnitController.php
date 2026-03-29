<?php

namespace App\Http\Controllers;

use App\Models\FleetUnit;
use Illuminate\Http\Request;

class FleetUnitController extends Controller
{
    public function index(Request $request)
    {
        $q = FleetUnit::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($qb) use ($s) {
                $qb->where('unit_code', 'like', "%$s%")
                   ->orWhere('vendor', 'like', "%$s%")
                   ->orWhere('department', 'like', "%$s%")
                   ->orWhere('registration', 'like', "%$s%");
            });
        }

        if ($request->filled('vendor')) {
            $q->where('vendor', $request->vendor);
        }

        if ($request->filled('monitored')) {
            $q->where('is_monitored', $request->monitored === '1');
        }

        $units   = $q->orderBy('unit_code')->paginate(30)->withQueryString();
        $vendors = FleetUnit::distinct()->pluck('vendor')->filter()->sort()->values();

        return view('fleet.units.index', compact('units', 'vendors'));
    }

    public function create()
    {
        return view('fleet.units.form', ['unit' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'unit_code'    => 'required|string|max:50|unique:fleet_units,unit_code',
            'vendor'       => 'nullable|string|max:100',
            'department'   => 'nullable|string|max=200',
            'brand'        => 'nullable|string|max:100',
            'type_model'   => 'nullable|string|max:100',
            'registration' => 'nullable|string|max:50',
            'is_monitored' => 'boolean',
        ]);

        $data['is_monitored'] = $request->boolean('is_monitored');
        FleetUnit::create($data);

        return redirect()->route('fleet.units.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    public function edit(FleetUnit $unit)
    {
        return view('fleet.units.form', compact('unit'));
    }

    public function update(Request $request, FleetUnit $unit)
    {
        $data = $request->validate([
            'unit_code'    => 'required|string|max:50|unique:fleet_units,unit_code,' . $unit->id,
            'vendor'       => 'nullable|string|max:100',
            'department'   => 'nullable|string|max:200',
            'brand'        => 'nullable|string|max:100',
            'type_model'   => 'nullable|string|max:100',
            'registration' => 'nullable|string|max:50',
            'is_monitored' => 'boolean',
        ]);

        $data['is_monitored'] = $request->boolean('is_monitored');
        $unit->update($data);

        return redirect()->route('fleet.units.index')->with('success', 'Unit berhasil diupdate.');
    }

    public function destroy(FleetUnit $unit)
    {
        $unit->delete();
        return redirect()->route('fleet.units.index')->with('success', 'Unit berhasil dihapus.');
    }

    public function toggleMonitor(FleetUnit $unit)
    {
        $unit->update(['is_monitored' => !$unit->is_monitored]);
        return back()->with('success', 'Status monitoring unit diubah.');
    }
}
