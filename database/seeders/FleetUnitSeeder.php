<?php

namespace Database\Seeders;

use App\Models\FleetUnit;
use Illuminate\Database\Seeder;

class FleetUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['unit_code' => 'SCM LV 01', 'vendor' => 'Trac', 'department' => 'Management', 'brand' => 'Toyota Hilux', 'type_model' => 'SUV'],
            ['unit_code' => 'SCM LV 10', 'vendor' => 'Trac', 'department' => 'Enviroment', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 011', 'vendor' => 'Trac', 'department' => 'Mine Operation', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 012', 'vendor' => 'Trac', 'department' => 'Mine Enggineering', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 13', 'vendor' => 'Trac', 'department' => 'Mine Enggineering', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 14', 'vendor' => 'Trac', 'department' => 'Commercial -IT', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 15', 'vendor' => 'Trac', 'department' => 'General Facility & Maintenance', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 16', 'vendor' => 'Trac', 'department' => 'Mine Operation-Survey', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 17', 'vendor' => 'Trac', 'department' => 'Road Maintenance', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 18', 'vendor' => 'Trac', 'department' => 'OHS-ERT', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 19', 'vendor' => 'Trac', 'department' => 'Mine Develovment', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 20', 'vendor' => 'Trac', 'department' => 'Exploration-Drilling', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 21', 'vendor' => 'Trac', 'department' => 'Exploration Geophysics', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 22', 'vendor' => 'Trac', 'department' => 'Ore Transport Hauling', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 23', 'vendor' => 'Trac', 'department' => 'Mine Develovment', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 24', 'vendor' => 'Trac', 'department' => 'OHS-Safety', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 25', 'vendor' => 'Trac', 'department' => 'Mine Develovment', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 26', 'vendor' => 'Trac', 'department' => 'Exploration-Drilling', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 27', 'vendor' => 'Trac', 'department' => 'Exploration-Drilling', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 28', 'vendor' => 'Trac', 'department' => 'Mine Operation-Survey', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 29', 'vendor' => 'Trac', 'department' => 'Production geology-QA', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 31', 'vendor' => 'Trac', 'department' => 'Mine Operation-Survey', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 32', 'vendor' => 'Trac', 'department' => 'HRGA Operations', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 33', 'vendor' => 'Trac', 'department' => 'Ore Transport Hauling', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 34', 'vendor' => 'Trac', 'department' => 'HRGA Operations', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 35', 'vendor' => 'Trac', 'department' => 'Strategic Project & Infrastructure', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM LV 36', 'vendor' => 'Trac', 'department' => 'HRGA Operations', 'brand' => 'Toyota Hilux', 'type_model' => 'Double Cabin'],
            ['unit_code' => 'SCM BUS 17', 'vendor' => 'Bagong', 'department' => 'HRGA SCM', 'brand' => 'MITSUBISHI CENTER FE 84', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'SCM BUS 10', 'vendor' => 'Bagong', 'department' => 'HRGA SCM', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'SCM BUS 11', 'vendor' => 'Bagong', 'department' => 'HRGA SCM', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'SCM BUS 12', 'vendor' => 'Bagong', 'department' => 'MAIN DEVELOPMENT', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'SCM BUS 13', 'vendor' => 'Bagong', 'department' => 'HRGA SCM', 'brand' => 'MITSUBISHI CANTER FE 84', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'SCM BUS 14', 'vendor' => 'Bagong', 'department' => 'HRGA SCM', 'brand' => 'MITSUBISHI CANTER FE 84', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'SCM BUS 15', 'vendor' => 'Bagong', 'department' => 'HRGA SCM', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'SCM BUS 16', 'vendor' => 'Bagong', 'department' => 'HRGA SCM', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'SCM MH 001', 'vendor' => 'Bagong', 'department' => 'MAIN DEVELOPMENT', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'ManHaul'],
            ['unit_code' => 'SCM MH 002', 'vendor' => 'Bagong', 'department' => 'MAIN DEVELOPMENT', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'ManHaul'],
            ['unit_code' => 'SCM MH 003', 'vendor' => 'Bagong', 'department' => 'MAIN DEVELOPMENT', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'ManHaul'],
            ['unit_code' => 'MMS BG 01', 'vendor' => 'Bagong', 'department' => 'INFRA', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'MMS BG 02', 'vendor' => 'Bagong', 'department' => 'EARTWORK', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'MMS BM 03', 'vendor' => 'Bagong', 'department' => 'EARTWORK', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'MMS BM 04', 'vendor' => 'Bagong', 'department' => 'INFRA', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'MMS BG 05', 'vendor' => 'Bagong', 'department' => 'INFRA', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'MMS BG 06', 'vendor' => 'Bagong', 'department' => 'EARTWORK', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'MMS BG 07', 'vendor' => 'Bagong', 'department' => 'HRGA SCM', 'brand' => 'MITSUBISHI CENTER FE 71 L', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'MMS BG 08', 'vendor' => 'Bagong', 'department' => 'EARTWORK', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'MMS BG 09', 'vendor' => 'Bagong', 'department' => 'EARTWORK', 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'BUS Medium'],
            ['unit_code' => 'MMS MH 001', 'vendor' => 'Bagong', 'department' => null, 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'ManHaul'],
            ['unit_code' => 'MMS MH 002', 'vendor' => 'Bagong', 'department' => null, 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'ManHaul'],
            ['unit_code' => 'MMS MH 003', 'vendor' => 'Bagong', 'department' => null, 'brand' => 'MITSUBISHI CENTER FE 74', 'type_model' => 'ManHaul'],
            ['unit_code' => 'SCM AM 02', 'vendor' => 'MUK3', 'department' => 'Klinik', 'brand' => 'Toyota', 'type_model' => 'Ambulance'],
            ['unit_code' => 'SCM AM 03', 'vendor' => 'MUK3', 'department' => 'Klinik', 'brand' => 'Mitsubishi', 'type_model' => 'Ambulance'],
            ['unit_code' => 'SCM AM 04', 'vendor' => 'MUK3', 'department' => 'Klinik', 'brand' => 'Mitsubishi', 'type_model' => 'Ambulance'],
            ['unit_code' => 'SCM AM 01', 'vendor' => 'SCM', 'department' => 'Pioneer 23', 'brand' => null, 'type_model' => 'Ambulance'],
            ['unit_code' => 'Anoa 1', 'vendor' => 'SCM', 'department' => 'OHS-ERT', 'brand' => null, 'type_model' => 'ERT Car'],
            ['unit_code' => 'Anoa 2', 'vendor' => 'SCM', 'department' => 'OHS-ERT', 'brand' => null, 'type_model' => 'ERT Car'],
            ['unit_code' => 'INNOVA Morowali', 'vendor' => 'SCM', 'department' => 'External Affair', 'brand' => 'Toyota Innova', 'type_model' => 'MPV'],
        ];

        foreach ($units as $unit) {
            FleetUnit::updateOrCreate(
                ['unit_code' => $unit['unit_code']],
                array_merge($unit, ['is_monitored' => true])
            );
        }

        $this->command->info('FleetUnit seeded: ' . count($units) . ' units.');
    }
}
