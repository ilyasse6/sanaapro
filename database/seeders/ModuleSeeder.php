<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Module::create([
            'nom' => 'Gestion des Utilisateurs',
            'actif' => 1,
            'user_insert' => 1,
        ]);

        Module::create([
            'nom' => 'Gestion des Profiles',
            'actif' => 1,
            'user_insert' => 1,
        ]);

        Module::create([
            'nom' => 'Gestion des Permissions',
            'actif' => 1,
            'user_insert' => 1,
        ]);

        Module::create([
            'nom' => 'Gestion des Entreprises',
            'actif' => 1,
            'user_insert' => 1,
        ]);

        Module::create([
            'nom' => 'Gestion des Postes',
            'actif' => 1,
            'user_insert' => 1,
        ]);

        Module::create([
            'nom' => 'Gestion des Clients',
            'actif' => 1,
            'user_insert' => 1,
        ]);

        Module::create([
            'nom' => 'Gestion des Rendez-vous',
            'actif' => 1,
            'user_insert' => 1,
        ]);

        Module::firstOrCreate(
            ['nom' => 'Gestion des Devis'],
            ['actif' => 1, 'user_insert' => 1]
        );

        Module::firstOrCreate(
            ['nom' => 'Gestion des Catalogues'],
            ['actif' => 1, 'user_insert' => 1]
        );

        Module::firstOrCreate(
            ['nom' => 'Gestion des Bons de livraison'],
            ['actif' => 1, 'user_insert' => 1]
        );

        Module::firstOrCreate(
            ['nom' => 'Gestion des Bons de commande'],
            ['actif' => 1, 'user_insert' => 1]
        );
    }
}
