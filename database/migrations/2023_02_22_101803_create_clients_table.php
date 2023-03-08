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
        Schema::create('clients', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->boolean("validated")->default(false);
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('registeredAt')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
        DB::statement('ALTER TABLE clients
            ADD CONSTRAINT FOREIGN KEY (registeredAt) REFERENCES stations(id) on DELETE SET NULL;'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
