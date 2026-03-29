<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fleet_unit_daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code');
            $table->date('report_date');
            $table->decimal('idle_hours', 8, 2)->default(0)->comment('Idle dalam jam');
            $table->decimal('hm_start', 10, 2)->nullable()->comment('Hour Meter awal');
            $table->decimal('hm_end', 10, 2)->nullable()->comment('Hour Meter akhir');
            $table->decimal('hm_usage', 8, 2)->nullable()->comment('Selisih HM');
            $table->decimal('distance_km', 10, 2)->default(0)->comment('Jarak tempuh KM');
            $table->decimal('ua_percent', 5, 2)->default(0)->comment('Unit Availability %');
            $table->decimal('standby_hours', 8, 2)->default(0)->comment('Standby dalam jam');
            $table->decimal('fuel_consumption', 10, 2)->default(0)->comment('Konsumsi BBM liter');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['unit_code', 'report_date']);
            $table->foreign('unit_code')->references('unit_code')->on('fleet_units')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fleet_unit_daily_metrics');
    }
};
