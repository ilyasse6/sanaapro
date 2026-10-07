<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
//facade DB
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'gmail',
        'telephone',
        'date_embauche',
        'horaires_travail',
        'id_entreprise',
        'id_poste',
        'active_profile_id',
        'email_verified_at',
        'password',
        'actif',
        'user_insert',
        'user_update',
        'remember_token',
    ];

    public function ActStaff()
    {
        return $this->hasMany(ActStaff::class, 'user_insert', 'id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_embauche' => 'date',
            'password' => 'hashed',
        ];
    }

    // Relations
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }

    public function poste_occupe()
    {
        return $this->belongsTo(Poste::class, 'id_poste');
    }

    public function getActiveProfileIdsAttribute()
    {
        if (empty($this->active_profile_id)) {
            return [];
        }

        return array_map('intval', explode(',', $this->active_profile_id));
    }

    /**
     * Définir les IDs des profils actifs à partir d'un tableau
     */
    public function setActiveProfileIdsAttribute($profileIds)
    {
        if (empty($profileIds)) {
            $this->attributes['active_profile_id'] = null;
        } else {
            $this->attributes['active_profile_id'] = implode(',', array_filter($profileIds));
        }
    }

    /**
     * Vérifier si un profil spécifique est actif
     */
    public function hasActiveProfile($profileId)
    {
        return in_array($profileId, $this->active_profile_ids);
    }

    /**
     * Vérifier si tous les profils sont activés
     */
    public function hasAllProfilesActive()
    {
        return empty($this->active_profile_id);
    }

    /**
     * Obtenir les profils actuellement actifs
     */
    public function getActiveProfilesAttribute()
    {
        if ($this->hasAllProfilesActive()) {
            return $this->profiles;
        }

        return $this->profiles->whereIn('id', $this->active_profile_ids);
    }

    /*------------------------------------------------------------------------------------------------------*/
    /**
     * Relation Many-to-Many avec Profile via la table pivot user_profiles
     */
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'user_profiles', 'id_user', 'id_profile')
            ->withPivot('actif')
            ->withTimestamps();
    }

    public function hasProfile($profileId)
    {
        // This checks for the existence of a profile in the user_profiles pivot table.
        // It's very efficient as it performs a `WHERE EXISTS` query.
        return $this->profiles()->where('id_profile', $profileId)->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->profiles()
            ->where('profiles.nom', 'Super Admin')
            ->where('user_profiles.actif', 1)
            ->exists();
    }

    public function isDirecteur(): bool
    {
        return $this->profiles()
            ->where('profiles.nom', 'Directeur')
            ->where('user_profiles.actif', 1)
            ->exists();
    }

    public function isComptable(): bool
    {
        $query = $this->profiles()
            ->where('profiles.nom', 'Comptable')
            ->where('user_profiles.actif', 1);

        if (! $this->hasAllProfilesActive()) {
            $query->whereIn('profiles.id', $this->active_profile_ids);
        }

        return $query->exists();
    }

    /**
     * Relation One-to-Many avec UserProfile
     */
    public function userProfiles()
    {
        return $this->hasMany(UserProfile::class, 'id_user');
    }

    /*-------------------------------------------------------------------------------------------------------*/
    public function hasPermission($moduleName, $action)
    {
        // Vérifier si l'utilisateur est actif
        if (! $this->actif) {
            return false;
        }

        // Récupérer le module
        $module = DB::table('modules')
            ->where('nom', $moduleName)
            ->where('actif', 1)
            ->first();

        if (! $module) {
            return false;
        }

        // Récupérer la permission spécifique dans ce module
        $permission = DB::table('permissions')
            ->where('id_module', $module->id)
            ->where('action', $action)
            ->where('actif', 1)
            ->first();

        if (! $permission) {
            return false;
        }

        // Gérer les profils multiples
        $userProfiles = [];

        if ($this->hasAllProfilesActive()) {
            // CAS 1: Tous les profils sont activés
            $userProfiles = DB::table('user_profiles')
                ->join('profiles', 'user_profiles.id_profile', '=', 'profiles.id')
                ->where('user_profiles.id_user', $this->id)
                ->where('user_profiles.actif', 1)
                ->where('profiles.actif', 1)
                ->pluck('profiles.id')
                ->toArray();
        } else {
            // CAS 2: Des profils spécifiques sont sélectionnés
            $selectedProfileIds = $this->active_profile_ids;

            if (empty($selectedProfileIds)) {
                return false;
            }

            $validProfiles = DB::table('user_profiles')
                ->join('profiles', 'user_profiles.id_profile', '=', 'profiles.id')
                ->where('user_profiles.id_user', $this->id)
                ->whereIn('user_profiles.id_profile', $selectedProfileIds)
                ->where('user_profiles.actif', 1)
                ->where('profiles.actif', 1)
                ->pluck('profiles.id')
                ->toArray();

            if (count($validProfiles) !== count($selectedProfileIds)) {
                return false;
            }

            $userProfiles = $validProfiles;
        }

        if (empty($userProfiles)) {
            return false;
        }

        // Vérifier si au moins un des profils actifs de l'utilisateur a cette permission
        $hasPermission = DB::table('profile_permissions')
            ->whereIn('id_profile', $userProfiles)
            ->where('actif', 1)
            ->where(function ($q) {
                // Vérifier les dates de validité
                $q->where(function ($dateQuery) {
                    $dateQuery->whereNull('date_debut')
                        ->orWhere('date_debut', '<=', now());
                })
                    ->where(function ($dateQuery) {
                        $dateQuery->whereNull('date_fin')
                            ->orWhere('date_fin', '>=', now());
                    });
            })
            ->where(function ($mainQuery) use ($permission) {

                // CAS 1 : Super Admin — aucun périmètre
                $mainQuery->orWhere(function ($q) {
                    $q->whereNull('id_permission')->whereNull('id_entreprise');
                })
                // CAS 2 : admin d'établissement — périmètre = entreprise de l'utilisateur
                    ->orWhere(function ($q) {
                        $eid = $this->id_entreprise;
                        if ($eid === null) {
                            $q->whereRaw('1 = 0');

                            return;
                        }
                        $q->whereNull('id_permission')
                            ->whereNotNull('id_entreprise')
                            ->where('profile_permissions.id_entreprise', $eid);
                    })
                // CAS 3 : permission explicite, portée globale
                    ->orWhere(function ($q) use ($permission) {
                        $q->where('id_permission', $permission->id)->whereNull('id_entreprise');
                    })
                // CAS 4 : permission explicite, limitée à l'entreprise de l'utilisateur
                    ->orWhere(function ($q) use ($permission) {
                        $eid = $this->id_entreprise;
                        if ($eid === null) {
                            $q->whereRaw('1 = 0');

                            return;
                        }
                        $q->where('id_permission', $permission->id)
                            ->whereNotNull('id_entreprise')
                            ->where('profile_permissions.id_entreprise', $eid);
                    });
            })
            ->exists();

        return $hasPermission;
    }

    /**
     * Use the user's gmail for mail notifications if available.
     */
    public function routeNotificationForMail(): ?string
    {
        return $this->gmail ?: null;
    }

    /**
     * Résout un identifiant de connexion : e-mail complet, Gmail secondaire, ou partie locale (identifiant avant @).
     *
     * @throws ValidationException
     */
    public static function emailFromLoginIdentifier(string $login, string $errorField = 'login'): string
    {
        $login = trim($login);
        if ($login === '') {
            throw ValidationException::withMessages([
                $errorField => __('validation.required', ['attribute' => $errorField]),
            ]);
        }

        if (str_contains($login, '@')) {
            $email = strtolower($login);
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages([
                    $errorField => 'Adresse e-mail invalide.',
                ]);
            }

            $byPrimary = static::query()->whereRaw('LOWER(email) = ?', [$email])->first();
            if ($byPrimary) {
                return $byPrimary->email;
            }

            $byGmail = static::query()
                ->whereNotNull('gmail')
                ->where('gmail', '!=', '')
                ->whereRaw('LOWER(gmail) = ?', [$email])
                ->first();
            if ($byGmail) {
                return $byGmail->email;
            }

            return $email;
        }

        $local = strtolower($login);
        $fromEmail = static::query()
            ->whereRaw('LOWER(email) LIKE ?', [$local.'@%'])
            ->pluck('email');
        $fromGmail = static::query()
            ->whereNotNull('gmail')
            ->where('gmail', '!=', '')
            ->whereRaw('LOWER(gmail) LIKE ?', [$local.'@%'])
            ->pluck('email');
        $merged = $fromEmail->merge($fromGmail)->unique()->values();

        if ($merged->count() === 1) {
            return $merged->first();
        }
        if ($merged->count() > 1) {
            throw ValidationException::withMessages([
                $errorField => 'Plusieurs comptes correspondent. Utilisez votre adresse e-mail complète.',
            ]);
        }

        throw ValidationException::withMessages([
            $errorField => trans('auth.failed'),
        ]);
    }
}
