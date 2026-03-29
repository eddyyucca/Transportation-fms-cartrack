<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fleet_vehicle_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->string('registration', 100);
            $table->unsignedInteger('total_trips')->default(0);
            $table->decimal('distance_km', 14, 2)->default(0);
            $table->decimal('total_engine_on_min', 14, 2)->default(0);
            $table->decimal('total_driving_min', 14, 2)->default(0);
            $table->decimal('total_idle_min', 14, 2)->default(0);
            $table->unsignedInteger('harsh_acceleration')->default(0);
            $table->unsignedInteger('harsh_braking')->default(0);
            $table->unsignedInteger('harsh_cornering')->default(0);
            $table->unsignedInteger('total_harsh_events')->default(0);
            $table->unsignedInteger('threshold_speeding')->default(0);
            $table->unsignedInteger('road_speeding')->default(0);
            $table->unsignedInteger('total_speeding_events')->default(0);
            $table->decimal('idle_ratio', 8, 2)->default(0);
            $table->decimal('utilization_ratio', 8, 2)->default(0);
            $table->decimal('pa_score', 8, 2)->default(0);
            $table->decimal('safety_score', 8, 2)->default(0);
            $table->decimal('performance_score', 8, 2)->default(0);
            $table->string('status', 30)->default('Good');
            $table->timestamps();

            $table->unique(['report_date', 'registration']);
            $table->index(['report_date', 'performance_score']);
            $table->index(['report_date', 'pa_score']);
            $table->index(['report_date', 'idle_ratio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fleet_vehicle_daily_stats');
    }
};
