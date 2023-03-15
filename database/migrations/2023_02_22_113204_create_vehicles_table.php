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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string("matricule", 10)->unique();
            $table->string("brand", 50);
            $table->string("model", 50);
            $table->integer("year");
            $table->string("fuelType", 50);
            $table->bigInteger("mileage");
            $table->timestamp('lastOilChange');
            $table->unsignedBigInteger('client_id')->nullable();

            $table->timestamps();

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
