<?php

namespace App\Http\Controllers;

use App\Models\P2hChecklist;
use App\Models\P2hUnit;
use App\Services\P2hImportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class P2hReportController extends Controller
{
    public function __construct(protected P2hImportService $importService) {}

    public function index()
    {
        $weeks = P2hChecklist::query()
            ->whereHas('unit', function ($query) {
                $query->where('is_active', true)
                    ->whereNotNull('department')
                    ->where('department', '!=', '')
                    ->where('department', '!=', 'Unassigned');
            })
            ->select('checklist_date')
            ->distinct()
            ->orderByDesc('checklist_date')
            ->get()
            ->map(function ($item) {
                $date = Carbon::parse($item->checklist_date);

                return [
                    'year' => $date->isoWeekYear,
                    'week' => $date->isoWeek(),
                ];
            })
            ->unique(fn ($item) => $item['year'] . '-' . $item['week'])
            ->sortByDesc(fn ($item) => sprintf('%04d%02d', $item['year'], $item['week']))
            ->values()
            ->map(function ($item) {
                $metrics = $this->buildWeeklyMetrics($item['week'], $item['year']);
                $lastInput = P2hChecklist::query()
                    ->whereHas('unit', function ($query) {
                        $query->where('is_active', true)
                            ->whereNotNull('department')
                            ->where('department', '!=', '')
                            ->where('department', '!=', 'Unassigned');
                    })
                    ->whereBetween('checklist_date', [
                        $metrics['startDate']->toDateString(),
                        $metrics['endDate']->toDateString(),
                    ])
                    ->latest('updated_at')
                    ->value('updated_at');

                return [
                    'week_number' => $item['week'],
                    'report_year' => $item['year'],
                    'start_date' => $metrics['startDate'],
                    'end_date' => $metrics['endDate'],
                    'overall_percent' => $metrics['overallPercent'],
                    'total_units' => $metrics['totalUnits'],
                    'achieve_units' => $metrics['achieveUnits'],
                    'not_achieve_units' => $metrics['notAchieveUnits'],
                    'departments' => $metrics['totalDepartments'],
                    'updated_at' => $lastInput ? Carbon::parse($lastInput) : null,
                ];
            });

        return view('fleet.p2h.index', [
            'selectedDate' => now()->toDateString(),
            'reports' => $weeks,
        ]);
    }

    public function dailyIndex(Request $request)
    {
        $query = P2hChecklist::query()
            ->with('unit')
            ->whereHas('unit', function ($unitQuery) {
                $unitQuery->where('is_active', true)
                    ->whereNotNull('department')
                    ->where('department', '!=', '')
                    ->where('department', '!=', 'Unassigned');
            });

        if ($request->filled('week') && $request->filled('year')) {
            $weekStart = now()->setISODate((int) $request->year, (int) $request->week)->startOfWeek(Carbon::SUNDAY);
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SATURDAY);
            $query->whereBetween('checklist_date', [$weekStart->toDateString(), $weekEnd->toDateString()]);
        }

        if ($request->filled('date')) {
            $query->whereDate('checklist_date', $request->date);
        }

        if ($request->filled('unit_id')) {
            $query->where('p2h_unit_id', $request->unit_id);
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        $checklists = $query
            ->latest('checklist_date')
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('fleet.p2h.daily', [
            'selectedDate' => now()->toDateString(),
            'units' => P2hUnit::query()
                ->where('is_active', true)
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->where('department', '!=', 'Unassigned')
                ->orderBy('department')
                ->orderBy('unit_code')
                ->get(),
            'checklists' => $checklists,
            'summary' => [
                'total_checklists' => P2hChecklist::count(),
                'today_checklists' => P2hChecklist::whereDate('checklist_date', now()->toDateString())->count(),
                'manual_count' => P2hChecklist::where('source_type', 'manual')->count(),
                'upload_count' => P2hChecklist::where('source_type', 'upload')->count(),
            ],
            'editChecklist' => null,
        ]);
    }

    public function dashboard(Request $request)
    {
        $selectedWeek = (int) $request->get('week', now()->isoWeek());
        $selectedYear = (int) $request->get('year', now()->year);

        $data = $this->buildWeeklyMetrics($selectedWeek, $selectedYear);

        return view('fleet.p2h.dashboard', $data);
    }

    protected function buildWeeklyMetrics(int $selectedWeek, int $selectedYear): array
    {
        $baseDate = now()->setISODate($selectedYear, $selectedWeek);
        $startDate = $baseDate->copy()->startOfWeek(Carbon::SUNDAY);
        $endDate = $startDate->copy()->endOfWeek(Carbon::SATURDAY);

        $weeks = collect([
            ['label' => 'W' . ($selectedWeek - 2), 'start' => $startDate->copy()->subWeeks(2), 'end' => $endDate->copy()->subWeeks(2)],
            ['label' => 'W' . ($selectedWeek - 1), 'start' => $startDate->copy()->subWeek(), 'end' => $endDate->copy()->subWeek()],
            ['label' => 'W' . $selectedWeek, 'start' => $startDate->copy(), 'end' => $endDate->copy()],
        ]);

        $activeUnits = P2hUnit::query()
            ->where('is_active', true)
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->where('department', '!=', 'Unassigned')
            ->orderBy('department')
            ->orderBy('unit_code')
            ->get();

        $unitIds = $activeUnits->pluck('id');

        $checklists = P2hChecklist::query()
            ->whereIn('p2h_unit_id', $unitIds)
            ->whereBetween('checklist_date', [$weeks->first()['start']->toDateString(), $weeks->last()['end']->toDateString()])
            ->get()
            ->groupBy(fn ($item) => $item->p2h_unit_id . '|' . $item->checklist_date->format('Y-m-d'));

        $departmentStats = $activeUnits
            ->groupBy(fn ($unit) => $unit->department)
            ->map(function (Collection $units, string $department) use ($weeks, $checklists) {
                $unitIds = $units->pluck('id')->all();
                $weekPercents = $weeks->map(function (array $week) use ($unitIds, $checklists) {
                    $days = Carbon::parse($week['start'])->diffInDays(Carbon::parse($week['end'])) + 1;
                    $submitted = $checklists->filter(function ($rows, $key) use ($unitIds, $week) {
                        [$unitId, $date] = explode('|', $key);
                        return in_array((int) $unitId, $unitIds, true) && $date >= $week['start']->toDateString() && $date <= $week['end']->toDateString();
                    })->count();

                    $plan = count($unitIds) * $days;
                    return $plan > 0 ? round(($submitted / $plan) * 100, 1) : 0.0;
                })->values();

                return [
                    'department' => $department,
                    'total_units' => $units->count(),
                    'w_prev2' => $weekPercents[0],
                    'w_prev1' => $weekPercents[1],
                    'w_current' => $weekPercents[2],
                ];
            })
            ->sortByDesc('w_current')
            ->values();

        $currentWeekUnitCounts = $activeUnits->map(function ($unit) use ($startDate, $endDate, $checklists) {
            $submitted = 0;
            $cursor = $startDate->copy();
            while ($cursor <= $endDate) {
                if ($checklists->has($unit->id . '|' . $cursor->toDateString())) {
                    $submitted++;
                }
                $cursor->addDay();
            }
            return ['unit_id' => $unit->id, 'submitted' => $submitted];
        });

        $achieveUnits = $currentWeekUnitCounts->where('submitted', '>=', 7)->count();
        $totalUnits = $activeUnits->count();
        $notAchieveUnits = $totalUnits - $achieveUnits;

        $overallPercent = $departmentStats->isNotEmpty() ? round($departmentStats->avg('w_current'), 1) : 0;
        $improved = $departmentStats->filter(fn ($item) => $item['w_current'] > $item['w_prev1'])->count();
        $declined = $departmentStats->filter(fn ($item) => $item['w_current'] < $item['w_prev1'])->count();
        $stable = $departmentStats->filter(fn ($item) => $item['w_current'] === $item['w_prev1'])->count();

        $topPerformers = $departmentStats->take(3)->values();
        $needsAction = $departmentStats->sortBy('w_current')->take(3)->values();

        return [
            'selectedDate' => $endDate->toDateString(),
            'selectedWeek' => $selectedWeek,
            'selectedYear' => $selectedYear,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'departmentStats' => $departmentStats,
            'overallPercent' => $overallPercent,
            'totalUnits' => $totalUnits,
            'achieveUnits' => $achieveUnits,
            'notAchieveUnits' => $notAchieveUnits,
            'improved' => $improved,
            'declined' => $declined,
            'stable' => $stable,
            'topPerformers' => $topPerformers,
            'needsAction' => $needsAction,
            'totalDepartments' => $departmentStats->count(),
            'activeUnits' => $activeUnits,
            'chart' => [
                'labels' => $departmentStats->pluck('department')->values(),
                'w_prev2' => $departmentStats->pluck('w_prev2')->values(),
                'w_prev1' => $departmentStats->pluck('w_prev1')->values(),
                'w_current' => $departmentStats->pluck('w_current')->values(),
                'week_labels' => $weeks->pluck('label')->values(),
            ],
        ];
    }

    public function manualStore(Request $request)
    {
        $data = $this->validateChecklist($request);

        P2hChecklist::updateOrCreate(
            [
                'p2h_unit_id' => $data['p2h_unit_id'],
                'checklist_date' => $data['checklist_date'],
            ],
            [
                'kilometer' => $data['kilometer'] ?? null,
                'safe_to_use' => (bool) $data['safe_to_use'],
                'maintenance_required' => (bool) $data['maintenance_required'],
                'created_by_name' => $data['created_by_name'] ?? null,
                'source_type' => 'manual',
            ]
        );

        return redirect()->route('fleet.p2h.daily.index')->with('success', 'Checklist manual berhasil disimpan.');
    }

    public function editChecklist(P2hChecklist $checklist)
    {
        return view('fleet.p2h.daily', [
            'selectedDate' => now()->toDateString(),
            'units' => P2hUnit::query()
                ->where('is_active', true)
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->where('department', '!=', 'Unassigned')
                ->orderBy('department')
                ->orderBy('unit_code')
                ->get(),
            'checklists' => P2hChecklist::query()
                ->with('unit')
                ->whereHas('unit', function ($unitQuery) {
                    $unitQuery->where('is_active', true)
                        ->whereNotNull('department')
                        ->where('department', '!=', '')
                        ->where('department', '!=', 'Unassigned');
                })
                ->latest('checklist_date')
                ->latest('updated_at')
                ->paginate(20),
            'summary' => [
                'total_checklists' => P2hChecklist::count(),
                'today_checklists' => P2hChecklist::whereDate('checklist_date', now()->toDateString())->count(),
                'manual_count' => P2hChecklist::where('source_type', 'manual')->count(),
                'upload_count' => P2hChecklist::where('source_type', 'upload')->count(),
            ],
            'editChecklist' => $checklist->load('unit'),
        ]);
    }

    public function updateChecklist(Request $request, P2hChecklist $checklist)
    {
        $data = $this->validateChecklist($request);

        $checklist->update([
            'p2h_unit_id' => $data['p2h_unit_id'],
            'checklist_date' => $data['checklist_date'],
            'kilometer' => $data['kilometer'] ?? null,
            'safe_to_use' => (bool) $data['safe_to_use'],
            'maintenance_required' => (bool) $data['maintenance_required'],
            'created_by_name' => $data['created_by_name'] ?? null,
            'source_type' => 'manual',
        ]);

        return redirect()->route('fleet.p2h.daily.index')->with('success', 'Checklist harian berhasil diupdate.');
    }

    public function uploadChecklist(Request $request)
    {
        $request->validate([
            'checklist_file' => ['required', 'file', 'mimes:xls,xlsx'],
        ]);

        $file = $request->file('checklist_file');
        $stored = $file->storeAs('private/p2h-imports', now()->format('Ymd_His_') . $file->getClientOriginalName());
        $path = storage_path('app/' . $stored);

        $result = $this->importService->importChecklistWorkbook($path, $file->getClientOriginalName());

        return redirect()->route('fleet.p2h.daily.index')->with(
            'success',
            "Upload checklist selesai. Imported {$result['imported']} data, skipped {$result['skipped']}, tidak ada di master {$result['not_in_master']}, spare/no dept {$result['spare_units']}."
        );
    }

    public function importMaster()
    {
        $path = 'c:\\Users\\eddy.saputra\\Documents\\Transportation\\04. P2h Apr 2026.xlsx';
        if (! is_file($path)) {
            return back()->with('error', 'File master P2H tidak ditemukan di lokasi default.');
        }

        $result = $this->importService->importMasterWorkbook($path);

        return back()->with('success', "Master P2H berhasil diimport: {$result['imported']} unit.");
    }

    protected function validateChecklist(Request $request): array
    {
        return $request->validate([
            'p2h_unit_id' => ['required', 'exists:p2h_units,id'],
            'checklist_date' => ['required', 'date'],
            'kilometer' => ['nullable', 'integer', 'min:0'],
            'safe_to_use' => ['required', 'boolean'],
            'maintenance_required' => ['required', 'boolean'],
            'created_by_name' => ['nullable', 'string', 'max:150'],
        ]);
    }
}
