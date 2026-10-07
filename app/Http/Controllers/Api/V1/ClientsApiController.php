<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientsApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Client::query()->where('actif', 1)->orderByDesc('id');
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($x) use ($q) {
                $x->where('nom', 'like', "%{$q}%")
                    ->orWhere('prenom', 'like', "%{$q}%")
                    ->orWhere('telephone', 'like', "%{$q}%");
            });
        }

        return $this->paginated($query->paginate((int) $request->get('per_page', 20)), 'Liste clients.');
    }

    public function show(int $id)
    {
        $client = Client::with(['devis', 'factures'])->findOrFail($id);
        return $this->ok($client, 'Detail client.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:30',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:120',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation error', $validator->errors()->toArray(), 422);
        }

        $payload = array_merge($validator->validated(), [
            'actif' => 1,
            'user_insert' => $request->user()->id,
        ]);
        $client = Client::create($payload);

        return $this->ok($client, 'Client cree.', 201);
    }

    public function update(Request $request, int $id)
    {
        $client = Client::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:30',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:120',
            'actif' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation error', $validator->errors()->toArray(), 422);
        }

        $data = $validator->validated();
        $data['user_update'] = $request->user()->id;
        $client->update($data);

        return $this->ok($client->fresh(), 'Client mis a jour.');
    }
}
