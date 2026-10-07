<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarcheLot extends Model
{
    protected $fillable = [
        'marche_id', 'code', 'libelle', 'montant', 'statut', 'description',
    ];

    protected $casts = ['montant' => 'decimal:2'];

    public function marche(): BelongsTo
    {
        return $this->belongsTo(Marche::class);
    }
}
