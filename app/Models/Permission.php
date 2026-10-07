<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'id',
        'nom',
        'id_module',
        'action',
        'actif',
        'created_at',
        'updated_at',
    ];

    // Relations
    public function module()
    {
        return $this->belongsTo(Module::class, 'id_module');
    }

    public function profilePermissions()
    {
        return $this->hasMany(ProfilePermission::class, 'id_permission');
    }

    // Relation Many-to-Many avec Profile via profile_permissions
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_permissions', 'id_permission', 'id_profile')
            ->withPivot('id_entreprise', 'actif', 'date_debut', 'date_fin', 'user_insert', 'user_update')
            ->withTimestamps();
    }
}
