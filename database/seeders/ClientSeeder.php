<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use Carbon\Carbon;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un client non diagnostiqué depuis plus de 2 mois
        $clientNonDiagnostique = Client::create([
            'nom' => 'Alami',
            'prenom' => 'Fatima',
            'date_naissance' => Carbon::parse('1990-05-15'),
            'cin' => 'AB123456',
            'besoin_diagnostic' => 0,
            'actif' => 1,
            'user_insert' => 1,
            'created_at' => Carbon::now()->subMonths(3), // Créé il y a 3 mois
            'updated_at' => Carbon::now()->subMonths(3),
        ]);

        // Créer un client diagnostiqué récemment
        $clientDiagnostique = Client::create([
            'nom' => 'Benali',
            'prenom' => 'Ahmed',
            'date_naissance' => Carbon::parse('1985-08-20'),
            'cin' => 'CD789012',
            'besoin_diagnostic' => 1,
            'actif' => 1,
            'user_insert' => 1,
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10),
        ]);

        // Créer le diagnostic pour le client diagnostiqué
        \App\Models\Diagnostic::create([
            'client_id' => $clientDiagnostique->id,
            'deja_fait_laser' => 0,
            'type_peau' => 'III',
            'consentement_eclaire' => 1,
            'zones_traiter' => 'Jambes, Aisselles',
            'zone_cible' => 'Jambes complètes',
            'type_traitement' => 'laser_diodes',
            'nb_seances_prevues' => 6,
            'date_prise_rdv' => Carbon::now()->addDays(7),
            'heure_rdv' => '10:00:00',
            'actif' => 1,
            'user_insert' => 1,
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10),
        ]);
    }
}

