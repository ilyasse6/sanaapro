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
        Schema::create('profile_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_profile');
            $table->foreign('id_profile')->references('id')->on('profiles')->onDelete('cascade');
            $table->unsignedBigInteger('id_permission')->nullable();
            $table->foreign('id_permission')->references('id')->on('permissions')->onDelete('cascade');
            $table->unsignedBigInteger('id_entreprise')->nullable();
            $table->foreign('id_entreprise')->references('id')->on('entreprises')->onDelete('cascade');
            $table->boolean('actif')->default(1);
            $table->dateTime('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();
            $table->integer('user_insert')->nullable();
            $table->integer('user_update')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_permissions');
    }
};
