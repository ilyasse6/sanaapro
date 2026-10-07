<?php

namespace Database\Seeders;

use App\Models\Entreprise;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Profile;
use App\Models\ProfilePermission;
use Illuminate\Database\Seeder;

class ProfilePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Profile::where('nom', 'Super Admin')->first();
        if (! $superAdmin) {
            $this->command->error('Profil Super Admin introuvable. Exécutez ProfileSeeder.');

            return;
        }

        $this->seedSuperAdmin($superAdmin->id);

        $etab = Entreprise::orderBy('id')->first();
        if (! $etab) {
            $this->command->error('Aucune entreprise. Exécutez EntrepriseSeeder.');

            return;
        }

        $directeur = Profile::where('nom', 'Directeur')->first();
        if ($directeur) {
            $this->seedDirecteurEntreprise($directeur->id, $etab);
        }

        /** @var array<string, array<int, array{0: string, 1: array<string>|null}>> */
        $map = [
            'Comptable' => [
                ['Gestion de la Comptabilité', null],
                ['Gestion des Factures', null],
                ['Gestion des Clients', ['consulter', 'toutconsulter', 'consultermien']],
                ['Gestion des Devis', ['consulter', 'toutconsulter', 'consultermien']],
                ['Gestion des Bons de livraison', ['consulter', 'toutconsulter', 'consultermien']],
                ['Gestion des Bons de commande', ['consulter', 'toutconsulter', 'consultermien']],
                ['Gestion des Marchés & Cautions', ['consulter']],
                ['Gestion des Catalogues', ['consulter', 'toutconsulter', 'consultermien']],
            ],
        ];

        foreach ($map as $profileNom => $entries) {
            $profile = Profile::where('nom', $profileNom)->first();
            if (! $profile) {
                $this->command->warn("Profil « {$profileNom} » introuvable, ignoré.");

                continue;
            }
            foreach ($entries as [$moduleName, $actions]) {
                $this->grantModulePermissionsCas6($profile->id, $moduleName, $actions);
            }
            $this->command->info("Permissions « {$profileNom} » : OK.");
        }
    }

    private function seedSuperAdmin(int $profileId): void
    {
        ProfilePermission::create([
            'id_profile' => $profileId,
            'id_permission' => null,
            'id_entreprise' => null,
            'actif' => 1,
            'date_debut' => now(),
            'date_fin' => null,
        ]);
        $this->command->info('Super Admin : permission globale OK.');
    }

    /**
     * CAS 4 (User::hasPermission) : admin de l’entreprise — toutes les permissions métier pour ce périmètre.
     */
    private function seedDirecteurEntreprise(int $profileId, Entreprise $entreprise): void
    {
        ProfilePermission::create([
            'id_profile' => $profileId,
            'id_permission' => null,
            'id_entreprise' => $entreprise->id,
            'actif' => 1,
            'date_debut' => now(),
            'date_fin' => null,
        ]);
        $this->command->info('Directeur : rôle admin entreprise OK.');
    }

    /**
     * CAS 6 : permission explicite, sans restriction géographique (adapté à un déploiement par société).
     *
     * @param  array<string>|null  $actions  null = toutes les actions du module
     */
    private function grantModulePermissionsCas6(int $profileId, string $moduleName, ?array $actions): void
    {
        $module = Module::where('nom', $moduleName)->where('actif', 1)->first();
        if (! $module) {
            $this->command->warn("Module « {$moduleName} » introuvable.");

            return;
        }
        $q = Permission::where('id_module', $module->id)->where('actif', 1);
        if ($actions !== null) {
            $q->whereIn('action', $actions);
        }
        foreach ($q->get() as $perm) {
            ProfilePermission::create([
                'id_profile' => $profileId,
                'id_permission' => $perm->id,
                'id_entreprise' => null,
                'actif' => 1,
                'date_debut' => now(),
                'date_fin' => null,
            ]);
        }
    }
}
