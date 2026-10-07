<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePermission extends Model
{
    protected $table = 'profile_permissions';

    protected $fillable = [
        'id_profile',
        'id_permission',
        'id_entreprise',
        'date_debut',
        'date_fin',
        'actif',
        'created_at',
        'updated_at',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'id_profile');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'id_permission');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }
}
