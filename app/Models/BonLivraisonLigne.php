<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonLivraisonLigne extends Model
{
    protected $table = 'bon_livraison_lignes';

    protected $fillable = [
        'bon_livraison_id',
        'reference_produit',
        'designation',
        'quantite',
        'unite',
        'ordre',
    ];

    protected $casts = [
        'quantite' => 'decimal:3',
    ];

    public function bonLivraison(): BelongsTo
    {
        return $this->belongsTo(BonLivraison::class);
    }
}
