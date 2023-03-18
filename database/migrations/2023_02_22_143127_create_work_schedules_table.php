<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->integer("minimumConsultationTime")->default(30);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE stations
            ADD CONSTRAINT FOREIGN KEY (work_schedule_id) REFERENCES work_schedules(id) ON DELETE SET NULL;'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
