<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employe extends Model
{
    use BelongsToEntreprise;

    protected $fillable = [
        'id_entreprise',
        'matricule', 'nom', 'prenom', 'email', 'telephone', 'poste_libelle',
        'date_embauche', 'salaire_mensuel', 'user_id', 'actif', 'user_insert', 'user_update',
    ];

    protected $casts = [
        'date_embauche' => 'date',
        'salaire_mensuel' => 'decimal:2',
        'actif' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conges(): HasMany
    {
        return $this->hasMany(Conge::class);
    }

    public function comptaMouvements(): HasMany
    {
        return $this->hasMany(ComptaMouvement::class);
    }
}
