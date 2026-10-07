<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class BonCommandePermissionSeeder extends Seeder
{
    /**
     * Permissions du module « Gestion des Bons de commande ».
     * php artisan db:seed --class=BonCommandePermissionSeeder
     */
    public function run(): void
    {
        $module = Module::where('nom', 'Gestion des Bons de commande')->first();
        if (! $module) {
            $this->command?->warn('Module « Gestion des Bons de commande » introuvable. Exécutez ModuleSeeder ou ajoutez le module.');

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
                    'nom' => ucfirst($action).' — '.$module->nom,
                    'actif' => 1,
                    'user_insert' => 1,
                ]
            );
        }
        $this->command?->info('Permissions bons de commande : OK.');
    }
}
