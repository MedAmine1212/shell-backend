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
        Schema::create('borne_advertisements', function (Blueprint $table) {
            $table->unsignedBigInteger('borne_id');
            $table->unsignedBigInteger('advertisement_id');
            $table->primary(['borne_id', 'advertisement_id']);
            $table->foreign('borne_id')->references('id')->on('bornes')->onDelete('cascade');
            $table->foreign('advertisement_id')->references('id')->on('advertisements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borne_advertisements');
    }
};
