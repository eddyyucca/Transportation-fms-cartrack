<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code');
            $table->date('scheduled_date')->comment('Tanggal jadwal PM');
            $table->date('completed_date')->nullable()->comment('Tanggal PM selesai');
            $table->enum('status', ['scheduled', 'done', 'overdue'])->default('scheduled');
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('notif_sent')->default(false);
            $table->timestamp('notif_sent_at')->nullable();
            $table->timestamps();

            $table->foreign('unit_code')->references('unit_code')->on('fleet_units')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_schedules');
    }
};
