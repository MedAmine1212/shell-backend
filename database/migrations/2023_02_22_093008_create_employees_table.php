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
        Schema::create('employees', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->boolean('active')->default(true);
            $table->boolean('onConsultation')->default(false);
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('station_id')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('station_id')
                ->references('id')
                ->on('stations')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
