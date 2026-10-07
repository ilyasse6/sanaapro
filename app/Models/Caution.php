<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caution extends Model
{
    use BelongsToEntreprise;

    protected $fillable = [
        'id_entreprise',
        'marche_id', 'libelle', 'type_caution', 'montant', 'organisme', 'numero_garantie',
        'date_debut', 'date_fin', 'statut', 'notes', 'user_insert', 'user_update',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function marche(): BelongsTo
    {
        return $this->belongsTo(Marche::class);
    }

    public function getExpireBientotAttribute(): bool
    {
        if (! $this->date_fin) {
            return false;
        }
        $eid = $this->marche?->id_entreprise;
        $jours = (int) \App\Models\AppSetting::get('alerte_caution_jours', 30, $eid);

        return $this->date_fin->lte(now()->addDays($jours)) && $this->date_fin->gte(now());
    }
}
