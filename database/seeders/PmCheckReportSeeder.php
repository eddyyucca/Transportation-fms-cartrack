<?php

namespace Database\Seeders;

use App\Models\PmCheckReport;
use App\Models\PmCheckReportVendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PmCheckReportSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        PmCheckReportVendor::truncate();
        PmCheckReport::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $reports = [
            [
                'week_number' => 12,
                'report_year' => 2026,
                'start_date' => '2026-03-15',
                'end_date' => '2026-03-21',
                'vendors' => [
                    ['vendor_name' => 'Bagong', 'theme' => 'bagong', 'total_units' => 33, 'plan_units' => 14, 'actual_units' => 14, 'daily_actual' => [2, 2, 2, 2, 2, 2, 2]],
                    ['vendor_name' => 'Transkon', 'theme' => 'transkon', 'total_units' => 26, 'plan_units' => 6, 'actual_units' => 4, 'daily_actual' => [1, 1, 1, 0, 1, 0, 0]],
                    ['vendor_name' => 'Trac', 'theme' => 'trac', 'total_units' => 111, 'plan_units' => 16, 'actual_units' => 13, 'daily_actual' => [2, 2, 2, 2, 2, 2, 1]],
                ],
            ],
            [
                'week_number' => 13,
                'report_year' => 2026,
                'start_date' => '2026-03-22',
                'end_date' => '2026-03-28',
                'vendors' => [
                    ['vendor_name' => 'Bagong', 'theme' => 'bagong', 'total_units' => 33, 'plan_units' => 14, 'actual_units' => 14, 'daily_actual' => [2, 2, 2, 2, 2, 2, 2]],
                    ['vendor_name' => 'Transkon', 'theme' => 'transkon', 'total_units' => 26, 'plan_units' => 12, 'actual_units' => 10, 'daily_actual' => [1, 2, 1, 2, 1, 1, 2]],
                    ['vendor_name' => 'Trac', 'theme' => 'trac', 'total_units' => 111, 'plan_units' => 9, 'actual_units' => 8, 'daily_actual' => [1, 1, 1, 1, 1, 1, 2]],
                ],
            ],
            [
                'week_number' => 14,
                'report_year' => 2026,
                'start_date' => '2026-03-29',
                'end_date' => '2026-04-04',
                'vendors' => [
                    ['vendor_name' => 'Bagong', 'theme' => 'bagong', 'total_units' => 33, 'plan_units' => 14, 'actual_units' => 14, 'daily_actual' => [2, 2, 2, 2, 2, 2, 2]],
                    ['vendor_name' => 'Transkon', 'theme' => 'transkon', 'total_units' => 26, 'plan_units' => 10, 'actual_units' => 10, 'daily_actual' => [1, 2, 1, 1, 2, 2, 1]],
                    ['vendor_name' => 'Trac', 'theme' => 'trac', 'total_units' => 111, 'plan_units' => 9, 'actual_units' => 8, 'daily_actual' => [1, 1, 1, 1, 1, 1, 2]],
                ],
            ],
            [
                'week_number' => 15,
                'report_year' => 2026,
                'start_date' => '2026-04-05',
                'end_date' => '2026-04-11',
                'vendors' => [
                    ['vendor_name' => 'Bagong', 'theme' => 'bagong', 'total_units' => 33, 'plan_units' => 14, 'actual_units' => 14, 'daily_actual' => [2, 2, 2, 2, 2, 2, 2]],
                    ['vendor_name' => 'Transkon', 'theme' => 'transkon', 'total_units' => 26, 'plan_units' => 15, 'actual_units' => 14, 'daily_actual' => [2, 2, 2, 2, 2, 2, 2]],
                    ['vendor_name' => 'Trac', 'theme' => 'trac', 'total_units' => 111, 'plan_units' => 20, 'actual_units' => 19, 'daily_actual' => [3, 3, 3, 3, 2, 2, 3]],
                ],
            ],
            [
                'week_number' => 16,
                'report_year' => 2026,
                'start_date' => '2026-04-12',
                'end_date' => '2026-04-18',
                'vendors' => [
                    ['vendor_name' => 'Bagong', 'theme' => 'bagong', 'total_units' => 33, 'plan_units' => 8, 'actual_units' => 8, 'daily_actual' => [1, 1, 1, 1, 1, 1, 2]],
                    ['vendor_name' => 'Transkon', 'theme' => 'transkon', 'total_units' => 26, 'plan_units' => 8, 'actual_units' => 8, 'daily_actual' => [1, 1, 1, 1, 1, 1, 2]],
                    ['vendor_name' => 'Trac', 'theme' => 'trac', 'total_units' => 111, 'plan_units' => 30, 'actual_units' => 29, 'daily_actual' => [4, 4, 4, 4, 4, 3, 6]],
                ],
            ],
            [
                'week_number' => 17,
                'report_year' => 2026,
                'start_date' => '2026-04-19',
                'end_date' => '2026-04-25',
                'vendors' => [
                    ['vendor_name' => 'Bagong', 'theme' => 'bagong', 'total_units' => 33, 'plan_units' => 0, 'actual_units' => 0, 'daily_actual' => [0, 0, 0, 0, 0, 0, 0]],
                    ['vendor_name' => 'Transkon', 'theme' => 'transkon', 'total_units' => 26, 'plan_units' => 0, 'actual_units' => 0, 'daily_actual' => [0, 0, 0, 0, 0, 0, 0]],
                    ['vendor_name' => 'Trac', 'theme' => 'trac', 'total_units' => 111, 'plan_units' => 0, 'actual_units' => 0, 'daily_actual' => [0, 0, 0, 0, 0, 0, 0]],
                ],
            ],
        ];

        foreach ($reports as $reportData) {
            $report = PmCheckReport::create([
                'title' => 'PM Check Performance',
                'company_name' => 'PT Sulawesi Cahaya Mineral',
                'week_number' => $reportData['week_number'],
                'report_year' => $reportData['report_year'],
                'start_date' => $reportData['start_date'],
                'end_date' => $reportData['end_date'],
                'notes' => 'Seed reset sesuai kebutuhan trend W12-W16, W17 kosong.',
            ]);

            $vendors = collect($reportData['vendors'])->values()->map(function (array $vendor, int $index) {
                return [
                    ...$vendor,
                    'sort_order' => $index + 1,
                ];
            })->all();

            $report->vendorReports()->createMany($vendors);
        }
    }
}
