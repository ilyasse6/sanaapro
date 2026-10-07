<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Models\Poste;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Téléphone mobile MA : uniquement les chiffres saisis.
     */
    private function digitsOnlyPhone(?string $value): string
    {
        return preg_replace('/\D/', '', (string) $value);
    }

    /**
     * Récupérer les profils autorisés pour l'utilisateur connecté
     */
    private function getAuthorizedProfiles()
    {
        try {
            $user = Auth::user();

            // Vérifier si l'utilisateur est actif
            if (! $user || ! $user->actif) {
                return collect();
            }

            // Récupérer les profils actifs de l'utilisateur connecté
            $userProfiles = [];

            if ($user->hasAllProfilesActive()) {
                $userProfiles = DB::table('user_profiles')
                    ->join('profiles', 'user_profiles.id_profile', '=', 'profiles.id')
                    ->where('user_profiles.id_user', $user->id)
                    ->where('user_profiles.actif', 1)
                    ->where('profiles.actif', 1)
                    ->pluck('profiles.id')
                    ->toArray();
            } else {
                $selectedProfileIds = $user->active_profile_ids;
                if (empty($selectedProfileIds)) {
                    return collect();
                }
                $validProfiles = DB::table('user_profiles')
                    ->join('profiles', 'user_profiles.id_profile', '=', 'profiles.id')
                    ->where('user_profiles.id_user', $user->id)
                    ->whereIn('user_profiles.id_profile', $selectedProfileIds)
                    ->where('user_profiles.actif', 1)
                    ->where('profiles.actif', 1)
                    ->pluck('profiles.id')
                    ->toArray();

                if (count($validProfiles) !== count($selectedProfileIds)) {
                    return collect();
                }
                $userProfiles = $validProfiles;
            }

            if (empty($userProfiles)) {
                return collect();
            }

            // Vérifier si l'utilisateur a le profil Super Admin
            $isSuperAdmin = $this->isSuperAdmin($userProfiles);

            if ($isSuperAdmin) {
                // Super Admin peut voir tous les profils actifs
                return Profile::where('actif', 1)->get();
            }

            // Pour les autres utilisateurs, récupérer les profils de leur région
            return $this->getRegionalProfiles($userProfiles);

        } catch (\Exception $e) {
            Log::error('Erreur dans getAuthorizedProfiles: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Vérifier si l'utilisateur a le profil Super Admin
     */
    private function isSuperAdmin($userProfiles)
    {
        try {
            // Récupérer les permissions pour vérifier si c'est un Super Admin
            $permissions = DB::table('profile_permissions')
                ->whereIn('id_profile', $userProfiles)
                ->where('actif', 1)
                ->where(function ($q) {
                    $q->where(function ($dateQuery) {
                        $dateQuery->whereNull('date_debut')
                            ->orWhere('date_debut', '<=', now());
                    })
                        ->where(function ($dateQuery) {
                            $dateQuery->whereNull('date_fin')
                                ->orWhere('date_fin', '>=', now());
                        });
                })
                ->get();

            foreach ($permissions as $permission) {
                // SUPER ADMIN - Toutes les permissions sont nulles
                if (is_null($permission->id_permission) &&
                    is_null($permission->id_entreprise)) {
                    return true;
                }
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Erreur dans isSuperAdmin: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Récupérer les profils selon les régions de l'utilisateur
     */
    private function getRegionalProfiles($userProfiles)
    {
        try {
            $entreprises = $this->getAuthorizedEntreprises();
            if ($entreprises->isEmpty()) {
                return collect();
            }
            $etabIds = $entreprises->pluck('id');

            $profileIds = DB::table('profile_permissions')
                ->where('actif', 1)
                ->where(function ($q) use ($etabIds) {
                    $q->whereIn('id_entreprise', $etabIds)
                        ->orWhere(function ($q2) {
                            $q2->whereNotNull('id_permission')->whereNull('id_entreprise');
                        });
                })
                ->where(function ($q) {
                    $q->where(function ($dateQuery) {
                        $dateQuery->whereNull('date_debut')
                            ->orWhere('date_debut', '<=', now());
                    })
                        ->where(function ($dateQuery) {
                            $dateQuery->whereNull('date_fin')
                                ->orWhere('date_fin', '>=', now());
                        });
                })
                ->pluck('id_profile')
                ->merge($userProfiles)
                ->unique();

            return Profile::whereIn('id', $profileIds)->where('actif', 1)->get();

        } catch (\Exception $e) {
            Log::error('Erreur dans getRegionalProfiles: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Récupérer les établissements autorisés pour l'utilisateur connecté
     */
    private function getAuthorizedEntreprises()
    {
        try {
            $user = Auth::user();

            if (! $user || ! $user->actif) {
                return collect();
            }

            if ($user->isSuperAdmin()) {
                return Entreprise::where('actif', 1)->get();
            }

            if (! $user->id_entreprise) {
                return collect();
            }

            return Entreprise::whereKey($user->id_entreprise)
                ->where('actif', 1)
                ->get();

        } catch (\Exception $e) {
            Log::error('Erreur dans getAuthorizedEntreprises: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Vérifier si l'utilisateur peut accéder à un établissement spécifique
     */
    private function canAccessEntreprise($entrepriseId)
    {
        try {
            if (Auth::user()?->isSuperAdmin()) {
                return true;
            }

            if (empty($entrepriseId)) {
                return false;
            }

            $authorizedEtablissements = $this->getAuthorizedEntreprises();

            return $authorizedEtablissements->pluck('id')->contains($entrepriseId);
        } catch (\Exception $e) {
            Log::error('Erreur dans canAccessEntreprise: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Postes proposés à la création / modification d'utilisateur (back-office).
     */
    private function postesPourFormulaireUtilisateur(): \Illuminate\Support\Collection
    {
        return Poste::query()
            ->where('actif', 1)
            ->whereIn('intitule', ['Directeur', 'Comptable'])
            ->orderBy('intitule')
            ->get();
    }

    /**
     * IDs de postes autorisés à l'enregistrement (création).
     */
    private function idsPostesUtilisateurAutorises(): array
    {
        return $this->postesPourFormulaireUtilisateur()->pluck('id')->all();
    }

    /**
     * IDs acceptés en mise à jour : Directeur / Comptable, ou conservation du poste actuel.
     */
    private function idsPostesAcceptesPourMiseAJour(User $userToUpdate): array
    {
        return array_values(array_unique(array_merge(
            $this->idsPostesUtilisateurAutorises(),
            $userToUpdate->id_poste ? [$userToUpdate->id_poste] : []
        )));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de consultation
            $hasPermission = $user->hasPermission('Gestion des Utilisateurs', 'consulter');
            if (! $hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter les utilisateurs.');
            }

            $entreprises = $this->getAuthorizedEntreprises();

            if ($user->isSuperAdmin()) {
                $utilisateurs = User::where('actif', 1)->get();
            } elseif (! $user->id_entreprise) {
                $utilisateurs = collect();
            } else {
                $utilisateurs = User::where('id_entreprise', $user->id_entreprise)
                    ->where('actif', 1)
                    ->get();
            }

            $postes = $this->postesPourFormulaireUtilisateur();

            // Utiliser la nouvelle méthode pour récupérer les profils autorisés
            $profiles = $this->getAuthorizedProfiles();

            return view('user.utilisateur.index', compact('utilisateurs', 'entreprises', 'postes', 'profiles'));

        } catch (\Exception $e) {
            Log::error('Erreur dans UserController@index: '.$e->getMessage().' Line: '.$e->getLine());

            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /*----------------------------------------------------------------------------------------------------------------------*/
    public function index_users_desactivate()
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de consultation des inactifs
            $hasPermission = $user->hasPermission('Gestion des Utilisateurs', 'inactif');
            if (! $hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter les utilisateurs inactifs.');
            }

            $entreprises = $this->getAuthorizedEntreprises();

            if ($user->isSuperAdmin()) {
                $utilisateurs = User::where('actif', 0)->get();
            } elseif (! $user->id_entreprise) {
                $utilisateurs = collect();
            } else {
                $utilisateurs = User::where('id_entreprise', $user->id_entreprise)
                    ->where('actif', 0)
                    ->get();
            }

            $postes = $this->postesPourFormulaireUtilisateur();

            // Utiliser la nouvelle méthode pour récupérer les profils autorisés
            $profiles = $this->getAuthorizedProfiles();

            return view('user.utilisateur.index', compact('utilisateurs', 'entreprises', 'postes', 'profiles'));

        } catch (\Exception $e) {
            Log::error('Erreur dans UserController@index_users_desactivate: '.$e->getMessage().' Line: '.$e->getLine());

            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /*----------------------------------------------------------------------------------------------------------------------*/
    public function store(Request $request)
    {

        try {
            $user = Auth::user();

            if (! $user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de création
            $hasPermission = $user->hasPermission('Gestion des Utilisateurs', 'ajouter');
            if (! $hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de créer des utilisateurs.');
            }

            if (! $user->isSuperAdmin()) {
                $request->merge(['id_entreprise' => $user->id_entreprise]);
            }

            // Vérifier que l'établissement est autorisé (si fourni)
            if ($request->id_entreprise && ! $this->canAccessEntreprise($request->id_entreprise)) {
                return redirect()->back()
                    ->with('error', 'Vous n\'avez pas accès à cet établissement.')
                    ->withInput();
            }

            $request->merge([
                'telephone' => $this->digitsOnlyPhone($request->input('telephone')),
            ]);

            // Validation globale
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'telephone' => ['required', 'regex:/^0[67]\d{8}$/'],
                'id_entreprise' => 'nullable|exists:entreprises,id',
                'id_poste' => ['required', Rule::in($this->idsPostesUtilisateurAutorises())],
                'profiles' => 'required|array|min:1',
                'profiles.*' => 'exists:profiles,id',
                'password' => ['required', 'confirmed', Password::min(8)],
            ], [
                'telephone.required' => 'Le téléphone est obligatoire.',
                'telephone.regex' => 'Le téléphone doit comporter 10 chiffres et commencer par 06 ou 07.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::transaction(function () use ($request, $user) {
                // Création de l'utilisateur
                $newUser = User::create([
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'id_entreprise' => $request->id_entreprise,
                    'id_poste' => $request->id_poste,
                    'password' => Hash::make($request->password),
                    'active_profile_id' => $request->profiles[0] ?? null,
                    'user_insert' => $user->id,
                ]);

                // Sauvegarder les profils
                foreach ($request->profiles as $profileId) {
                    UserProfile::create([
                        'id_user' => $newUser->id,
                        'id_profile' => $profileId,
                    ]);
                }

                // Log utilisateur
                Log::channel('user_log')->info("👤 Utilisateur : {$user->id} - {$user->nom} {$user->prenom}", []);
                Log::channel('user_log')->info('✅ Utilisateur créé', [
                    '🆔 ID Utilisateur' => $newUser->id,
                    '📝 Données' => $request->except(['_token', '_method', 'password', 'password_confirmation']),
                    '👥 Profils assignés' => $request->profiles,
                ]);
                Log::channel('user_log')->info(str_repeat('*', 100));
            });

            return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur créé avec succès.');

        } catch (\Exception $e) {
            $currentUser = Auth::user();
            if ($currentUser) {
                Log::channel('user_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('user_log')->error("❌ Erreur lors de la création de l'utilisateur", [
                    '⚠️ Message' => $e->getMessage(),
                    '📍 Ligne' => $e->getLine(),
                    '📝 Données tentées' => $request->except(['_token', '_method', 'password', 'password_confirmation']),
                ]);
                Log::channel('user_log')->info(str_repeat('*', 100));
            }

            return redirect()->back()->with('error', 'Erreur lors de la création de l\'utilisateur.')->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de modification
            $hasPermission = $user->hasPermission('Gestion des Utilisateurs', 'modifier');
            if (! $hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de modifier des utilisateurs.');
            }

            $userToUpdate = User::findOrFail($id);

            // Vérifier que l'utilisateur peut accéder à l'établissement actuel de l'utilisateur à modifier
            if (! $this->canAccessEntreprise($userToUpdate->id_entreprise)) {
                return redirect()->back()->with('error', 'Vous n\'avez pas accès à cet utilisateur.');
            }

            if (! $user->isSuperAdmin()) {
                $request->merge(['id_entreprise' => $user->id_entreprise]);
            }

            // Vérifier que le nouvel établissement est autorisé (si fourni)
            if ($request->id_entreprise && ! $this->canAccessEntreprise($request->id_entreprise)) {
                return redirect()->back()->with('error', 'Vous n\'avez pas accès au nouvel établissement sélectionné.')
                    ->withInput()
                    ->with('failed_modal', 'EditModal-'.$id);
            }

            DB::transaction(function () use ($request, $id) {
                $userToUpdate = User::findOrFail($id);

                $telDigits = $this->digitsOnlyPhone($request->input('telephone'));
                $request->merge([
                    'telephone' => $telDigits === '' ? null : $telDigits,
                ]);

                $validator = Validator::make($request->all(), [
                    'nom' => 'required|string|max:255',
                    'prenom' => 'required|string|max:255',
                    'email' => [
                        'required',
                        'string',
                        'email',
                        'max:255',
                        Rule::unique('users')->ignore($userToUpdate->id),
                    ],
                    'telephone' => ['nullable', 'regex:/^0[67]\d{8}$/'],
                    'id_entreprise' => 'nullable|exists:entreprises,id',
                    'id_poste' => ['required', Rule::in($this->idsPostesAcceptesPourMiseAJour($userToUpdate))],
                    'profiles' => 'required|array|min:1',
                    'profiles.*' => 'exists:profiles,id',
                    'password' => ['nullable', 'confirmed', Password::min(8)],
                ], [
                    'telephone.regex' => 'Le téléphone doit comporter 10 chiffres et commencer par 06 ou 07.',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput()
                        ->with('failed_modal', 'EditModal-'.$id);
                }

                // Mettre à jour les données de l'utilisateur (sans id_profile)
                $updateData = [
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'id_entreprise' => $request->id_entreprise,
                    'id_poste' => $request->id_poste,
                    'user_update' => auth()->user()->id,
                    'active_profile_id' => $request->profiles[0] ?? null,
                ];

                if ($request->password) {
                    $updateData['password'] = Hash::make($request->password);
                }

                $userToUpdate->update($updateData);

                // Supprimer les anciens profils de l'utilisateur
                UserProfile::where('id_user', $userToUpdate->id)->delete();

                // Ajouter les nouveaux profils
                foreach ($request->profiles as $profileId) {
                    UserProfile::create([
                        'id_user' => $userToUpdate->id,
                        'id_profile' => $profileId,
                    ]);
                }

                $currentUser = Auth::user();
                Log::channel('user_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('user_log')->info('✅ Utilisateur mis à jour', [
                    '🆔 ID Utilisateur' => $id,
                    '📝 Nouvelles données' => $request->except(['_token', '_method', 'password', 'password_confirmation']),
                    '👥 Profils assignés' => $request->profiles,
                ]);
                Log::channel('user_log')->info(str_repeat('*', 100));
            });

            return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur modifié avec succès.');

        } catch (\Exception $e) {
            $currentUser = Auth::user();
            if ($currentUser) {
                Log::channel('user_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('user_log')->error("❌ Erreur lors de la mise à jour de l'utilisateur", [
                    '🆔 ID Utilisateur' => $id,
                    '⚠️ Message' => $e->getMessage(),
                    '📍 Ligne' => $e->getLine(),
                    '📝 Données tentées' => $request->except(['_token', '_method', 'password', 'password_confirmation']),
                ]);
                Log::channel('user_log')->info(str_repeat('*', 100));
            }

            return redirect()->back()->with('error', 'Erreur lors de la modification de l\'utilisateur.')->withInput();
        }
    }

    /*----------------------------------------------------------------------------------------------------------------------*/
    public function activate($id)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission d'activation
            $hasPermission = $user->hasPermission('Gestion des Utilisateurs', 'activer');
            if (! $hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission d\'activer des utilisateurs.');
            }

            $userToActivate = User::findOrFail($id);

            // Vérifier que l'utilisateur peut accéder à l'établissement de l'utilisateur à activer
            if (! $this->canAccessEntreprise($userToActivate->id_entreprise)) {
                return redirect()->back()->with('error', 'Vous n\'avez pas accès à cet utilisateur.');
            }

            $userToActivate->actif = 1;
            $userToActivate->user_update = Auth::id();
            $userToActivate->save();

            $currentUser = Auth::user();
            Log::channel('user_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
            Log::channel('user_log')->info('✅ Utilisateur réactivé', [
                '🆔 ID Utilisateur' => $id,
                '📝 Données' => ['actif' => 1, 'user_update' => $currentUser->id],
            ]);
            Log::channel('user_log')->info(str_repeat('*', 100));

            return redirect()->route('utilisateurs.index_desactivate')->with('success', 'Utilisateur restauré avec succès.');

        } catch (\Exception $e) {
            $currentUser = Auth::user();
            if ($currentUser) {
                Log::channel('user_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('user_log')->error("❌ Erreur lors de la restauration de l'utilisateur", [
                    '🆔 ID Utilisateur' => $id,
                    '⚠️ Message' => $e->getMessage(),
                    '📍 Ligne' => $e->getLine(),
                ]);
                Log::channel('user_log')->info(str_repeat('*', 100));
            }

            return redirect()->back()->with('error', 'Erreur lors de la restauration de l\'utilisateur.');
        }
    }

    /*----------------------------------------------------------------------------------------------------------------------*/
    public function desactivate($id)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de désactivation
            $hasPermission = $user->hasPermission('Gestion des Utilisateurs', 'desactiver');
            if (! $hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de désactiver des utilisateurs.');
            }

            $userToDeactivate = User::findOrFail($id);

            // Vérifier que l'utilisateur peut accéder à l'établissement de l'utilisateur à désactiver
            if (! $this->canAccessEntreprise($userToDeactivate->id_entreprise)) {
                return redirect()->back()->with('error', 'Vous n\'avez pas accès à cet utilisateur.');
            }

            $userToDeactivate->actif = 0;
            $userToDeactivate->user_update = Auth::id();
            $userToDeactivate->save();

            $currentUser = Auth::user();
            if ($currentUser && $currentUser->id === $userToDeactivate->id) {
                Auth::logout();

                return redirect()->route('login')->with('message', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
            } else {
                $sessions = DB::table('sessions')->where('id_user', $userToDeactivate->id)->delete();
            }

            Log::channel('user_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
            Log::channel('user_log')->info('🚫 Utilisateur désactivé', [
                '🆔 ID Utilisateur' => $id,
                '📝 Données' => ['actif' => 0, 'user_update' => $currentUser->id],
            ]);
            Log::channel('user_log')->info(str_repeat('*', 100));

            return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur désactivé avec succès.');

        } catch (\Exception $e) {
            $currentUser = Auth::user();
            if ($currentUser) {
                Log::channel('user_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('user_log')->error("❌ Erreur lors de la désactivation de l'utilisateur", [
                    '🆔 ID Utilisateur' => $id,
                    '⚠️ Message' => $e->getMessage(),
                    '📍 Ligne' => $e->getLine(),
                ]);
                Log::channel('user_log')->info(str_repeat('*', 100));
            }

            return redirect()->back()->with('error', 'Erreur lors de la désactivation de l\'utilisateur.');
        }
    }

    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $originalEmail = $request->input('email');

        // 1. Check if the originally provided email exists
        if (! User::where('email', $originalEmail)->exists()) {
            return response()->json(['exists' => false]);
        }

        // 2. If it exists, generate a new suggestion
        // Example: turns "i.belahcen@chuis.ma" into "i" and ".belahcen@chuis.ma"
        $parts = explode('@', $originalEmail);
        $localPart = $parts[0];
        $domain = $parts[1];

        // Find the position of the first letter and the rest of the local part
        // This is robust enough to handle "i.belahcen" or even "i1.belahcen"
        preg_match('/^([a-zA-Z]+)(\d*)(\..*)$/', $localPart, $matches);

        // If the pattern doesn't match f.lastname, fallback gracefully
        if (count($matches) !== 4) {
            // Can't automatically suggest a new email, just report it exists
            return response()->json(['exists' => true, 'suggestion' => null]);
        }

        $prefix = $matches[1]; // "i"
        $rest = $matches[3];   // ".belahcen"
        $counter = 1;

        while (true) {
            // Create a new email like i1.belahcen@chuis.ma, then i2.belahcen@chuis.ma
            $suggestedEmail = $prefix.$counter.$rest.'@'.$domain;

            // If we found an email that does NOT exist, we have our suggestion
            if (! User::where('email', $suggestedEmail)->exists()) {
                return response()->json([
                    'exists' => true, // The original one exists
                    'suggestion' => $suggestedEmail, // Here is the new one
                ]);
            }

            // Otherwise, increment the counter and try the next number
            $counter++;

            // Safety break to prevent infinite loops in unexpected scenarios
            if ($counter > 50) {
                return response()->json(['exists' => true, 'suggestion' => null]);
            }
        }
    }
}
