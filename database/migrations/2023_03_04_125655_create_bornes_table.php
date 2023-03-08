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
        Schema::create('bornes', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->boolean("status");
            $table->timestamp("lastHeartBeat")->nullable();
            $table->integer("heartBeatInterval")->default(5);
            $table->unsignedBigInteger('station_id')->nullable();
            $table->timestamps();
            $table->foreign('station_id')
                ->references('id')
                ->on('stations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bornes');
    }
};
