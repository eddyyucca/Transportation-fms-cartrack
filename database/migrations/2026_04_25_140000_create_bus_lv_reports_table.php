<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_lv_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Bus & LV SCM');
            $table->string('company_name')->default('PT Sulawesi Cahaya Mineral');
            $table->unsignedSmallInteger('week_number');
            $table->unsignedSmallInteger('report_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->json('bus_co_daily');
            $table->json('bus_ci_daily');
            $table->json('bus_capacity_daily');
            $table->json('bus_extra_daily');
            $table->json('lv_co_daily');
            $table->json('lv_ci_daily');
            $table->json('lv_capacity_daily');
            $table->json('notes')->nullable();
            $table->timestamps();

            $table->unique(['report_year', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_lv_reports');
    }
};
