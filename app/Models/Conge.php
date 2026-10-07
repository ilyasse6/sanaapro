<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conge extends Model
{
    use BelongsToEntreprise;

    protected $fillable = [
        'id_entreprise',
        'employe_id', 'date_debut', 'date_fin', 'type_conge', 'statut',
        'motif', 'commentaire_rh', 'user_insert', 'user_update',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }
}
