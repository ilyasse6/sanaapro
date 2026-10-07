<?php

namespace Database\Seeders;

use App\Models\Entreprise;
use App\Models\Poste;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::withoutEvents(function () {
            // Récupérer les références nécessaires
            $superAdminProfile = Profile::where('nom', 'Super Admin')->first();
            $directeurPoste = Poste::where('intitule', 'Directeur')->first();
            $entreprisePrincipal = Entreprise::find(1);

            // Vérifier les dépendances
            if (! $superAdminProfile) {
                $this->command->error('Profile "Super Admin" not found. Please run ProfileSeeder first.');

                return;
            }

            if (! $directeurPoste) {
                $this->command->error('Poste "Directeur" not found. Please run PosteSeeder first.');

                return;
            }

            if (! $entreprisePrincipal) {
                $this->command->error('Entreprise ID 1 introuvable. Exécutez EntrepriseSeeder d\'abord.');

                return;
            }

            // === Admins ===
            // Nom d'utilisateur (identifiant court à la connexion) = partie locale avant @prowise.ma
            $domaine = 'prowise.ma';
            $admins = [
                [
                    'nom' => 'Admin',
                    'prenom' => 'SanaaPro',
                    'username' => 'admin',
                    'password' => 'password',
                ],
                [
                    'nom' => 'Belahcen',
                    'prenom' => 'Ilyasse',
                    'username' => 'i.belahcen',
                    'password' => 'ilyasse104',
                ],
                [
                    'nom' => 'Elhor',
                    'prenom' => 'Zakaria',
                    'username' => 'z.elhor',
                    'password' => 'zakaria104',
                ],
            ];

            foreach ($admins as $admin) {
                $email = strtolower($admin['username']).'@'.$domaine;

                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    $this->command->info("User {$email} already exists, skipping...");

                    continue;
                }

                $userData = [
                    'nom' => $admin['nom'],
                    'prenom' => $admin['prenom'],
                    'email' => $email,
                    'id_entreprise' => $entreprisePrincipal->id,
                    'id_poste' => $directeurPoste->id,
                    'password' => Hash::make($admin['password']),
                    'actif' => true,
                    'user_insert' => 1,
                    'email_verified_at' => now(),
                ];

                if (isset($admin['gmail'])) {
                    $userData['gmail'] = $admin['gmail'];
                }

                $user = User::create($userData);

                UserProfile::create([
                    'id_user' => $user->id,
                    'id_profile' => $superAdminProfile->id,
                    'actif' => true,
                ]);

                $this->command->info("Created user: {$admin['username']} ({$email}) with Super Admin profile");
            }

        });
    }
}
