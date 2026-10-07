<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Employe;
use Illuminate\Http\Request;

class EmployesTrackingApiController extends BaseApiController
{
    public function employesIndex(Request $request)
    {
        $q = Employe::with('conges')->where('actif', 1)->orderByDesc('id');
        return $this->paginated($q->paginate((int) $request->get('per_page', 20)), 'Liste employes.');
    }

    public function employeShow(int $id)
    {
        $employe = Employe::with('conges')->findOrFail($id);
        return $this->ok($employe, 'Detail employe.');
    }

    public function trackingSnapshot()
    {
        $employes = Employe::query()->where('actif', true)->orderBy('id')->take(2)->get();
        $positions = [
            ['zone' => 'Casablanca', 'quartier' => 'Maarif', 'lat' => 33.5869, 'lng' => -7.6327],
            ['zone' => 'Rabat', 'quartier' => 'Hassan', 'lat' => 34.0211, 'lng' => -6.8428],
        ];

        $data = [];
        foreach ($positions as $i => $pos) {
            $e = $employes[$i] ?? null;
            $data[] = [
                'numero' => $i + 1,
                'employe_id' => $e?->id,
                'nom' => $e ? trim($e->prenom . ' ' . $e->nom) : ($i === 0 ? 'Amine El Mansouri' : 'Sara Benali'),
                'zone' => $pos['zone'],
                'quartier' => $pos['quartier'],
                'lat' => $pos['lat'],
                'lng' => $pos['lng'],
                'statut' => $i === 0 ? 'En route' : 'A l arret',
                'updated_at' => now()->subMinutes($i === 0 ? 2 : 8)->toIso8601String(),
            ];
        }

        return $this->ok([
            'server_time' => now()->toIso8601String(),
            'employees' => $data,
        ], 'Tracking snapshot.');
    }
}
