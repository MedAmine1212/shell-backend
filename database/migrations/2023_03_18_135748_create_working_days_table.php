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
        Schema::create('working_days', function (Blueprint $table) {
            $table->id();
            $table->string("day", 10);
            $table->boolean("working")->default(true);
            $table->string("shiftStart", 10)->default("08:00");
            $table->string("shiftEnd", 10)->default("17:00");
            $table->boolean("pause")->default(true);
            $table->string("pauseStart", 10)->nullable();
            $table->string("pauseEnd", 10)->nullable();
            $table->unsignedBigInteger('work_schedule_id');
            $table->foreign('work_schedule_id')->references('id')->on('work_schedules')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_days');
    }
};
