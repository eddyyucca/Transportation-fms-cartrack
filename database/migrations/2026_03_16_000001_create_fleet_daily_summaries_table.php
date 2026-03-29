<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fleet_daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('report_date')->unique();
            $table->unsignedInteger('total_vehicles')->default(0);
            $table->unsignedInteger('total_trips')->default(0);
            $table->decimal('total_distance_km', 14, 2)->default(0);
            $table->decimal('total_engine_on_min', 14, 2)->default(0);
            $table->decimal('total_driving_min', 14, 2)->default(0);
            $table->decimal('total_idle_min', 14, 2)->default(0);
            $table->unsignedInteger('total_harsh_acceleration')->default(0);
            $table->unsignedInteger('total_harsh_braking')->default(0);
            $table->unsignedInteger('total_harsh_cornering')->default(0);
            $table->unsignedInteger('total_threshold_speeding')->default(0);
            $table->unsignedInteger('total_road_speeding')->default(0);
            $table->unsignedInteger('total_harsh_events')->default(0);
            $table->unsignedInteger('total_speeding_events')->default(0);
            $table->decimal('avg_idle_ratio', 8, 2)->default(0);
            $table->decimal('avg_utilization_ratio', 8, 2)->default(0);
            $table->decimal('avg_pa_score', 8, 2)->default(0);
            $table->decimal('avg_safety_score', 8, 2)->default(0);
            $table->decimal('avg_performance_score', 8, 2)->default(0);
            $table->json('raw_meta')->nullable();
            $table->timestamps();

            $table->index('report_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fleet_daily_summaries');
    }
};
