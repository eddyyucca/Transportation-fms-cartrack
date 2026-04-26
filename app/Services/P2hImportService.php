<?php

namespace App\Services;

use App\Models\P2hChecklist;
use App\Models\P2hUnit;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class P2hImportService
{
    public function importMasterWorkbook(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('P2h Online Rekap Apr 26') ?? $spreadsheet->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $imported = 0;

        for ($row = 6; $row <= $highestRow; $row++) {
            $unitCode = $this->clean((string) $sheet->getCell('C' . $row)->getFormattedValue());
            if ($unitCode === '') {
                continue;
            }

            P2hUnit::updateOrCreate(
                ['unit_code' => $unitCode],
                [
                    'vendor' => $this->clean((string) $sheet->getCell('B' . $row)->getFormattedValue()),
                    'head' => $this->clean((string) $sheet->getCell('D' . $row)->getFormattedValue()),
                    'department' => $this->clean((string) $sheet->getCell('E' . $row)->getFormattedValue()),
                    'plate_no' => $this->clean((string) $sheet->getCell('F' . $row)->getFormattedValue()),
                    'pic_name' => $this->clean((string) $sheet->getCell('G' . $row)->getFormattedValue()),
                    'model_name' => $this->clean((string) $sheet->getCell('H' . $row)->getFormattedValue()),
                    'is_active' => true,
                ]
            );

            $imported++;
        }

        return ['imported' => $imported];
    }

    public function importChecklistWorkbook(string $path, string $originalName): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $imported = 0;
        $skipped = 0;
        $notInMaster = 0;
        $spareUnits = 0;

        for ($row = 2; $row <= $highestRow; $row++) {
            $dateValue = $sheet->getCell('A' . $row)->getValue();
            $unitCode = $this->clean((string) $sheet->getCell('B' . $row)->getFormattedValue());

            if (($dateValue === null || $dateValue === '') || $unitCode === '') {
                $skipped++;
                continue;
            }

            $unit = P2hUnit::query()
                ->where('unit_code', $unitCode)
                ->where('is_active', true)
                ->first();

            if (! $unit) {
                $notInMaster++;
                $skipped++;
                continue;
            }

            $department = $this->clean((string) $unit->department);
            if ($department === '' || strcasecmp($department, 'Unassigned') === 0) {
                $spareUnits++;
                $skipped++;
                continue;
            }

            $date = $this->parseChecklistDate($dateValue);
            if ($date === null) {
                $skipped++;
                continue;
            }

            P2hChecklist::updateOrCreate(
                [
                    'p2h_unit_id' => $unit->id,
                    'checklist_date' => $date,
                ],
                [
                    'kilometer' => $this->toInt((string) $sheet->getCell('C' . $row)->getFormattedValue()),
                    'safe_to_use' => strtolower($this->clean((string) $sheet->getCell('D' . $row)->getFormattedValue())) === 'yes',
                    'maintenance_required' => strtolower($this->clean((string) $sheet->getCell('E' . $row)->getFormattedValue())) === 'yes',
                    'created_by_name' => $this->clean((string) $sheet->getCell('F' . $row)->getFormattedValue()),
                    'source_type' => 'upload',
                    'source_filename' => $originalName,
                ]
            );

            $imported++;
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'not_in_master' => $notInMaster,
            'spare_units' => $spareUnits,
        ];
    }

    protected function clean(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }

    protected function toInt(string $value): ?int
    {
        $digits = preg_replace('/[^\d]/', '', $value);

        return $digits === '' ? null : (int) $digits;
    }

    protected function parseChecklistDate(mixed $value): ?string
    {
        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        $value = $this->clean((string) $value);
        if ($value === '') {
            return null;
        }

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Throwable $exception) {
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $exception) {
            return null;
        }
    }
}
