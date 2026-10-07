<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('raison_sociale');
            $table->string('ice')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone', 50)->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal', 20)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('actif')->default(true);
            $table->unsignedBigInteger('user_insert')->nullable();
            $table->unsignedBigInteger('user_update')->nullable();
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 80)->unique();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('unite', 30)->default('pièce');
            $table->decimal('prix_achat_ht', 12, 2)->default(0);
            $table->decimal('prix_vente_ht', 12, 2)->default(0);
            $table->decimal('stock_quantity', 14, 3)->default(0);
            $table->decimal('seuil_alerte', 14, 3)->default(0);
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->nullOnDelete();
            $table->foreignId('catalogue_service_id')->nullable()->constrained('catalogue_services')->nullOnDelete();
            $table->boolean('actif')->default(true);
            $table->unsignedBigInteger('user_insert')->nullable();
            $table->unsignedBigInteger('user_update')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_mouvements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->enum('type', ['entree', 'sortie', 'ajustement']);
            $table->decimal('quantite', 14, 3);
            $table->string('motif')->nullable();
            $table->string('reference_piece', 120)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_mouvements');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('fournisseurs');
    }
};
