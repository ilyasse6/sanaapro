<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonCommandeLigne extends Model
{
    protected $table = 'bon_commande_lignes';

    protected $fillable = [
        'bon_commande_id',
        'reference_produit',
        'designation',
        'quantite',
        'unite',
        'prix_unitaire_ht',
        'ordre',
    ];

    protected $casts = [
        'quantite' => 'decimal:3',
        'prix_unitaire_ht' => 'decimal:2',
    ];

    public function bonCommande(): BelongsTo
    {
        return $this->belongsTo(BonCommande::class);
    }

    public function getMontantHtAttribute(): float
    {
        if ($this->prix_unitaire_ht === null) {
            return 0.0;
        }

        return round((float) $this->quantite * (float) $this->prix_unitaire_ht, 2);
    }
}
