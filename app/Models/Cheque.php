<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cheque extends Model
{
    use BelongsToEntreprise;

    public const STATUTS = [
        'en_portefeuille' => 'En portefeuille',
        'remis' => 'Remis en banque',
        'encaisse' => 'Encaissé',
        'impaye' => 'Impayé',
        'restitue' => 'Restitué',
    ];

    /** Chèques encore attendus (non encaissés, non rejetés). */
    public const STATUTS_EN_COURS = ['en_portefeuille', 'remis'];

    protected $fillable = [
        'id_entreprise', 'client_id', 'facture_id', 'compta_mouvement_id',
        'numero', 'banque', 'tireur', 'montant', 'date_reception', 'date_echeance',
        'statut', 'date_remise', 'date_encaissement', 'date_impaye', 'motif_impaye', 'notes',
        'user_insert', 'user_update',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_reception' => 'date',
        'date_echeance' => 'date',
        'date_remise' => 'date',
        'date_encaissement' => 'date',
        'date_impaye' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }

    public function mouvement(): BelongsTo
    {
        return $this->belongsTo(ComptaMouvement::class, 'compta_mouvement_id');
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function getStatutBadgeAttribute(): string
    {
        return match ($this->statut) {
            'en_portefeuille' => 'text-bg-primary',
            'remis' => 'text-bg-info',
            'encaisse' => 'text-bg-success',
            'impaye' => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    }

    /** Échéance atteinte alors que le chèque n’a pas encore été déposé. */
    public function getADeposerAttribute(): bool
    {
        return $this->statut === 'en_portefeuille'
            && $this->date_echeance !== null
            && $this->date_echeance->lte(today());
    }
}
