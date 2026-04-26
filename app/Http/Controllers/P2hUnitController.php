<?php

namespace App\Http\Controllers;

use App\Models\P2hUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class P2hUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = P2hUnit::query();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('unit_code', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('plate_no', 'like', '%' . $search . '%')
                    ->orWhere('vendor', 'like', '%' . $search . '%');
            });
        }

        $units = $query->orderBy('department')->orderBy('unit_code')->paginate(20)->withQueryString();

        return view('fleet.p2h.units.index', [
            'units' => $units,
            'summary' => [
                'total_units' => P2hUnit::count(),
                'active_units' => P2hUnit::where('is_active', true)->count(),
                'departments' => P2hUnit::whereNotNull('department')->where('department', '!=', '')->distinct('department')->count('department'),
                'vendors' => P2hUnit::whereNotNull('vendor')->where('vendor', '!=', '')->distinct('vendor')->count('vendor'),
            ],
        ]);
    }

    public function create()
    {
        return view('fleet.p2h.units.form', ['unit' => new P2hUnit()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        P2hUnit::create($data);

        return redirect()->route('fleet.p2h.units.index')->with('success', 'Master unit P2H berhasil ditambahkan.');
    }

    public function edit(P2hUnit $unit)
    {
        return view('fleet.p2h.units.form', compact('unit'));
    }

    public function update(Request $request, P2hUnit $unit)
    {
        $data = $this->validated($request, $unit);
        $unit->update($data);

        return redirect()->route('fleet.p2h.units.index')->with('success', 'Master unit P2H berhasil diupdate.');
    }

    protected function validated(Request $request, ?P2hUnit $unit = null): array
    {
        return $request->validate([
            'vendor' => ['nullable', 'string', 'max:100'],
            'unit_code' => ['required', 'string', 'max:100', Rule::unique('p2h_units', 'unit_code')->ignore($unit?->id)],
            'head' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:150'],
            'plate_no' => ['nullable', 'string', 'max:100'],
            'pic_name' => ['nullable', 'string', 'max:150'],
            'model_name' => ['nullable', 'string', 'max:200'],
            'is_active' => ['boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
