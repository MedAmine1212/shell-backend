<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('firstName', 25)->default('');
            $table->string('lastName', 25)->default('');
            $table->string("phone",20)->default('')->unique();
            $table->string("barCode", 255)->nullable()->unique();
            $table->string("password", 255)->nullable()->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
