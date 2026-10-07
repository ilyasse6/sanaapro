<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Permission;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = Module::all();
        $actions = ['ajouter', 'modifier', 'desactiver', 'activer', 'inactif', 'consulter','toutconsulter','consultermien'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::create([
                    'nom' => ucfirst($action) . ' - ' . $module->nom,
                    'action' => $action,
                    'actif' => 1,
                    'user_insert' => 1,
                    'id_module' => $module->id,
                ]);
            }
        }
    }
}
