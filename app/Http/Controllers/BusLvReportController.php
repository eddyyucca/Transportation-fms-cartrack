<?php

namespace App\Http\Controllers;

use App\Models\BusLvReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class BusLvReportController extends Controller
{
    public function index()
    {
        $reports = BusLvReport::query()
            ->orderByDesc('report_year')
            ->orderByDesc('week_number')
            ->paginate(15);

        return view('fleet.bus-lv-reports.index', compact('reports'));
    }

    public function create()
    {
        $report = new BusLvReport([
            'title' => 'Bus & LV SCM',
            'company_name' => 'PT Sulawesi Cahaya Mineral',
            'week_number' => now()->isoWeek(),
            'report_year' => now()->year,
            'start_date' => now()->startOfWeek(Carbon::SUNDAY)->toDateString(),
            'end_date' => now()->endOfWeek(Carbon::SATURDAY)->toDateString(),
            'bus_unit_count_daily' => [2, 2, 2, 2, 2, 2, 2],
            'bus_co_daily' => [0, 0, 0, 0, 0, 0, 0],
            'bus_ci_daily' => [0, 0, 0, 0, 0, 0, 0],
            'bus_capacity_daily' => [53, 53, 53, 53, 53, 53, 53],
            'bus_extra_daily' => [0, 0, 0, 0, 0, 0, 0],
            'lv_unit_count_daily' => [1, 1, 1, 1, 1, 1, 1],
            'lv_co_daily' => [0, 0, 0, 0, 0, 0, 0],
            'lv_ci_daily' => [0, 0, 0, 0, 0, 0, 0],
            'lv_capacity_daily' => [4, 4, 4, 4, 4, 4, 4],
            'notes' => ['Data masih parsial', '', '', ''],
        ]);

        return view('fleet.bus-lv-reports.form', [
            'report' => $report,
            'dayLabels' => $this->dayLabels($report->start_date, $report->end_date),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $report = BusLvReport::create($data);

        return redirect()->route('fleet.bus-lv-reports.show', $report)->with('success', 'Report Bus & LV berhasil dibuat.');
    }

    public function show(BusLvReport $busLvReport)
    {
        $previous = BusLvReport::query()
            ->where(function ($query) use ($busLvReport) {
                $query->where('report_year', '<', $busLvReport->report_year)
                    ->orWhere(function ($subQuery) use ($busLvReport) {
                        $subQuery->where('report_year', $busLvReport->report_year)
                            ->where('week_number', '<', $busLvReport->week_number);
                    });
            })
            ->orderByDesc('report_year')
            ->orderByDesc('week_number')
            ->first();

        $recentLvReports = BusLvReport::query()
            ->where(function ($query) use ($busLvReport) {
                $query->where('report_year', '<', $busLvReport->report_year)
                    ->orWhere(function ($subQuery) use ($busLvReport) {
                        $subQuery->where('report_year', $busLvReport->report_year)
                            ->where('week_number', '<=', $busLvReport->week_number);
                    });
            })
            ->orderByDesc('report_year')
            ->orderByDesc('week_number')
            ->limit(3)
            ->get()
            ->sortBy(fn ($report) => sprintf('%04d%02d', $report->report_year, $report->week_number))
            ->values();

        $metrics = $this->buildMetrics($busLvReport, $previous, $recentLvReports);

        return view('fleet.bus-lv-reports.show', [
            'report' => $busLvReport,
            'metrics' => $metrics,
            'selectedDate' => optional($busLvReport->end_date)->format('Y-m-d'),
        ]);
    }

    public function edit(BusLvReport $busLvReport)
    {
        return view('fleet.bus-lv-reports.form', [
            'report' => $busLvReport,
            'dayLabels' => $this->dayLabels($busLvReport->start_date, $busLvReport->end_date),
        ]);
    }

    public function update(Request $request, BusLvReport $busLvReport)
    {
        $data = $this->validatedData($request, $busLvReport);
        $busLvReport->update($data);

        return redirect()->route('fleet.bus-lv-reports.show', $busLvReport)->with('success', 'Report Bus & LV berhasil diupdate.');
    }

    public function destroy(BusLvReport $busLvReport)
    {
        $busLvReport->delete();

        return redirect()->route('fleet.bus-lv-reports.index')->with('success', 'Report Bus & LV berhasil dihapus.');
    }

    protected function validatedData(Request $request, ?BusLvReport $report = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'company_name' => ['required', 'string', 'max:150'],
            'week_number' => [
                'required',
                'integer',
                'min:1',
                'max:53',
                Rule::unique('bus_lv_reports')
                    ->ignore($report?->id)
                    ->where(fn ($query) => $query->where('report_year', $request->integer('report_year'))),
            ],
            'report_year' => ['required', 'integer', 'min:2024', 'max:2100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'bus_unit_count_daily' => ['required', 'array', 'size:7'],
            'bus_unit_count_daily.*' => ['nullable', 'integer', 'min:0'],
            'bus_co_daily' => ['required', 'array', 'size:7'],
            'bus_co_daily.*' => ['nullable', 'integer', 'min:0'],
            'bus_ci_daily' => ['required', 'array', 'size:7'],
            'bus_ci_daily.*' => ['nullable', 'integer', 'min:0'],
            'bus_capacity_daily' => ['required', 'array', 'size:7'],
            'bus_capacity_daily.*' => ['nullable', 'integer', 'min:0'],
            'bus_extra_daily' => ['required', 'array', 'size:7'],
            'bus_extra_daily.*' => ['nullable', 'integer', 'min:0'],
            'lv_unit_count_daily' => ['required', 'array', 'size:7'],
            'lv_unit_count_daily.*' => ['nullable', 'integer', 'min:0'],
            'lv_co_daily' => ['required', 'array', 'size:7'],
            'lv_co_daily.*' => ['nullable', 'integer', 'min:0'],
            'lv_ci_daily' => ['required', 'array', 'size:7'],
            'lv_ci_daily.*' => ['nullable', 'integer', 'min:0'],
            'lv_capacity_daily' => ['required', 'array', 'size:7'],
            'lv_capacity_daily.*' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'array'],
            'notes.*' => ['nullable', 'string', 'max:255'],
        ]);

        return [
            'title' => $validated['title'],
            'company_name' => $validated['company_name'],
            'week_number' => $validated['week_number'],
            'report_year' => $validated['report_year'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'bus_unit_count_daily' => $this->normalizeDaily($validated['bus_unit_count_daily']),
            'bus_co_daily' => $this->normalizeDaily($validated['bus_co_daily']),
            'bus_ci_daily' => $this->normalizeDaily($validated['bus_ci_daily']),
            'bus_capacity_daily' => $this->normalizeDaily($validated['bus_capacity_daily']),
            'bus_extra_daily' => $this->normalizeDaily($validated['bus_extra_daily']),
            'lv_unit_count_daily' => $this->normalizeDaily($validated['lv_unit_count_daily']),
            'lv_co_daily' => $this->normalizeDaily($validated['lv_co_daily']),
            'lv_ci_daily' => $this->normalizeDaily($validated['lv_ci_daily']),
            'lv_capacity_daily' => $this->normalizeDaily($validated['lv_capacity_daily']),
            'notes' => collect($validated['notes'] ?? [])->map(fn ($note) => trim((string) $note))->pad(4, '')->take(4)->values()->all(),
        ];
    }

    protected function normalizeDaily(array $values): array
    {
        return collect($values)->map(fn ($value) => (int) ($value ?? 0))->pad(7, 0)->take(7)->values()->all();
    }

    protected function dayLabels($startDate, $endDate): array
    {
        $start = Carbon::parse($startDate);
        $labels = [];

        for ($i = 0; $i < 7; $i++) {
            $labels[] = $start->copy()->addDays($i)->format('d M');
        }

        return $labels;
    }

    protected function buildMetrics(BusLvReport $report, ?BusLvReport $previous, $recentLvReports): array
    {
        $busCo = collect($report->bus_co_daily ?? []);
        $busCi = collect($report->bus_ci_daily ?? []);
        $busExtra = collect($report->bus_extra_daily ?? []);
        $busUnits = collect($report->bus_unit_count_daily ?? []);
        $lvCo = collect($report->lv_co_daily ?? []);
        $lvCi = collect($report->lv_ci_daily ?? []);
        $lvUnits = collect($report->lv_unit_count_daily ?? []);
        $labels = $this->dayLabels($report->start_date, $report->end_date);

        $busCoTotal = (int) $busCo->sum();
        $busCiTotal = (int) $busCi->sum();
        $lvCoTotal = (int) $lvCo->sum();
        $lvCiTotal = (int) $lvCi->sum();

        $previousBusCoTotal = $previous ? (int) collect($previous->bus_co_daily ?? [])->sum() : 0;
        $previousBusCiTotal = $previous ? (int) collect($previous->bus_ci_daily ?? [])->sum() : 0;

        return [
            'labels' => $labels,
            'period_label' => Carbon::parse($report->start_date)->format('d') . ' - ' . Carbon::parse($report->end_date)->format('d M Y'),
            'sidebar' => [
                'bus_co_total' => $busCoTotal,
                'bus_co_avg' => round($busCoTotal / 7, 1),
                'bus_ci_total' => $busCiTotal,
                'bus_ci_avg' => round($busCiTotal / 7, 1),
                'bus_extra_total' => (int) $busExtra->sum(),
                'bus_extra_days' => $busExtra->filter(fn ($value) => (int) $value > 0)->count(),
                'bus_units_avg' => round($busUnits->avg() ?? 0, 1),
                'lv_co_total' => $lvCoTotal,
                'lv_ci_total' => $lvCiTotal,
                'lv_units_avg' => round($lvUnits->avg() ?? 0, 1),
            ],
            'daily_chart' => [
                'labels' => $labels,
                'co' => $busCo->values(),
                'ci' => $busCi->values(),
                'target' => collect($report->bus_capacity_daily ?? [])->map(fn ($v) => max(0, (int) $v / 2))->values(),
            ],
            'bus_compare_chart' => [
                'labels' => ['CO', 'CI'],
                'previous' => [$previousBusCoTotal, $previousBusCiTotal],
                'current' => [$busCoTotal, $busCiTotal],
                'previous_label' => $previous ? 'W' . $previous->week_number : 'Prev',
                'current_label' => 'W' . $report->week_number,
            ],
            'lv_trend_chart' => [
                'labels' => $recentLvReports->map(fn ($item) => 'W' . $item->week_number)->values(),
                'co' => $recentLvReports->map(fn ($item) => (int) collect($item->lv_co_daily ?? [])->sum())->values(),
                'ci' => $recentLvReports->map(fn ($item) => (int) collect($item->lv_ci_daily ?? [])->sum())->values(),
            ],
            'notes' => collect($report->notes ?? [])->pad(4, '')->take(4)->values(),
            'footer' => [
                'bus_co_delta' => $previous ? $busCoTotal - $previousBusCoTotal : null,
                'bus_ci_delta' => $previous ? $busCiTotal - $previousBusCiTotal : null,
            ],
        ];
    }
}
