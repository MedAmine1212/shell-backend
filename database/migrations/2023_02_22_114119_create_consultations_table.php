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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->double("price")->nullable();
            $table->double("discount")->nullable();
            $table->integer("duration");
            $table->string("type");
            $table->integer("status")->default(-1);
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->timestamp('dateConsultation')->nullable();
            $table->timestamps();

            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles')
                ->onDelete('cascade');

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
