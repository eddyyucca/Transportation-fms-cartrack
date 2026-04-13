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
        Schema::table('fleet_units', function (Blueprint $table) {
            $table->decimal('initial_hm', 10, 2)->default(0)->after('is_monitored')
                ->comment('HM awal sebagai titik mulai akumulasi engine hours');
        });
    }

    public function down(): void
    {
        Schema::table('fleet_units', function (Blueprint $table) {
            $table->dropColumn('initial_hm');
        });
    }
};
