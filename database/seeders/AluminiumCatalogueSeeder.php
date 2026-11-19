<?php

namespace Database\Seeders;

use App\Models\CatalogueService;
use Illuminate\Database\Seeder;

class AluminiumCatalogueSeeder extends Seeder
{
    /**
     * Catalogue menuiserie aluminium AfifAlu (gamme SEPALUMIC), repris des devis SEPALUMIC MT23006596 et MT23006332.
     */
    public function run(): void
    {
        $produits = [
            [
                'nom' => 'Coulissant 2 vantaux série 2600L en aluminium SEPALUMIC',
                'description' => 'Fenêtre / porte-fenêtre coulissante 2 vantaux, série froide 2600L. Joint brosse 5×7 HI-FIN, dormant coulissant 2731×3 + 2730, 2 verrous 26491 CLIPSY rotation. Double vitrage 24 mm (ni fourni ni chiffré). Dimensions de référence : L 2,40 m × H 2,50 m. Prix SEPALUMIC de référence : 5 408,00 DH HT.',
                'image' => 'assets/imgs/catalogue-aluminium/coulissant-2-vantaux-2600l.png',
            ],
            [
                'nom' => 'Coulissant 3 rails 3 vantaux série 2600L en aluminium SEPALUMIC',
                'description' => 'Baie coulissante 3 rails 3 vantaux, série froide 2600L. Joint brosse 5×7 HI-FIN, couvre-joint 1L + 2H 4089 froid, dormant coulissant 3 rails 2732×4, 2 verrous 26491 CLIPSY rotation. Double vitrage 24 mm (ni fourni ni chiffré). Dimensions de référence : L 4,35 m × H 2,30 m. Prix SEPALUMIC de référence : 9 058,00 DH HT.',
                'image' => 'assets/imgs/catalogue-aluminium/coulissant-3-rails-3-vantaux-2600l.png',
            ],
            [
                'nom' => 'Ensemble ouvrant à la française 1 vantail 4219 + allège fixe série 4200 en aluminium SEPALUMIC',
                'description' => 'Ensemble composé d\'un ouvrant à la française 1 vantail 4219 en partie haute et d\'un remplissage fixe série 4200 en allège (H 0,96 m). Double vitrage 24 mm (ni fourni ni chiffré). Dimensions de référence : L 0,55 m × H 2,30 m. Prix SEPALUMIC de référence : 1 814,00 DH HT.',
                'image' => 'assets/imgs/catalogue-aluminium/ensemble-ouvrant-francaise-4219-allege-fixe-4200.png',
            ],
            [
                'nom' => 'Ensemble 2 fixes série 4200 en aluminium SEPALUMIC',
                'description' => 'Ensemble de 2 remplissages fixes juxtaposés, série 4200. Double vitrage 24 mm (ni fourni ni chiffré). Dimensions de référence : L 1,95 m × H 2,60 m. Prix SEPALUMIC de référence : 2 818,00 DH HT.',
                'image' => 'assets/imgs/catalogue-aluminium/ensemble-2-fixes-4200.png',
            ],
            [
                'nom' => 'Châssis fixe série 4200 en aluminium SEPALUMIC',
                'description' => 'Remplissage fixe série 4200, dormant avec couvre-joint 4221. Double vitrage 24 mm (ni fourni ni chiffré). Dimensions de référence : L 1,92 m × H 3,20 m. Prix SEPALUMIC de référence : 1 807,00 DH HT.',
                'image' => 'assets/imgs/catalogue-aluminium/chassis-fixe-4200-cj-4221.png',
            ],
            [
                'nom' => 'Ensemble coulifix série 2600 en aluminium SEPALUMIC',
                'description' => 'Ensemble coulifix : 2 ouvrants coulifix 1 vantail latéraux 2600 + fixe coulifix central 2600 avec parclose simple, couvre-joint 1L + 2H 3380M froid, verrous 26491 CLIPSY rotation. Double vitrage 24 mm (ni fourni ni chiffré). Dimensions de référence : L 5,90 m × H 2,56 m. Prix SEPALUMIC de référence : 8 925,00 DH HT.',
                'image' => 'assets/imgs/catalogue-aluminium/ensemble-coulifix-2600.png',
            ],
            [
                'nom' => 'Mur-rideau série 1100 en aluminium SEPALUMIC',
                'description' => 'Mur-rideau type capot et serreur, série 1100 : tube 20×20 sur 4 côtés et fixes pour mur-rideau feuillure 33 mm. Double vitrage 24 mm (ni fourni ni chiffré). Dimensions de référence : L 5,98 m × H 7,20 m. Prix SEPALUMIC de référence : 43 733,00 DH HT.',
                'image' => 'assets/imgs/catalogue-aluminium/mur-rideau-1100.png',
            ],
            [
                'nom' => 'Mur-rideau VEC série 1100 avec ouvrants projetés en aluminium SEPALUMIC',
                'description' => 'Mur-rideau type V.E.C. série 1100 sur une colonne : cadres VEC droits 1611100 fixes et ouvrants projetés. Double vitrage 44.1/12-8 = 28 mm (ni fourni ni chiffré). Dimensions de référence : L 1,10 m × H 11,00 m. Prix SEPALUMIC de référence (devis MT23006332) : 20 519,00 DH HT.',
                'image' => 'assets/imgs/catalogue-aluminium/mur-rideau-vec-1100-ouvrants-projetes.png',
            ],
        ];

        foreach ($produits as $p) {
            CatalogueService::firstOrCreate(
                ['nom' => $p['nom']],
                [
                    'description' => $p['description'],
                    'prix' => 0,
                    'reduction' => 0,
                    'image' => $p['image'],
                    'actif' => 1,
                    'user_insert' => 1,
                ]
            );
        }
    }
}
