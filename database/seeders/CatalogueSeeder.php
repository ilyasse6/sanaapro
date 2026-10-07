<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatalogueService;
use App\Models\Pack;

class CatalogueSeeder extends Seeder
{
    /**
     * Catalogue commercial Prowise : prestations / produits (équipements médicaux, cuisine, bureautique) et packs.
     */
    public function run(): void
    {
        // ——— Prestations / Produits (équipements et services) ———
        $services = [
            // Équipement médical
            ['nom' => 'Lit médical électrique', 'description' => 'Lit médical à hauteur variable, dossier et jambes réglables électriquement.', 'prix' => 8500.00, 'reduction' => 0],
            ['nom' => 'Moniteur de signes vitaux', 'description' => 'Monitoring tension, SpO2, fréquence cardiaque, température.', 'prix' => 4200.00, 'reduction' => 5],
            ['nom' => 'Aspirateur médical', 'description' => 'Aspirateur de secrétions pour bloc et soins intensifs.', 'prix' => 2800.00, 'reduction' => 0],
            ['nom' => 'Chariot de soins inox', 'description' => 'Chariot à tiroirs et plateau pour bloc ou chambre.', 'prix' => 1200.00, 'reduction' => 10],
            ['nom' => 'Lampe de consultation', 'description' => 'Lampe sur bras articulé pour cabinet médical.', 'prix' => 650.00, 'reduction' => 0],
            ['nom' => 'Autoclave stérilisateur', 'description' => 'Stérilisation vapeur pour instruments médicaux.', 'prix' => 5500.00, 'reduction' => 8],
            ['nom' => 'Fauteuil roulant standard', 'description' => 'Fauteuil roulant pliable, réglable.', 'prix' => 1800.00, 'reduction' => 0],
            ['nom' => 'Table d\'examen gynécologique', 'description' => 'Table d\'examen avec étriers et réglage électrique.', 'prix' => 7200.00, 'reduction' => 5],
            // Cuisine professionnelle
            ['nom' => 'Four à convection professionnel', 'description' => 'Four à convection 10 à 20 niveaux, gaz ou électrique.', 'prix' => 15000.00, 'reduction' => 0],
            ['nom' => 'Réfrigérateur professionnel 2 portes', 'description' => 'Froid positif / négatif, inox, capacité 600 L.', 'prix' => 8500.00, 'reduction' => 5],
            ['nom' => 'Plancha gaz inox', 'description' => 'Plancha professionnelle 2 ou 3 feux, inox.', 'prix' => 3200.00, 'reduction' => 0],
            ['nom' => 'Hotte professionnelle filtrante', 'description' => 'Hotte à filtres à charbon, débit 1200 m³/h.', 'prix' => 4500.00, 'reduction' => 8],
            ['nom' => 'Lave-vaisselle professionnel', 'description' => 'Lave-vaisselle à capot, 50 couverts.', 'prix' => 6200.00, 'reduction' => 0],
            ['nom' => 'Mixeur plongeant professionnel', 'description' => 'Mixeur inox 1000 W, usage intensif.', 'prix' => 450.00, 'reduction' => 10],
            ['nom' => 'Table inox travail', 'description' => 'Table de travail inox 200x80 cm, pieds réglables.', 'prix' => 1800.00, 'reduction' => 0],
            ['nom' => 'Armoire de cuisson 6 niveaux', 'description' => 'Armoire de cuisson à vapeur ou à gaz.', 'prix' => 9500.00, 'reduction' => 5],
            // Bureautique
            ['nom' => 'Imprimante multifonction A3', 'description' => 'Impression, scan, copie, fax, réseau.', 'prix' => 2200.00, 'reduction' => 0],
            ['nom' => 'Armoire de rangement métallique', 'description' => 'Armoire 2 portes, 5 tiroirs, classeur suspendu.', 'prix' => 850.00, 'reduction' => 5],
            ['nom' => 'Bureau réglable en hauteur', 'description' => 'Bureau électrique assis-debout, plateau 160x80 cm.', 'prix' => 1800.00, 'reduction' => 10],
            ['nom' => 'Chaise de bureau ergonomique', 'description' => 'Dossier réglable, accoudoirs, roulettes.', 'prix' => 650.00, 'reduction' => 0],
            ['nom' => 'Vidéoprojecteur Full HD', 'description' => 'Vidéoprojecteur 3500 lumens, HDMI, WiFi.', 'prix' => 4200.00, 'reduction' => 8],
            ['nom' => 'Écran tactile 55 pouces', 'description' => 'Écran tactile pour salle de réunion ou formation.', 'prix' => 8500.00, 'reduction' => 5],
            ['nom' => 'Destructeur de documents', 'description' => 'Destruction coupe croisée, 20 feuilles.', 'prix' => 380.00, 'reduction' => 0],
        ];

        $createdServices = [];
        foreach ($services as $s) {
            $service = CatalogueService::create([
                'nom' => $s['nom'],
                'description' => $s['description'],
                'prix' => $s['prix'],
                'reduction' => $s['reduction'] ?? 0,
                'image' => null,
                'actif' => 1,
                'user_insert' => 1,
            ]);
            $createdServices[] = $service;
        }

        // ——— Packs / Ensembles commerciaux ———
        $packs = [
            [
                'nom' => 'Pack Cabinet médical de base',
                'description' => 'Équipement de base pour cabinet : lit d\'examen, lampe, chariot, fauteuil.',
                'prix' => 15000.00,
                'reduction' => 12,
                'services' => [$createdServices[0]->id, $createdServices[4]->id, $createdServices[3]->id, $createdServices[6]->id],
            ],
            [
                'nom' => 'Pack Bloc / Soins intensifs',
                'description' => 'Monitoring, aspirateur, chariot, autoclave pour bloc ou réanimation.',
                'prix' => 14000.00,
                'reduction' => 10,
                'services' => [$createdServices[1]->id, $createdServices[2]->id, $createdServices[3]->id, $createdServices[5]->id],
            ],
            [
                'nom' => 'Pack Cuisine pro petite structure',
                'description' => 'Four, frigo, plancha, table inox, mixeur pour petite cuisine.',
                'prix' => 28000.00,
                'reduction' => 15,
                'services' => [$createdServices[8]->id, $createdServices[9]->id, $createdServices[10]->id, $createdServices[14]->id, $createdServices[13]->id],
            ],
            [
                'nom' => 'Pack Cuisine pro complète',
                'description' => 'Cuisine complète : four, frigo, plancha, hotte, lave-vaisselle, armoire cuisson, tables.',
                'prix' => 52000.00,
                'reduction' => 18,
                'services' => [$createdServices[8]->id, $createdServices[9]->id, $createdServices[10]->id, $createdServices[11]->id, $createdServices[12]->id, $createdServices[14]->id, $createdServices[15]->id],
            ],
            [
                'nom' => 'Pack Bureau standard',
                'description' => 'Bureau, chaise, armoire, imprimante pour poste de travail.',
                'prix' => 4500.00,
                'reduction' => 8,
                'services' => [$createdServices[18]->id, $createdServices[19]->id, $createdServices[17]->id, $createdServices[16]->id],
            ],
            [
                'nom' => 'Pack Salle de réunion',
                'description' => 'Vidéoprojecteur, écran tactile, bureau réglable, chaises.',
                'prix' => 18000.00,
                'reduction' => 10,
                'services' => [$createdServices[20]->id, $createdServices[21]->id, $createdServices[18]->id, $createdServices[19]->id],
            ],
        ];

        foreach ($packs as $p) {
            $servicesIds = $p['services'];
            unset($p['services']);
            $pack = Pack::create([
                'nom' => $p['nom'],
                'description' => $p['description'],
                'prix' => $p['prix'],
                'reduction' => $p['reduction'] ?? 0,
                'image' => null,
                'actif' => 1,
                'user_insert' => 1,
            ]);
            if (!empty($servicesIds)) {
                $pack->services()->attach($servicesIds);
            }
        }
    }
}
