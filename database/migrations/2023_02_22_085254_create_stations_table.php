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
    public function up()
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('label', 255);
            $table->string('address', 255);
            $table->unsignedBigInteger('station_admin_id')->nullable();
            $table->unsignedBigInteger('work_schedule_id')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('stations');
    }
};
