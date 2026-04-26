<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bus_lv_reports', function (Blueprint $table) {
            $table->json('bus_unit_count_daily')->nullable()->after('end_date');
            $table->json('lv_unit_count_daily')->nullable()->after('bus_extra_daily');
        });
    }

    public function down(): void
    {
        Schema::table('bus_lv_reports', function (Blueprint $table) {
            $table->dropColumn(['bus_unit_count_daily', 'lv_unit_count_daily']);
        });
    }
};
