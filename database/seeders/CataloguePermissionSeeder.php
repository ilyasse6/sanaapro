<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Permission;

class CataloguePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $module = Module::where('nom', 'Gestion des Catalogues')->first();
        if (!$module) {
            $this->command->warn('Module "Gestion des Catalogues" introuvable. Exécutez d\'abord ModuleSeeder.');
            return;
        }
        $actions = ['consulter', 'ajouter', 'modifier', 'desactiver', 'activer'];
        foreach ($actions as $action) {
            Permission::firstOrCreate(
                ['id_module' => $module->id, 'action' => $action],
                ['nom' => ucfirst($action) . ' - ' . $module->nom, 'actif' => 1, 'user_insert' => 1]
            );
        }
        $this->command->info('Permissions "Gestion des Catalogues" créées ou déjà existantes.');
    }
}
