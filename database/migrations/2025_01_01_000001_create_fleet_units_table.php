<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fleet_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code')->unique();
            $table->string('vendor')->nullable();
            $table->string('department')->nullable();
            $table->string('brand')->nullable();
            $table->string('type_model')->nullable();
            $table->string('registration')->nullable()->comment('Nomor polisi / ID di Cartrack');
            $table->boolean('is_monitored')->default(true)->comment('Apakah unit ini dimonitor di dashboard');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fleet_units');
    }
};
