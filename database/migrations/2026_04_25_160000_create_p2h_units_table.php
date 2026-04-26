<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('p2h_units', function (Blueprint $table) {
            $table->id();
            $table->string('vendor')->nullable();
            $table->string('unit_code')->unique();
            $table->string('head')->nullable();
            $table->string('department')->nullable();
            $table->string('plate_no')->nullable();
            $table->string('pic_name')->nullable();
            $table->string('model_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p2h_units');
    }
};
