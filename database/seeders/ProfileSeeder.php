<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = [
            [
                'nom' => 'Super Admin',
                'description' => 'Administrateur système avec tous les privilèges',
            ],
            [
                'nom' => 'Directeur',
                'description' => 'Pilotage : accès complet au périmètre de l’établissement (hors administration système)',
            ],
            [
                'nom' => 'Comptable',
                'description' => 'Comptabilité, facturation, lecture commerciale (clients, devis)',
            ],
        ];

        foreach ($profiles as $row) {
            Profile::create([
                'nom' => $row['nom'],
                'description' => $row['description'],
                'actif' => 1,
                'user_insert' => 1,
            ]);
        }
    }
}
