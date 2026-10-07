<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Entreprise;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class ProwiseModulesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'Gestion des Marchés & Cautions' => ['consulter', 'ajouter', 'modifier', 'desactiver', 'activer'],
            'Gestion des Fournisseurs' => ['consulter', 'ajouter', 'modifier', 'desactiver', 'activer'],
            'Gestion du Stock' => ['consulter', 'ajouter', 'modifier', 'desactiver', 'activer'],
            'Gestion des Factures' => ['consulter', 'ajouter', 'modifier', 'desactiver', 'activer'],
            'Gestion de la Comptabilité' => ['consulter', 'ajouter', 'modifier', 'desactiver', 'activer'],
            'Gestion des Employés & Congés' => ['consulter', 'ajouter', 'modifier', 'desactiver', 'activer'],
            'Alertes & Paramètres' => ['consulter', 'modifier'],
            'Suivi des déplacements' => ['consulter'],
        ];

        foreach ($modules as $nom => $actions) {
            $module = Module::firstOrCreate(
                ['nom' => $nom],
                ['actif' => 1, 'user_insert' => 1]
            );
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    [
                        'id_module' => $module->id,
                        'action' => $action,
                    ],
                    [
                        'nom' => ucfirst(str_replace('_', ' ', $action)).' — '.$nom,
                        'actif' => 1,
                        'user_insert' => 1,
                    ]
                );
            }
        }

        $eid = (int) Entreprise::query()->orderBy('id')->value('id');

        AppSetting::query()->firstOrCreate(
            ['cle' => 'alerte_caution_jours', 'id_entreprise' => $eid],
            ['valeur' => '30']
        );
        AppSetting::query()->firstOrCreate(
            ['cle' => 'alerte_facture_impayee_jours', 'id_entreprise' => $eid],
            ['valeur' => '7']
        );
        AppSetting::query()->firstOrCreate(
            ['cle' => 'alerte_stock_actif', 'id_entreprise' => $eid],
            ['valeur' => '1']
        );
        AppSetting::query()->firstOrCreate(
            ['cle' => 'devis_mention_tva', 'id_entreprise' => $eid],
            ['valeur' => 'TVA non applicable - Art. 293 B du CGI']
        );

        $this->command?->info('Modules et permissions Prowise : OK.');
    }
}
