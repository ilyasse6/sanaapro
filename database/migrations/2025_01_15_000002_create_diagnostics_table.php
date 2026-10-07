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
        Schema::create('diagnostics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            
            // Informations de base
            $table->boolean('deja_fait_laser')->default(0);
            $table->string('autre_centre')->nullable();
            $table->integer('nb_seances_anterieures')->nullable();
            
            // Diagnostic médical
            $table->string('type_peau')->nullable();
            $table->text('antecedents_medicaux')->nullable();
            $table->text('medications_cours')->nullable();
            $table->text('allergies')->nullable();
            $table->boolean('grossesse_allaitement')->default(0);
            $table->text('zones_traiter')->nullable();
            $table->string('zone_cible')->nullable();
            $table->text('contre_indications')->nullable();
            $table->boolean('consentement_eclaire')->default(0);
            
            // Rendez-vous et traitement
            $table->date('date_prise_rdv')->nullable();
            $table->time('heure_rdv')->nullable();
            $table->string('type_traitement')->nullable();
            $table->integer('nb_seances_prevues')->nullable();
            
            // Notes
            $table->text('notes_diagnostic')->nullable();
            
            // Métadonnées
            $table->boolean('actif')->default(1);
            $table->unsignedBigInteger('user_insert')->nullable();
            $table->unsignedBigInteger('user_update')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostics');
    }
};

