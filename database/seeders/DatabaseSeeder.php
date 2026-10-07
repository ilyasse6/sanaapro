<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EntrepriseSeeder::class,
            PosteSeeder::class,
            ModuleSeeder::class,
            ProfileSeeder::class,
            PermissionSeeder::class,
            ProwiseModulesPermissionsSeeder::class,
            ProfilePermissionSeeder::class,
        ]);

        $firstEid = (int) \App\Models\Entreprise::query()->orderBy('id')->value('id');
        if ($firstEid > 0) {
            \App\Support\CurrentTenant::set($firstEid);
        }

        $this->call([
            UserSeeder::class,
            AluminiumCatalogueSeeder::class,
        ]);

        \App\Support\CurrentTenant::forget();
    }
}
