<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devis_lignes', function (Blueprint $table) {
            $table->decimal('largeur', 8, 3)->nullable()->after('designation');
            $table->decimal('hauteur', 8, 3)->nullable()->after('largeur');
        });
    }

    public function down(): void
    {
        Schema::table('devis_lignes', function (Blueprint $table) {
            $table->dropColumn(['largeur', 'hauteur']);
        });
    }
};
