<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\CatalogueService;
use App\Models\Pack;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RendezVousApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = RendezVous::with(['client', 'services', 'packs'])->where('actif', 1)->orderByDesc('date_rdv');
        if ($request->filled('date_from')) {
            $query->whereDate('date_rdv', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_rdv', '<=', $request->date_to);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        return $this->paginated($query->paginate((int) $request->get('per_page', 20)), 'Liste visites.');
    }

    public function show(int $id)
    {
        $rdv = RendezVous::with(['client', 'services', 'packs'])->findOrFail($id);
        return $this->ok($rdv, 'Detail visite.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'date_rdv' => 'required|date',
            'heure_rdv' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'statut' => 'nullable|in:en_attente,confirme,en_cours,termine,annule',
            'notes' => 'nullable|string|max:1000',
            'services' => 'nullable|array',
            'services.*' => 'exists:catalogue_services,id',
            'packs' => 'nullable|array',
            'packs.*' => 'exists:catalogue_packs,id',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation error', $validator->errors()->toArray(), 422);
        }

        $hasServices = $request->filled('services') && is_array($request->services) && count($request->services) > 0;
        $hasPacks = $request->filled('packs') && is_array($request->packs) && count($request->packs) > 0;
        if (! $hasServices && ! $hasPacks) {
            return $this->fail('Validation error', ['selection' => ['Selectionnez au moins une prestation ou un pack.']], 422);
        }

        $prixTotal = 0;
        foreach ((array) $request->services as $id) {
            $srv = CatalogueService::find($id);
            if ($srv) {
                $prixTotal += (float) $srv->prix_apres_reduction;
            }
        }
        foreach ((array) $request->packs as $id) {
            $pack = Pack::find($id);
            if ($pack) {
                $prixTotal += (float) $pack->prix_apres_reduction;
            }
        }

        $rdv = RendezVous::create([
            'client_id' => $request->client_id,
            'date_rdv' => $request->date_rdv,
            'heure_rdv' => $request->heure_rdv,
            'statut' => $request->statut ?: 'en_attente',
            'notes' => $request->notes,
            'prix_total' => $prixTotal,
            'actif' => 1,
            'user_insert' => $request->user()->id,
        ]);

        $servicesData = [];
        foreach ((array) $request->services as $id) {
            $srv = CatalogueService::find($id);
            if ($srv) {
                $servicesData[$id] = ['prix' => $srv->prix_apres_reduction];
            }
        }
        $rdv->services()->sync($servicesData);

        $packsData = [];
        foreach ((array) $request->packs as $id) {
            $pack = Pack::find($id);
            if ($pack) {
                $packsData[$id] = ['prix' => $pack->prix_apres_reduction];
            }
        }
        $rdv->packs()->sync($packsData);

        return $this->ok($rdv->load(['client', 'services', 'packs']), 'Visite creee.', 201);
    }

    public function update(Request $request, int $id)
    {
        $rdv = RendezVous::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'client_id' => 'sometimes|required|exists:clients,id',
            'date_rdv' => 'sometimes|required|date',
            'heure_rdv' => ['sometimes', 'required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'statut' => 'sometimes|required|in:en_attente,confirme,en_cours,termine,annule',
            'notes' => 'nullable|string|max:1000',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation error', $validator->errors()->toArray(), 422);
        }
        $data = $validator->validated();
        $data['user_update'] = $request->user()->id;
        $rdv->update($data);
        return $this->ok($rdv->fresh()->load(['client', 'services', 'packs']), 'Visite mise a jour.');
    }
}
