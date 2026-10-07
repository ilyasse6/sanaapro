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
        Schema::create('entreprises', function (Blueprint $table) {
            $table->id('id');
            $table->string('abbreviation');
            $table->string('description');
            $table->enum('type', [
                'SARL',
                'SARL_AU',
                'SA',
                'SAS',
                'SNC',
                'SCS',
                'GIE',
                'COOPERATIVE',
                'ASSOCIATION',
                'AUTO_ENTREPRENEUR',
            ])->default('SARL');
            $table->boolean('actif')->default(1);
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
        Schema::dropIfExists('entreprises');
    }
};
