<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
    use BelongsToEntreprise;

    protected $fillable = [
        'id_entreprise',
        'raison_sociale', 'ice', 'email', 'telephone', 'adresse', 'ville', 'code_postal',
        'notes', 'actif', 'user_insert', 'user_update',
    ];

    protected $casts = ['actif' => 'boolean'];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function bonsCommande(): HasMany
    {
        return $this->hasMany(BonCommande::class);
    }
}
