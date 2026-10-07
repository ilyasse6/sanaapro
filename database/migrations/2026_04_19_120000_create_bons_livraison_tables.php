<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bons_livraison', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('numero', 50)->unique();
            $table->date('date_bl');
            $table->string('lieu', 255)->nullable();
            $table->string('numero_commande', 120)->nullable();
            $table->string('reference_client', 120)->nullable();
            $table->string('contact_client', 255)->nullable();
            $table->string('emis_par', 255)->nullable();
            $table->text('informations_additionnelles')->nullable();
            $table->date('recu_le')->nullable();
            $table->date('livre_le')->nullable();
            $table->enum('statut', ['brouillon', 'valide', 'annule'])->default('brouillon');
            $table->json('branding_snapshot')->nullable();
            $table->boolean('actif')->default(true);
            $table->unsignedBigInteger('user_insert')->nullable();
            $table->unsignedBigInteger('user_update')->nullable();
            $table->timestamps();
        });

        Schema::create('bon_livraison_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_livraison_id')->constrained('bons_livraison')->cascadeOnDelete();
            $table->string('reference_produit', 120)->nullable();
            $table->string('designation');
            $table->decimal('quantite', 12, 3)->default(1);
            $table->string('unite', 30)->default('pièce');
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bon_livraison_lignes');
        Schema::dropIfExists('bons_livraison');
    }
};
