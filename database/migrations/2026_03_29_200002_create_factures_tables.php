<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('devis_id')->nullable()->unique()->constrained('devis')->nullOnDelete();
            $table->string('numero_facture', 50)->unique();
            $table->date('date_emission');
            $table->date('date_echeance')->nullable();
            $table->enum('statut', ['brouillon', 'emise', 'envoyee', 'payee_partielle', 'payee', 'annulee', 'avoir'])->default('emise');
            $table->decimal('remise_globale_pct', 5, 2)->default(0);
            $table->decimal('taux_tva', 5, 2)->default(20);
            $table->string('conditions_paiement', 500)->nullable();
            $table->text('notes')->nullable();
            $table->text('objet')->nullable();
            $table->json('branding_snapshot')->nullable();
            $table->boolean('actif')->default(true);
            $table->unsignedBigInteger('user_insert')->nullable();
            $table->unsignedBigInteger('user_update')->nullable();
            $table->timestamps();
        });

        Schema::create('facture_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_id')->constrained('factures')->cascadeOnDelete();
            $table->enum('type_entree', ['libre', 'prestation', 'pack'])->default('libre');
            $table->unsignedBigInteger('catalogue_service_id')->nullable();
            $table->unsignedBigInteger('catalogue_pack_id')->nullable();
            $table->string('designation');
            $table->decimal('quantite', 12, 3)->default(1);
            $table->string('unite', 30)->default('pièce');
            $table->decimal('prix_unitaire_ht', 12, 2);
            $table->decimal('remise_pct', 5, 2)->default(0);
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facture_lignes');
        Schema::dropIfExists('factures');
    }
};
