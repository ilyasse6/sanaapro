<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tables métier à isoler par entreprise (SaaS multi-tenant). */
    private array $tenantTables = [
        'clients',
        'diagnostics',
        'catalogue_services',
        'catalogue_packs',
        'rendez_vous',
        'devis',
        'fournisseurs',
        'articles',
        'stock_mouvements',
        'marches',
        'cautions',
        'factures',
        'employes',
        'conges',
        'compta_mouvements',
        'rappels_personnels',
        'bons_livraison',
        'bons_commande',
    ];

    public function up(): void
    {
        $firstEntrepriseId = (int) DB::table('entreprises')->orderBy('id')->value('id');
        if ($firstEntrepriseId < 1) {
            $firstEntrepriseId = (int) DB::table('entreprises')->insertGetId([
                'abbreviation' => 'TadbirPro',
                'description' => 'TadbirPro',
                'type' => 'SARL',
                'actif' => 1,
                'user_insert' => null,
                'user_update' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($this->tenantTables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->unsignedBigInteger('id_entreprise')->nullable()->after('id');
            });
            DB::table($table)->whereNull('id_entreprise')->update(['id_entreprise' => $firstEntrepriseId]);
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->unsignedBigInteger('id_entreprise')->nullable(false)->change();
                $blueprint->foreign('id_entreprise')->references('id')->on('entreprises')->cascadeOnDelete();
            });
        }

        Schema::table('app_settings', function (Blueprint $blueprint) {
            $blueprint->dropUnique(['cle']);
        });

        Schema::table('app_settings', function (Blueprint $blueprint) {
            $blueprint->unsignedBigInteger('id_entreprise')->nullable()->after('id');
        });

        DB::table('app_settings')->whereNull('id_entreprise')->update(['id_entreprise' => $firstEntrepriseId]);

        Schema::table('app_settings', function (Blueprint $blueprint) {
            $blueprint->unsignedBigInteger('id_entreprise')->nullable(false)->change();
            $blueprint->foreign('id_entreprise')->references('id')->on('entreprises')->cascadeOnDelete();
            $blueprint->unique(['id_entreprise', 'cle']);
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['id_entreprise']);
            $blueprint->dropUnique(['id_entreprise', 'cle']);
        });

        Schema::table('app_settings', function (Blueprint $blueprint) {
            $blueprint->dropColumn('id_entreprise');
        });

        Schema::table('app_settings', function (Blueprint $blueprint) {
            $blueprint->string('cle', 120)->unique()->change();
        });

        foreach (array_reverse($this->tenantTables) as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropForeign(['id_entreprise']);
                $blueprint->dropIndex(['id_entreprise']);
                $blueprint->dropColumn('id_entreprise');
            });
        }
    }
};
