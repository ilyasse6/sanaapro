<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devis_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devis_id')->constrained('devis')->onDelete('cascade');
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
        Schema::dropIfExists('devis_lignes');
    }
};
