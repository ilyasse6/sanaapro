<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComptaMouvement extends Model
{
    use BelongsToEntreprise;

    protected $fillable = [
        'id_entreprise',
        'type', 'montant', 'date_operation', 'libelle', 'mode_paiement', 'reference_externe',
        'client_id', 'fournisseur_id', 'facture_id', 'employe_id', 'notes', 'user_insert',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_operation' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }

    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'entree' => 'Entrée / recette',
            'depense' => 'Dépense',
            'paiement_client' => 'Paiement client',
            'paiement_fournisseur' => 'Paiement fournisseur',
            'credit' => 'Crédit / avoir',
            'salaire' => 'Salaire',
            default => $this->type,
        };
    }
}
