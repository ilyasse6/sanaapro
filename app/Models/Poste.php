<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poste extends Model
{
    protected $fillable = [
        'intitule',
        'description',
        'actif',
        'user_insert',
        'user_update',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_poste');
    }
    public function participants()
    {
        return $this->hasMany(Participant::class, 'id_poste', 'id');
    }
}
