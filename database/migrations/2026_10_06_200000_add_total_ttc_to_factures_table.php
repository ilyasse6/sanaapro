<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->decimal('total_ttc', 12, 2)->nullable()->after('taux_tva');
        });

        Schema::table('facture_lignes', function (Blueprint $table) {
            $table->text('designation')->change();
        });
    }

    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn('total_ttc');
        });

        Schema::table('facture_lignes', function (Blueprint $table) {
            $table->string('designation')->change();
        });
    }
};
