<?php

namespace Database\Seeders;

use App\Models\Poste;
use Illuminate\Database\Seeder;

class PosteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $postes = [
            ['intitule' => 'Directeur', 'description' => 'Direction et pilotage de l\'entreprise'],
            ['intitule' => 'Comptable', 'description' => 'Gestion comptable et financière'],
        ];

        foreach ($postes as $row) {
            Poste::firstOrCreate(
                ['intitule' => $row['intitule']],
                [
                    'description' => $row['description'],
                    'actif' => true,
                    'user_insert' => 1,
                ]
            );
        }

        $keepIds = Poste::query()
            ->whereIn('intitule', ['Directeur', 'Comptable'])
            ->pluck('id');

        Poste::query()
            ->whereNotIn('id', $keepIds)
            ->update(['actif' => false]);
    }
}
