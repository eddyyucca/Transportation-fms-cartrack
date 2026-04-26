<?php

namespace App\Http\Controllers;

use App\Models\FleetUnit;
use App\Models\PmCheckReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PmCheckReportController extends Controller
{
    public function index()
    {
        $reports = PmCheckReport::withCount('vendorReports')
            ->orderByDesc('report_year')
            ->orderByDesc('week_number')
            ->paginate(15);

        return view('fleet.pm-reports.index', compact('reports'));
    }

    public function create()
    {
        $report = new PmCheckReport([
            'title' => 'PM Check Performance',
            'company_name' => 'PT Sulawesi Cahaya Mineral',
            'week_number' => now()->isoWeek(),
            'report_year' => now()->year,
            'start_date' => now()->startOfWeek(Carbon::SUNDAY)->toDateString(),
            'end_date' => now()->endOfWeek(Carbon::SATURDAY)->toDateString(),
        ]);

        return view('fleet.pm-reports.form', [
            'report' => $report,
            'vendorRows' => $this->defaultVendorRows(),
            'vendorOptions' => $this->vendorOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        DB::transaction(function () use ($data) {
            $report = PmCheckReport::create($data['report']);
            $this->syncVendorRows($report, $data['vendors']);
        });

        return redirect()->route('fleet.pm-reports.index')->with('success', 'Report PM Check berhasil dibuat.');
    }

    public function show(PmCheckReport $pmReport)
    {
        $pmReport->load('vendorReports');

        $vendorCards = $pmReport->vendorReports
            ->map(fn ($vendor) => $this->mapVendorCard($vendor))
            ->sortByDesc('achievement_percent')
            ->values();

        $kpi = (object) [
            'total_units' => $vendorCards->sum('total_units'),
            'vendors' => $vendorCards->count(),
            'plan_units' => $vendorCards->sum('plan_units'),
            'actual_units' => $vendorCards->sum('actual_units'),
        ];
        $kpi->achievement_percent = $kpi->plan_units > 0 ? round(($kpi->actual_units / $kpi->plan_units) * 100, 1) : null;

        $trendReports = PmCheckReport::with('vendorReports')
            ->where(function ($query) use ($pmReport) {
                $query->where('report_year', '<', $pmReport->report_year)
                    ->orWhere(function ($subQuery) use ($pmReport) {
                        $subQuery->where('report_year', $pmReport->report_year)
                            ->where('week_number', '<=', $pmReport->week_number);
                    });
            })
            ->orderByDesc('report_year')
            ->orderByDesc('week_number')
            ->limit(5)
            ->get()
            ->sortBy(fn ($report) => sprintf('%04d%02d', $report->report_year, $report->week_number))
            ->values();

        $vendorNames = $pmReport->vendorReports->pluck('vendor_name')->values();
        $trendChart = [
            'labels' => $trendReports->map(fn ($report) => 'W' . $report->week_number)->values(),
            'datasets' => $vendorNames->map(function ($vendorName) use ($trendReports) {
                return [
                    'label' => $vendorName,
                    'data' => $trendReports->map(function ($report) use ($vendorName) {
                        $vendor = $report->vendorReports->firstWhere('vendor_name', $vendorName);
                        if (!$vendor || (int) $vendor->plan_units <= 0) {
                            return null;
                        }

                        return round(((int) $vendor->actual_units / (int) $vendor->plan_units) * 100, 1);
                    })->values(),
                ];
            })->values(),
        ];

        $planActualChart = [
            'labels' => $vendorCards->pluck('vendor_name')->values(),
            'plan' => $vendorCards->pluck('plan_units')->values(),
            'actual' => $vendorCards->pluck('actual_units')->values(),
        ];

        return view('fleet.pm-reports.show', [
            'report' => $pmReport,
            'vendorCards' => $vendorCards,
            'kpi' => $kpi,
            'trendChart' => $trendChart,
            'planActualChart' => $planActualChart,
        ]);
    }

    public function edit(PmCheckReport $pmReport)
    {
        $pmReport->load('vendorReports');

        $vendorRows = $pmReport->vendorReports->map(function ($vendor) {
            return [
                'vendor_name' => $vendor->vendor_name,
                'theme' => $vendor->theme,
                'total_units' => $vendor->total_units,
                'plan_units' => $vendor->plan_units,
                'daily_actual' => collect($vendor->daily_actual ?? [])->pad(7, 0)->take(7)->values()->all(),
            ];
        })->values()->all();

        if ($vendorRows === []) {
            $vendorRows = $this->defaultVendorRows();
        }

        return view('fleet.pm-reports.form', [
            'report' => $pmReport,
            'vendorRows' => $vendorRows,
            'vendorOptions' => $this->vendorOptions(),
        ]);
    }

    public function update(Request $request, PmCheckReport $pmReport)
    {
        $data = $this->validateRequest($request, $pmReport);

        DB::transaction(function () use ($pmReport, $data) {
            $pmReport->update($data['report']);
            $pmReport->vendorReports()->delete();
            $this->syncVendorRows($pmReport, $data['vendors']);
        });

        return redirect()->route('fleet.pm-reports.show', $pmReport)->with('success', 'Report PM Check berhasil diupdate.');
    }

    public function destroy(PmCheckReport $pmReport)
    {
        $pmReport->delete();

        return redirect()->route('fleet.pm-reports.index')->with('success', 'Report PM Check berhasil dihapus.');
    }

    protected function validateRequest(Request $request, ?PmCheckReport $report = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'company_name' => ['required', 'string', 'max:150'],
            'week_number' => [
                'required',
                'integer',
                'min:1',
                'max:53',
                Rule::unique('pm_check_reports')
                    ->ignore($report?->id)
                    ->where(fn ($query) => $query->where('report_year', $request->integer('report_year'))),
            ],
            'report_year' => ['required', 'integer', 'min:2024', 'max:2100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string'],
            'vendors' => ['required', 'array', 'min:1'],
            'vendors.*.vendor_name' => ['required', 'string', 'max:100'],
            'vendors.*.theme' => ['required', Rule::in(['default', 'bagong', 'transkon', 'trac'])],
            'vendors.*.total_units' => ['required', 'integer', 'min:0'],
            'vendors.*.plan_units' => ['required', 'integer', 'min:0'],
            'vendors.*.daily_actual' => ['required', 'array', 'size:7'],
            'vendors.*.daily_actual.*' => ['nullable', 'integer', 'min:0'],
        ]);

        return [
            'report' => [
                'title' => $validated['title'],
                'company_name' => $validated['company_name'],
                'week_number' => $validated['week_number'],
                'report_year' => $validated['report_year'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'notes' => $validated['notes'] ?? null,
            ],
            'vendors' => collect($validated['vendors'])
                ->filter(fn ($vendor) => trim((string) ($vendor['vendor_name'] ?? '')) !== '')
                ->values()
                ->map(function ($vendor, $index) {
                    $dailyActual = collect($vendor['daily_actual'])->map(fn ($value) => (int) ($value ?? 0))->take(7)->pad(7, 0)->values()->all();

                    return [
                        'vendor_name' => trim((string) $vendor['vendor_name']),
                        'theme' => $vendor['theme'],
                        'total_units' => (int) $vendor['total_units'],
                        'plan_units' => (int) $vendor['plan_units'],
                        'actual_units' => array_sum($dailyActual),
                        'daily_actual' => $dailyActual,
                        'sort_order' => $index + 1,
                    ];
                })->all(),
        ];
    }

    protected function syncVendorRows(PmCheckReport $report, array $vendors): void
    {
        $report->vendorReports()->createMany($vendors);
    }

    protected function vendorOptions()
    {
        return FleetUnit::query()
            ->select('vendor')
            ->selectRaw('COUNT(*) as total_units')
            ->whereNotNull('vendor')
            ->where('vendor', '!=', '')
            ->groupBy('vendor')
            ->orderBy('vendor')
            ->get();
    }

    protected function defaultVendorRows(): array
    {
        $vendorCounts = $this->vendorOptions()->keyBy('vendor');
        $defaults = [
            ['vendor_name' => 'Bagong', 'theme' => 'bagong'],
            ['vendor_name' => 'Transkon', 'theme' => 'transkon'],
            ['vendor_name' => 'Trac', 'theme' => 'trac'],
        ];

        return collect($defaults)->map(function ($vendor) use ($vendorCounts) {
            $matched = $vendorCounts->first(function ($item, $name) use ($vendor) {
                return strcasecmp((string) $name, $vendor['vendor_name']) === 0;
            });

            return [
                'vendor_name' => $vendor['vendor_name'],
                'theme' => $vendor['theme'],
                'total_units' => (int) ($matched->total_units ?? 0),
                'plan_units' => 0,
                'daily_actual' => [0, 0, 0, 0, 0, 0, 0],
            ];
        })->all();
    }

    protected function mapVendorCard($vendor): array
    {
        $dailyActual = collect($vendor->daily_actual ?? [])->map(fn ($value) => (int) $value)->take(7)->pad(7, 0)->values();
        $maxDaily = max(1, (int) $dailyActual->max());
        $hasPlan = (int) $vendor->plan_units > 0;
        $achievementPercent = $vendor->achievement_ratio !== null ? round($vendor->achievement_ratio * 100, 1) : null;
        $deviationUnits = max(0, (int) $vendor->plan_units - (int) $vendor->actual_units);

        if (!$hasPlan) {
            $statusText = 'No Plan';
            $statusOk = false;
        } else {
            $statusText = $deviationUnits === 0 ? 'On Target' : 'Dev: -' . $deviationUnits . ' units';
            $statusOk = $deviationUnits === 0;
        }

        return [
            'vendor_name' => $vendor->vendor_name,
            'theme' => $vendor->theme,
            'total_units' => (int) $vendor->total_units,
            'plan_units' => (int) $vendor->plan_units,
            'actual_units' => (int) $vendor->actual_units,
            'achievement_percent' => $achievementPercent,
            'status_text' => $statusText,
            'status_ok' => $statusOk,
            'daily_bars' => $dailyActual->map(function ($value) use ($maxDaily) {
                $height = max(12, (int) round(($value / $maxDaily) * 61));

                return [
                    'value' => $value,
                    'height' => $height,
                    'low' => $value < $maxDaily,
                ];
            })->all(),
        ];
    }
}
