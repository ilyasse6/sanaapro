<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends BaseApiController
{
    public function login(Request $request)
    {
        $login = trim((string) ($request->input('login') ?? $request->input('email', '')));

        $validator = Validator::make(
            [
                'login' => $login,
                'password' => $request->input('password'),
                'device_name' => $request->input('device_name'),
            ],
            [
                'login' => 'required|string|max:255',
                'password' => 'required|string|min:4',
                'device_name' => 'nullable|string|max:120',
            ]
        );

        if ($validator->fails()) {
            return $this->fail('Validation error', $validator->errors()->toArray(), 422);
        }

        try {
            $email = User::emailFromLoginIdentifier($login, 'login');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->fail('Identifiants invalides.', $e->errors(), 422);
        }

        $user = User::where('email', $email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->fail('Identifiants invalides.', ['auth' => ['Identifiant ou mot de passe incorrect.']], 401);
        }

        if (! $user->actif) {
            return $this->fail('Compte inactif.', ['auth' => ['Votre compte est desactive.']], 403);
        }

        $deviceName = $request->device_name ?: 'mobile-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->ok([
            'token' => $token,
            'user' => $user->only(['id', 'nom', 'prenom', 'email', 'gmail', 'telephone']),
        ], 'Connexion reussie.');
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return $this->ok($user?->only(['id', 'nom', 'prenom', 'email', 'gmail', 'telephone']), 'Profil utilisateur.');
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();
        return $this->ok(null, 'Deconnexion reussie.');
    }
}
