<?php

namespace Database\Seeders;

use App\Models\Entreprise;
use Illuminate\Database\Seeder;

class EntrepriseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Entreprise::firstOrCreate(
            ['abbreviation' => 'SanaaPro'],
            [
                'description' => 'SanaaPro',
                'type' => 'SARL',
                'actif' => 1,
                'user_insert' => 1,
            ]
        );
    }
}
