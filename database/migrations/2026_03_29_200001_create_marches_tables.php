<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marches', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 80)->unique();
            $table->string('intitule');
            $table->text('objet')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->enum('statut', ['brouillon', 'en_cours', 'attribue', 'execution', 'cloture', 'annule'])->default('brouillon');
            $table->enum('decision_offre', ['en_attente', 'accepte', 'refuse'])->default('en_attente');
            $table->date('date_depart')->nullable();
            $table->date('date_ouverture')->nullable();
            $table->date('date_limite')->nullable();
            $table->dateTime('date_limite_plis')->nullable();
            $table->date('date_attribution')->nullable();
            $table->decimal('montant_estime', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->text('source_url')->nullable();
            $table->string('reference_consultation', 120)->nullable();
            $table->text('acheteur_public')->nullable();
            $table->string('type_annonce', 255)->nullable();
            $table->text('procedure_text')->nullable();
            $table->string('categorie_principale', 120)->nullable();
            $table->string('allotissement', 120)->nullable();
            $table->text('lieu_execution')->nullable();
            $table->text('domaines_activite')->nullable();
            $table->text('adresse_retrait_dossiers')->nullable();
            $table->text('adresse_depot_offres')->nullable();
            $table->text('lieu_ouverture_plis')->nullable();
            $table->decimal('prix_acquisition_plans', 14, 2)->nullable();
            $table->string('contact_nom', 255)->nullable();
            $table->string('contact_email', 255)->nullable();
            $table->string('contact_telephone', 80)->nullable();
            $table->string('contact_fax', 80)->nullable();
            $table->boolean('actif')->default(true);
            $table->unsignedBigInteger('user_insert')->nullable();
            $table->unsignedBigInteger('user_update')->nullable();
            $table->timestamps();
        });

        Schema::create('marche_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marche_id')->constrained('marches')->cascadeOnDelete();
            $table->string('code', 50)->nullable();
            $table->string('libelle');
            $table->decimal('montant', 14, 2)->nullable();
            $table->enum('statut', ['ouvert', 'attribue', 'en_cours', 'cloture'])->default('ouvert');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('marche_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marche_id')->constrained('marches')->cascadeOnDelete();
            $table->string('nom_fichier');
            $table->string('chemin');
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('taille_octets')->nullable();
            $table->string('type_document', 80)->nullable();
            $table->unsignedBigInteger('user_insert')->nullable();
            $table->timestamps();
        });

        Schema::create('cautions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marche_id')->constrained('marches')->cascadeOnDelete();
            $table->string('libelle');
            $table->enum('type_caution', ['provisoire', 'definitive', 'retenue_garantie', 'autre'])->default('provisoire');
            $table->decimal('montant', 14, 2);
            $table->string('organisme', 255)->nullable();
            $table->string('numero_garantie', 120)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->enum('statut', ['active', 'restituee', 'appelee', 'expiree'])->default('active');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_insert')->nullable();
            $table->unsignedBigInteger('user_update')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cautions');
        Schema::dropIfExists('marche_documents');
        Schema::dropIfExists('marche_lots');
        Schema::dropIfExists('marches');
    }
};
