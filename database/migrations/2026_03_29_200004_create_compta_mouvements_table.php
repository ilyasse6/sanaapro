<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compta_mouvements', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['entree', 'depense', 'paiement_client', 'paiement_fournisseur', 'credit', 'salaire']);
            $table->decimal('montant', 14, 2);
            $table->date('date_operation');
            $table->string('libelle');
            $table->string('mode_paiement', 50)->nullable();
            $table->string('reference_externe', 120)->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->nullOnDelete();
            $table->foreignId('facture_id')->nullable()->constrained('factures')->nullOnDelete();
            $table->foreignId('employe_id')->nullable()->constrained('employes')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_insert')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compta_mouvements');
    }
};
