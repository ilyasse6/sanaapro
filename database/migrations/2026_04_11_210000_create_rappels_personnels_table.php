<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rappels_personnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('titre');
            $table->text('notes')->nullable();
            $table->date('date_rappel');
            $table->time('heure_rappel')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date_rappel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rappels_personnels');
    }
};
