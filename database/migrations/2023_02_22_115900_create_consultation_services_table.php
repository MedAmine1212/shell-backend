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
        Schema::create('consultation_services', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('consultation_id');
            $table->primary(['service_id', 'consultation_id']);
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
            $table->string("status",255)->default("");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_services');
    }
};
