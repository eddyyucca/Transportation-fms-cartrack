<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_check_report_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pm_check_report_id')->constrained('pm_check_reports')->cascadeOnDelete();
            $table->string('vendor_name');
            $table->string('theme')->default('default');
            $table->unsignedInteger('total_units')->default(0);
            $table->unsignedInteger('plan_units')->default(0);
            $table->unsignedInteger('actual_units')->default(0);
            $table->json('daily_actual')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_check_report_vendors');
    }
};
