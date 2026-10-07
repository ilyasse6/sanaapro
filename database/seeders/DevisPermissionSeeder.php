<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Permission;

class DevisPermissionSeeder extends Seeder
{
    /**
     * Créer les permissions pour le module "Gestion des Devis".
     * À exécuter après avoir ajouté le module (ModuleSeeder) sur une base existante :
     * php artisan db:seed --class=DevisPermissionSeeder
     */
    public function run(): void
    {
        $module = Module::where('nom', 'Gestion des Devis')->first();
        if (!$module) {
            $this->command->warn('Module "Gestion des Devis" introuvable. Exécutez d\'abord ModuleSeeder.');
            return;
        }

        $actions = ['consulter', 'ajouter', 'modifier', 'desactiver', 'activer'];
        foreach ($actions as $action) {
            Permission::firstOrCreate(
                [
                    'id_module' => $module->id,
                    'action' => $action,
                ],
                [
                    'nom' => ucfirst($action) . ' - ' . $module->nom,
                    'actif' => 1,
                    'user_insert' => 1,
                ]
            );
        }
        $this->command->info('Permissions "Gestion des Devis" créées ou déjà existantes.');
    }
}
