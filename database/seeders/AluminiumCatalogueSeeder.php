<?php

namespace Database\Seeders;

use App\Models\CatalogueService;
use Illuminate\Database\Seeder;

class AluminiumCatalogueSeeder extends Seeder
{
    /**
     * Catalogue menuiserie aluminium AfifAlu (gamme SEPALUMIC), repris du devis YAKADA 2.
     */
    public function run(): void
    {
        $produits = [
            [
                'nom' => 'Châssis ouvrant cintré en aluminium SEPALUMIC',
                'description' => 'Châssis ouvrant à partie haute cintrée, profilés aluminium SEPALUMIC. Dimensions de référence : L 0,645 m × H 1,075 m.',
                'image' => 'assets/imgs/catalogue-aluminium/chassis-ouvrant-cintre.png',
            ],
            [
                'nom' => 'Porte ouvrant battant en aluminium SEPALUMIC',
                'description' => 'Porte vitrée ouvrant battant, profilés aluminium SEPALUMIC avec traverses intermédiaires. Dimensions de référence : L 0,88 m × H 2,10 m.',
                'image' => 'assets/imgs/catalogue-aluminium/porte-ouvrant-battant.png',
            ],
            [
                'nom' => 'Ouvrant à soufflet en aluminium SEPALUMIC',
                'description' => 'Ouvrant basculant à soufflet (ouverture par le haut), profilés aluminium SEPALUMIC. Dimensions de référence : L 0,54 m × H 0,59 m.',
                'image' => 'assets/imgs/catalogue-aluminium/ouvrant-soufflet.png',
            ],
            [
                'nom' => 'Ouvrant à la française en aluminium SEPALUMIC',
                'description' => 'Fenêtre 2 vantaux ouvrant à la française, profilés aluminium SEPALUMIC. Dimensions de référence : L 1,70 m × H 1,40 m.',
                'image' => 'assets/imgs/catalogue-aluminium/ouvrant-francaise.png',
            ],
            [
                'nom' => 'Dormant galandage 2 vantaux et 1 rail en aluminium SEPALUMIC',
                'description' => 'Coulissant à galandage 2 vantaux sur 1 rail (vantaux escamotables dans la cloison), profilés aluminium SEPALUMIC. Dimensions de référence : L 1,60 m × H 2,20 m.',
                'image' => 'assets/imgs/catalogue-aluminium/dormant-galandage-2-vantaux.png',
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
