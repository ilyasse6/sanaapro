<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bons_commande', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->cascadeOnDelete();
            $table->string('numero', 50)->unique();
            $table->date('date_bc');
            $table->string('lieu_livraison', 255)->nullable();
            $table->string('reference_interne', 120)->nullable();
            $table->string('reference_fournisseur', 120)->nullable();
            $table->string('contact_fournisseur', 255)->nullable();
            $table->string('emis_par', 255)->nullable();
            $table->text('informations_additionnelles')->nullable();
            $table->date('date_livraison_souhaitee')->nullable();
            $table->enum('statut', ['brouillon', 'valide', 'annule'])->default('brouillon');
            $table->json('branding_snapshot')->nullable();
            $table->boolean('actif')->default(true);
            $table->unsignedBigInteger('user_insert')->nullable();
            $table->unsignedBigInteger('user_update')->nullable();
            $table->timestamps();
        });

        Schema::create('bon_commande_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_commande_id')->constrained('bons_commande')->cascadeOnDelete();
            $table->string('reference_produit', 120)->nullable();
            $table->string('designation');
            $table->decimal('quantite', 12, 3)->default(1);
            $table->string('unite', 30)->default('pièce');
            $table->decimal('prix_unitaire_ht', 12, 2)->nullable();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bon_commande_lignes');
        Schema::dropIfExists('bons_commande');
    }
};
