<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('credit_initial', 14, 2)->default(0)->after('notes');
        });

        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_entreprise');
            $table->foreign('id_entreprise')->references('id')->on('entreprises')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('facture_id')->nullable()->constrained('factures')->nullOnDelete();
            $table->foreignId('compta_mouvement_id')->nullable()->constrained('compta_mouvements')->nullOnDelete();
            $table->string('numero', 50);
            $table->string('banque', 120)->nullable();
            $table->string('tireur', 191)->nullable();
            $table->decimal('montant', 14, 2);
            $table->date('date_reception');
            $table->date('date_echeance')->nullable();
            $table->enum('statut', ['en_portefeuille', 'remis', 'encaisse', 'impaye', 'restitue'])->default('en_portefeuille');
            $table->date('date_remise')->nullable();
            $table->date('date_encaissement')->nullable();
            $table->date('date_impaye')->nullable();
            $table->string('motif_impaye', 255)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_insert')->nullable();
            $table->unsignedBigInteger('user_update')->nullable();
            $table->timestamps();

            $table->index(['id_entreprise', 'statut', 'date_echeance']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cheques');
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('credit_initial');
        });
    }
};
