<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarcheDocument extends Model
{
    protected $fillable = [
        'marche_id', 'nom_fichier', 'chemin', 'mime', 'taille_octets', 'type_document', 'user_insert',
    ];

    public function marche(): BelongsTo
    {
        return $this->belongsTo(Marche::class);
    }
}
