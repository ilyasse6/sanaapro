<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Profile;
use App\Models\ProfilePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfilController extends Controller
{
    /**
     * Vérifier si l'utilisateur est un super admin
     */
    private function isSuperAdmin()
    {
        try {
            $user = Auth::user();

            if (! $user || ! $user->actif) {
                return false;
            }

            // Gérer les profils multiples
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
                    return false;
                }
                $validProfiles = DB::table('user_profiles')
                    ->join('profiles', 'user_profiles.id_profile', '=', 'profiles.id')
                    ->where('user_profiles.id_user', $user->id)
                    ->whereIn('user_profiles.id_profile', $selectedProfileIds)
                    ->where('user_profiles.actif', 1)
                    ->where('profiles.actif', 1)
                    ->pluck('profiles.id')
                    ->toArray();

                $userProfiles = $validProfiles;
            }

            if (empty($userProfiles)) {
                return false;
            }

            // Vérifier s'il y a une permission globale (Super Admin)
            $superAdminPermission = DB::table('profile_permissions')
                ->whereIn('id_profile', $userProfiles)
                ->where('actif', 1)
                ->whereNull('id_permission')
                ->whereNull('id_entreprise')
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
                ->exists();

            return $superAdminPermission;

        } catch (\Exception $e) {
            Log::error('Erreur dans isSuperAdmin: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Récupérer les profils autorisés selon les permissions géographiques
     */
    private function getAuthorizedProfiles()
    {
        try {
            $user = Auth::user();

            if (! $user || ! $user->actif) {
                return collect();
            }

            // Si l'utilisateur est Super Admin, retourner tous les profils
            if ($this->isSuperAdmin()) {
                return Profile::where('actif', 1)->get();
            }

            // Récupérer les établissements autorisés
            $authorizedEtablissements = $this->getAuthorizedEntreprises();

            if ($authorizedEtablissements->isEmpty()) {
                return collect();
            }

            $authorizedEtablissementIds = $authorizedEtablissements->pluck('id')->unique();

            $profileIds = DB::table('profile_permissions')
                ->where('actif', 1)
                ->where(function ($query) use ($authorizedEtablissementIds) {
                    $query->whereIn('id_entreprise', $authorizedEtablissementIds)
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
                ->unique();

            return Profile::whereIn('id', $profileIds)->where('actif', 1)->get();

        } catch (\Exception $e) {
            Log::error('Erreur dans getAuthorizedProfiles: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Récupérer les régions autorisées pour l'utilisateur connecté
     */
    /**
     * Récupérer les établissements autorisés pour l'utilisateur connecté
     */
    private function getAuthorizedEntreprises()
    {
        try {
            $user = Auth::user();

            // Vérifier si l'utilisateur est actif
            if (! $user || ! $user->actif) {
                return collect();
            }

            // Gérer les profils multiples (même logique que hasPermission)
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

            // Récupérer les permissions géographiques
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

            $entrepriseIds = collect();

            foreach ($permissions as $permission) {
                if (is_null($permission->id_permission) &&
                    is_null($permission->id_entreprise)) {
                    return Entreprise::where('actif', 1)->get();
                }

                if (! is_null($permission->id_entreprise)) {
                    $entrepriseIds->push($permission->id_entreprise);
                }
            }

            // Retourner les établissements uniques
            $uniqueIds = $entrepriseIds->unique()->values();

            if ($uniqueIds->isEmpty()) {
                return collect();
            }

            return Entreprise::whereIn('id', $uniqueIds)->where('actif', 1)->get();

        } catch (\Exception $e) {
            Log::error('Erreur dans getAuthorizedEntreprises: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * @deprecated Régions retirées du modèle métier
     */
    private function getAuthorizedRegions()
    {
        return collect();
    }

    /**
     * @deprecated Villes retirées du modèle métier
     */
    private function getAuthorizedVilles()
    {
        return collect();
    }

    /**
     * Vérifier si l'utilisateur peut accéder à une région spécifique
     */
    private function canAccessRegion($regionId)
    {
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut accéder à une ville spécifique
     */
    private function canAccessVille($villeId)
    {
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut accéder à un établissement spécifique
     */
    private function canAccessEntreprise($entrepriseId)
    {
        try {
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
     * Display a listing of active profiles
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de consultation
            $hasPermission = $user->hasPermission('Gestion des Profiles', 'consulter');
            if (! $hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter les profils.');
            }

            // Récupérer les profils selon les permissions géographiques
            $profiles = $this->getAuthorizedProfiles();

            $modules = Module::where('actif', 1)->with(['permissions' => function ($query) {
                $query->where('actif', 1);
            }])->get();

            $entreprises = $this->getAuthorizedEntreprises();
            $permissions = Permission::where('actif', 1)->get();

            return view('user.profil.index', compact('profiles', 'modules', 'entreprises', 'permissions'));

        } catch (\Exception $e) {
            Log::error('Erreur dans ProfilController@index: '.$e->getMessage().' Line: '.$e->getLine());

            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Display a listing of inactive profiles
     */
    public function index_profiles_desactivate()
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de consultation des inactifs
            $hasPermission = $user->hasPermission('Gestion des Profiles', 'inactif');
            if (! $hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter les profils inactifs.');
            }

            // Récupérer les profils inactifs selon les permissions géographiques
            $profiles = $this->getAuthorizedProfilesInactive();
            $modules = Module::where('actif', 1)->with(['permissions' => function ($query) {
                $query->where('actif', 1);
            }])->get();

            $entreprises = $this->getAuthorizedEntreprises();
            $permissions = Permission::where('actif', 1)->get();

            return view('user.profil.index', compact('profiles', 'modules', 'entreprises', 'permissions'));

        } catch (\Exception $e) {
            Log::channel('profile_log')->error('❌ Erreur lors de la récupération des profils inactifs', [
                '⚠️ Message' => $e->getMessage(),
                '➡️ Ligne' => $e->getLine(),
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Récupérer les profils inactifs autorisés selon les permissions géographiques
     */
    private function getAuthorizedProfilesInactive()
    {
        try {
            $user = Auth::user();

            if (! $user || ! $user->actif) {
                return collect();
            }

            // Si l'utilisateur est Super Admin, retourner tous les profils inactifs
            if ($this->isSuperAdmin()) {
                return Profile::where('actif', 0)->paginate(10);
            }

            // Récupérer les établissements autorisés
            $authorizedEtablissements = $this->getAuthorizedEntreprises();

            if ($authorizedEtablissements->isEmpty()) {
                return collect();
            }

            $authorizedEtablissementIds = $authorizedEtablissements->pluck('id')->unique();

            $profileIds = DB::table('profile_permissions')
                ->where(function ($query) use ($authorizedEtablissementIds) {
                    $query->whereIn('id_entreprise', $authorizedEtablissementIds)
                        ->orWhere(function ($q2) {
                            $q2->whereNotNull('id_permission')->whereNull('id_entreprise');
                        });
                })
                ->pluck('id_profile')
                ->unique();

            return Profile::whereIn('id', $profileIds)->where('actif', 0)->paginate(10);

        } catch (\Exception $e) {
            Log::error('Erreur dans getAuthorizedProfilesInactive: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Switch profile functionality
     */
    public function switchProfile(Request $request)
    {
        try {
            $user = Auth::user();

            // Si "tous les profils" est sélectionné
            if ($request->has('all_profiles') && $request->all_profiles == '1') {
                $user->active_profile_id = null;
                $user->save();

                return redirect()->route('home')->with('success', 'Tous les profils ont été activés avec succès');
            }

            // Validation des profils sélectionnés
            $request->validate([
                'selected_profiles' => 'nullable|array',
                'selected_profiles.*' => 'exists:profiles,id',
            ]);

            $selectedProfiles = $request->input('selected_profiles', []);

            // Vérifier que tous les profils sélectionnés sont actifs et appartiennent à l'utilisateur
            if (! empty($selectedProfiles)) {
                $userProfileIds = $user->profiles->where('actif', 1)->pluck('id')->toArray();
                $invalidProfiles = array_diff($selectedProfiles, $userProfileIds);

                if (! empty($invalidProfiles)) {
                    return redirect()->route('home')->with('error', 'Certains profils sélectionnés sont invalides ou inactifs');
                }
            }

            // Mettre à jour les profils actifs
            $user->active_profile_ids = $selectedProfiles;
            $user->save();

            $message = empty($selectedProfiles)
                ? 'Aucun profil spécifique n\'est activé'
                : 'Profils sélectionnés activés avec succès';

            return redirect()->route('home')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Erreur lors de la mise à jour des profils: '.$e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de création
            $hasPermission = $user->hasPermission('Gestion des Profiles', 'ajouter');
            if (! $hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de créer des profils.');
            }

            // Validation des données géographiques selon les permissions
            $this->validateGeographicAccess($request);

            $request->validate([
                'nom' => 'required|string|max:255|unique:profiles,nom',
                'permission_type' => 'nullable|string',
                'selected_entreprises' => 'nullable|array',
                'selected_entreprises.*' => 'exists:entreprises,id',
                'specific_permissions' => 'nullable|array',
            ]);

            DB::beginTransaction();

            // Créer le profil
            $profil = Profile::create([
                'nom' => $request->nom,
                'description' => $request->description ?? null,
                'actif' => 1,
                'user_insert' => auth()->id(),
            ]);

            // Traitement des permissions selon le type
            $this->saveOptimizedPermissions($profil->id, $request);

            $currentUser = Auth::user();
            Log::channel('profile_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
            Log::channel('profile_log')->info('✅ Profil créé', [
                '🆔 ID Profil' => $profil->id,
                '📝 Données' => $request->except(['_token', '_method']),
            ]);
            Log::channel('profile_log')->info(str_repeat('*', 100));

            DB::commit();

            return redirect()->back()->with('success', 'Profil créé avec succès');

        } catch (\Exception $e) {
            DB::rollback();

            $currentUser = Auth::user();
            if ($currentUser) {
                Log::channel('profile_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('profile_log')->error('❌ Erreur lors de la création du profil', [
                    '⚠️ Message' => $e->getMessage(),
                    '📍 Ligne' => $e->getLine(),
                    '📝 Données tentées' => $request->except(['_token', '_method']),
                ]);
                Log::channel('profile_log')->info(str_repeat('*', 100));
            }

            return redirect()->back()->with('error', 'Erreur lors de la création du profil: '.$e->getMessage());
        }
    }

    /**
     * Valider l'accès géographique selon les permissions de l'utilisateur
     */
    private function validateGeographicAccess($request)
    {
        if ($request->has('selected_entreprises') && is_array($request->selected_entreprises)) {
            foreach ($request->selected_entreprises as $entrepriseId) {
                if (! $this->canAccessEntreprise($entrepriseId)) {
                    throw new \Exception('Vous n\'avez pas accès à une ou plusieurs entreprises sélectionnées.');
                }
            }
        }
    }

    private function saveOptimizedPermissions($profileId, $request)
    {
        $permissionsData = [];
        $base = [
            'actif' => 1,
            'date_debut' => now(),
            'user_insert' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($request->permission_type === 'global') {
            $permissionsData[] = array_merge($base, [
                'id_profile' => $profileId,
                'id_permission' => null,
                'id_entreprise' => null,
            ]);
        }

        if ($request->has('selected_entreprises') && is_array($request->selected_entreprises)) {
            foreach ($request->selected_entreprises as $entrepriseId) {
                $permissionsData[] = array_merge($base, [
                    'id_profile' => $profileId,
                    'id_permission' => null,
                    'id_entreprise' => $entrepriseId,
                ]);
            }
        }

        if ($request->has('specific_permissions') && ! empty($request->specific_permissions)) {
            foreach ($this->groupSpecificPermissions($request->specific_permissions) as $permission) {
                $permissionsData[] = array_merge($base, [
                    'id_profile' => $profileId,
                    'id_permission' => $permission['permission_id'] ?? null,
                    'id_entreprise' => $permission['entreprise_id'] ?? null,
                ]);
            }
        }

        if (! empty($permissionsData)) {
            ProfilePermission::insert($permissionsData);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $specificPermissions
     * @return array<int, array{permission_id: mixed, entreprise_id: mixed}>
     */
    private function groupSpecificPermissions($specificPermissions)
    {
        $out = [];
        $seen = [];

        foreach ($specificPermissions as $permission) {
            $pid = $permission['permission_id'] ?? null;
            $eid = $permission['entreprise_id'] ?? null;
            if ($this->isPermissionGlobalInArray((int) $pid, $specificPermissions)) {
                $eid = null;
            }
            $key = ($pid ?? 'null').'|'.($eid ?? 'null');
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = ['permission_id' => $pid, 'entreprise_id' => $eid];
        }

        return $out;
    }

    private function isPermissionGlobalInArray($permissionId, $allPermissions)
    {
        $totalEntreprises = DB::table('entreprises')->where('actif', 1)->count();
        if ($totalEntreprises === 0) {
            return false;
        }

        $permissionCount = 0;
        foreach ($allPermissions as $permission) {
            if (($permission['permission_id'] ?? null) == $permissionId) {
                $permissionCount++;
            }
        }

        return $permissionCount >= $totalEntreprises;
    }

    public function desactivate($id)
    {
        try {
            $profile = Profile::findOrFail($id);
            $profile->actif = 0;
            $profile->save();
            $currentUser = Auth::user();
            // Logs
            Log::channel('poste_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
            Log::channel('profile_log')->info('✅ Profil désactivé', [
                '🆔 ID Profil' => $profile->id,
                '📝 Nom' => $profile->nom,
                '🗓️ Date de désactivation' => now(),
            ]);

            return redirect()->back()->with('success', 'Profil désactivé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la désactivation du profil: '.$e->getMessage());
        }
    }

    /*---------------------------------------------------------------------------------------------------------*/
    public function activate($id)
    {
        try {
            $profile = Profile::findOrFail($id);
            $profile->actif = 1;
            $profile->save();
            $currentUser = Auth::user();
            // Logs
            Log::channel('poste_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
            Log::channel('profile_log')->info('✅ Profil activé', [
                '🆔 ID Profil' => $profile->id,
                '📝 Nom' => $profile->nom,
                '🗓️ Date d\'activation' => now(),
            ]);

            return redirect()->back()->with('success', 'Profil activé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'activation du profil: '.$e->getMessage());
        }
    }
    /*---------------------------------------------------------------------------------------------------------*/

}
