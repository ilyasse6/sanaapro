<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactureLigne extends Model
{
    protected $fillable = [
        'facture_id', 'type_entree', 'catalogue_service_id', 'catalogue_pack_id',
        'designation', 'quantite', 'unite', 'prix_unitaire_ht', 'remise_pct', 'ordre',
    ];

    protected $casts = [
        'quantite' => 'decimal:3',
        'prix_unitaire_ht' => 'decimal:2',
        'remise_pct' => 'decimal:2',
    ];

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }

    public function getMontantHtAttribute(): float
    {
        $pu = (float) $this->prix_unitaire_ht;
        if ($this->remise_pct > 0) {
            $pu *= 1 - ((float) $this->remise_pct / 100);
        }

        return round((float) $this->quantite * $pu, 2);
    }
}
