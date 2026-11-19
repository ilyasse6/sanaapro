<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreditChequePermissionSeeder extends Seeder
{
    /**
     * Module « Gestion des Crédits & Chèques » et ses permissions, accordées aux profils qui gèrent déjà les factures.
     * php artisan db:seed --class=CreditChequePermissionSeeder
     */
    public function run(): void
    {
        $module = Module::firstOrCreate(
            ['nom' => 'Gestion des Crédits & Chèques'],
            ['actif' => 1, 'user_insert' => 1]
        );

        $permissions = collect(['consulter', 'ajouter', 'modifier', 'desactiver', 'activer'])
            ->mapWithKeys(fn ($action) => [$action => Permission::firstOrCreate(
                ['id_module' => $module->id, 'action' => $action],
                ['nom' => ucfirst($action).' — '.$module->nom, 'actif' => 1, 'user_insert' => 1]
            )]);

        $moduleFactures = Module::where('nom', 'Gestion des Factures')->value('id');
        $profils = DB::table('profile_permissions')
            ->join('permissions', 'permissions.id', '=', 'profile_permissions.id_permission')
            ->where('permissions.id_module', $moduleFactures)
            ->where('profile_permissions.actif', 1)
            ->select('profile_permissions.id_profile', 'permissions.action', 'profile_permissions.id_entreprise')
            ->get();

        foreach ($profils as $p) {
            $permission = $permissions->get($p->action);
            if (! $permission) {
                continue;
            }
            $existe = DB::table('profile_permissions')
                ->where('id_profile', $p->id_profile)
                ->where('id_permission', $permission->id)
                ->exists();
            if (! $existe) {
                DB::table('profile_permissions')->insert([
                    'id_profile' => $p->id_profile,
                    'id_permission' => $permission->id,
                    'id_entreprise' => $p->id_entreprise,
                    'actif' => 1,
                    'user_insert' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command?->info('Permissions crédits & chèques : OK.');
    }
}
