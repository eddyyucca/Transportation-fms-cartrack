<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_check_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('PM Check Performance');
            $table->string('company_name')->default('PT Sulawesi Cahaya Mineral');
            $table->unsignedSmallInteger('week_number');
            $table->unsignedSmallInteger('report_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['report_year', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_check_reports');
    }
};
