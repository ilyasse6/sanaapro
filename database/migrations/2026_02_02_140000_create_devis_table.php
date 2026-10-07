<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('numero_devis', 50)->unique();
            $table->date('date_emission');
            $table->date('date_validite');
            $table->enum('statut', ['brouillon', 'envoye', 'accepte', 'refuse'])->default('brouillon');
            $table->decimal('remise_globale_pct', 5, 2)->default(0);
            $table->decimal('taux_tva', 5, 2)->default(20);
            $table->string('conditions_paiement', 500)->nullable();
            $table->text('notes')->nullable();
            $table->text('objet')->nullable();
            $table->json('branding_snapshot')->nullable();
            $table->boolean('actif')->default(1);
            $table->unsignedBigInteger('user_insert')->nullable();
            $table->unsignedBigInteger('user_update')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
