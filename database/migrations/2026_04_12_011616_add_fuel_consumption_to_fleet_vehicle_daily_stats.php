<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fleet_vehicle_daily_stats', function (Blueprint $table) {
            $table->decimal('fuel_consumption', 10, 2)->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('fleet_vehicle_daily_stats', function (Blueprint $table) {
            $table->dropColumn('fuel_consumption');
        });
    }
};
