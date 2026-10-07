<?php

namespace App\Support;

use App\Models\Client;
use Illuminate\Http\Request;

/**
 * Champ client des formulaires : sélection dans la liste (client_id) ou saisie libre (client_libre).
 */
class ClientSaisi
{
    public static function rules(): array
    {
        return [
            'client_mode' => 'nullable|in:liste,libre',
            'client_id' => 'exclude_if:client_mode,libre|required|exists:clients,id',
            'client_libre' => 'exclude_unless:client_mode,libre|required|string|max:255',
        ];
    }

    public static function messages(): array
    {
        return [
            'client_id.required' => 'Veuillez choisir un client.',
            'client_id.exists' => 'Le client sélectionné est introuvable.',
            'client_libre.required' => 'Veuillez saisir le nom du client.',
            'client_libre.max' => 'Le nom du client ne doit pas dépasser 255 caractères.',
        ];
    }

    public static function resolveId(Request $request, ?int $userId = null): int
    {
        if ($request->input('client_mode') === 'libre') {
            return Client::trouverOuCreerParNom((string) $request->input('client_libre'), $userId)->id;
        }

        return (int) $request->input('client_id');
    }
}
