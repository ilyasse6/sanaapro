<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles';

    protected $fillable = [
        'nom',
        'description',
        'actif',
        'created_at',
        'updated_at',
    ];
    // // Relations
    // public function users()
    // {
    //     return $this->hasMany(User::class, 'id_profile');
    // }

    public function profilePermissions()
    {
        return $this->hasMany(ProfilePermission::class, 'id_profile');
    }

    // Relation Many-to-Many avec Permission via profile_permissions
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'profile_permissions', 'id_profile', 'id_permission')
            ->withPivot('id_entreprise', 'actif', 'date_debut', 'date_fin', 'user_insert', 'user_update')
            ->withTimestamps();
    }

    /**
     * Relation Many-to-Many avec User via la table pivot user_profiles
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_profiles', 'id_profile', 'id_user')
            ->withPivot('actif')
            ->withTimestamps();
    }

    /**
     * Relation One-to-Many avec UserProfile
     */
    public function userProfiles()
    {
        return $this->hasMany(UserProfile::class, 'id_profile');
    }
}
