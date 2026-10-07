<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marche extends Model
{
    use BelongsToEntreprise;

    protected $fillable = [
        'id_entreprise',
        'reference', 'intitule', 'objet', 'client_id', 'statut',
        'date_ouverture', 'date_limite', 'date_limite_plis', 'date_attribution', 'montant_estime',
        'notes', 'actif', 'user_insert', 'user_update',
        'source_url', 'reference_consultation',
        'acheteur_public', 'type_annonce', 'procedure_text', 'categorie_principale', 'allotissement',
        'lieu_execution', 'domaines_activite',
        'adresse_retrait_dossiers', 'adresse_depot_offres', 'lieu_ouverture_plis',
        'prix_acquisition_plans',
        'contact_nom', 'contact_email', 'contact_telephone', 'contact_fax',
        'decision_offre', 'date_depart',
    ];

    protected $casts = [
        'date_ouverture' => 'date',
        'date_limite' => 'date',
        'date_limite_plis' => 'datetime',
        'date_depart' => 'date',
        'date_attribution' => 'date',
        'montant_estime' => 'decimal:2',
        'prix_acquisition_plans' => 'decimal:2',
        'actif' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(MarcheLot::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MarcheDocument::class);
    }

    public function cautions(): HasMany
    {
        return $this->hasMany(Caution::class);
    }

    public static function genererReference(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->orderByDesc('id')->first();
        $num = $last ? ((int) substr($last->reference, -4)) + 1 : 1;

        return 'MCH-'.$year.'-'.str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'brouillon' => 'Brouillon',
            'en_cours' => 'En cours',
            'attribue' => 'Attribué',
            'execution' => 'Exécution',
            'cloture' => 'Clôturé',
            'annule' => 'Annulé',
            default => $this->statut,
        };
    }

    /** Classes Bootstrap pour un badge « pilule » (liste / fiche marché). */
    public function getStatutBadgeClassAttribute(): string
    {
        return match ($this->statut) {
            'brouillon' => 'badge rounded-pill bg-secondary fw-semibold text-white px-3 py-2',
            'en_cours' => 'badge rounded-pill fw-bold text-white px-3 py-2 marche-statut-badge marche-statut--en-cours',
            'attribue' => 'badge rounded-pill bg-success fw-semibold text-white px-3 py-2',
            'execution' => 'badge rounded-pill bg-info fw-semibold text-dark px-3 py-2',
            'cloture' => 'badge rounded-pill bg-dark fw-semibold text-white px-3 py-2',
            'annule' => 'badge rounded-pill bg-danger fw-semibold text-white px-3 py-2',
            default => 'badge rounded-pill bg-secondary fw-semibold text-white px-3 py-2',
        };
    }

    public function getDecisionOffreLabelAttribute(): string
    {
        $d = $this->decision_offre ?? 'en_attente';

        return match ($d) {
            'en_attente' => 'En attente',
            'accepte' => 'Offre acceptée',
            'refuse' => 'Offre refusée',
            default => $d,
        };
    }

    public function estOffreAcceptee(): bool
    {
        return $this->decision_offre === 'accepte';
    }

    /**
     * Quatre tranches égales du montant estimé, chacune sur 3 mois à partir de date_depart.
     *
     * @return array<int, array{index: int, montant: float, debut: Carbon, fin: Carbon}>
     */
    public function tranchesMontantEstimes(): array
    {
        if (! $this->estOffreAcceptee() || ! $this->date_depart || $this->montant_estime === null) {
            return [];
        }

        $total = (float) $this->montant_estime;
        $base = round($total / 4, 2);
        $montants = array_fill(0, 4, $base);
        $ecart = round($total - array_sum($montants), 2);
        $montants[3] = round($montants[3] + $ecart, 2);

        $debut = $this->date_depart->copy()->startOfDay();
        $out = [];
        for ($i = 0; $i < 4; $i++) {
            $d1 = $debut->copy()->addMonths($i * 3);
            $d2 = $debut->copy()->addMonths(($i + 1) * 3)->subDay();
            $out[] = [
                'index' => $i + 1,
                'montant' => $montants[$i],
                'debut' => $d1,
                'fin' => $d2,
            ];
        }

        return $out;
    }
}
