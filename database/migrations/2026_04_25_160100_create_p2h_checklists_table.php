<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('p2h_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('p2h_unit_id')->constrained('p2h_units')->cascadeOnDelete();
            $table->date('checklist_date');
            $table->unsignedBigInteger('kilometer')->nullable();
            $table->boolean('safe_to_use')->default(true);
            $table->boolean('maintenance_required')->default(false);
            $table->string('created_by_name')->nullable();
            $table->string('source_type')->default('manual');
            $table->string('source_filename')->nullable();
            $table->timestamps();

            $table->unique(['p2h_unit_id', 'checklist_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p2h_checklists');
    }
};
