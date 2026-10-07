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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->string('action')->nullable();
            $table->boolean('actif')->default(1);
            $table->integer('user_insert')->nullable();
            $table->integer('user_update')->nullable();
            $table->unsignedBigInteger('id_module');
            $table->foreign('id_module')->references('id')->on('modules')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
