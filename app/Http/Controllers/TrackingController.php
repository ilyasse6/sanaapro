<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TrackingController extends Controller
{
    public function index()
    {
        $u = Auth::user();
        if (! $u) {
            return redirect()->route('login');
        }
        if (! $u->hasPermission('Suivi des déplacements', 'consulter')) {
            return redirect()->back()->with('error', 'Permission insuffisante.');
        }

        $now = Carbon::now()->locale('fr');

        // Positions WGS84 (démo) — axe Casa → Rabat ; carte Leaflet + tuiles OSM/Carto
        $positions = [
            [
                'label' => 'Casablanca',
                'quartier' => 'Maârif',
                'adresse' => 'Bd Zerktouni, près de Twin Center',
                'lat' => 33.5869,
                'lng' => -7.6327,
            ],
            [
                'label' => 'Rabat',
                'quartier' => 'Hassan',
                'adresse' => 'Av. Allal Ben Abdellah',
                'lat' => 34.0211,
                'lng' => -6.8428,
            ],
        ];

        $demos = [
            [
                'distance_km' => '87',
                'duree_eta' => '1 h 08',
                'vitesse' => '58',
                'mission' => 'VIS-2026-0842',
                'vehicule' => 'Dacia Duster',
                'immat' => '45234-B-1',
                'carburant_pct' => '41',
                'conso' => '6,2',
                'budget_carburant' => '124',
                'odometre' => '48 290',
            ],
            [
                'distance_km' => '62',
                'duree_eta' => '52 min',
                'vitesse' => '0',
                'mission' => 'INT-2026-0156',
                'vehicule' => 'Renault Kangoo',
                'immat' => '88102-A-7',
                'carburant_pct' => '68',
                'conso' => '5,8',
                'budget_carburant' => '78',
                'odometre' => '112 045',
            ],
        ];

        $employesDb = Employe::query()
            ->where('actif', true)
            ->orderBy('id')
            ->take(2)
            ->get();

        $trackingEmployes = [];
        for ($i = 0; $i < 2; $i++) {
            $e = $employesDb[$i] ?? null;
            $pos = $positions[$i];
            $d = $demos[$i];
            $maj = $now->copy()->subMinutes($i === 0 ? 2 : 8);

            $trackingEmployes[] = array_merge([
                'id' => $e?->id,
                'numero' => $i + 1,
                'nom' => $e ? trim($e->prenom . ' ' . $e->nom) : ['Amine El Mansouri', 'Sara Benali'][$i],
                'matricule' => $e?->matricule ?? ['EMP-2026-014', 'EMP-2026-022'][$i],
                'poste' => $e?->poste_libelle ?? ['Commercial terrain', 'Technicien SAV itinérant'][$i],
                'zone' => $pos['label'],
                'quartier' => $pos['quartier'],
                'adresse' => $pos['adresse'],
                'statut' => $i === 0 ? 'En route' : 'À l’arrêt (client)',
                'lat' => $pos['lat'],
                'lng' => $pos['lng'],
                'derniere_maj' => $maj->format('H:i'),
                'derniere_maj_rel' => $maj->diffForHumans(),
            ], $d);
        }

        $map = config('laravel-maps.tracking');

        return view('user.tracking.index', [
            'trackingEmployes' => $trackingEmployes,
            'carteHeure' => $now->format('H:i'),
            'carteDate' => $now->translatedFormat('l j F Y'),
            'mapTileUrl' => $map['tile_url'],
            'mapTileSubdomains' => $map['tile_subdomains'],
            'mapAttribution' => $map['tile_attribution'],
        ]);
    }
}
