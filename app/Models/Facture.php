<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use App\Support\CurrentTenant;
use App\Support\MontantEnLettres;
use App\Support\SocieteBranding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Facture extends Model
{
    use BelongsToEntreprise;

    protected $fillable = [
        'id_entreprise',
        'client_id', 'devis_id', 'numero_facture', 'date_emission', 'date_echeance', 'statut',
        'remise_globale_pct', 'taux_tva', 'total_ttc', 'conditions_paiement', 'notes', 'objet', 'branding_snapshot',
        'actif', 'user_insert', 'user_update',
    ];

    public const DESIGNATION_DEFAUT = 'Fourniture et pose d’aluminium + vitrage.';

    protected $casts = [
        'date_emission' => 'date',
        'date_echeance' => 'date',
        'remise_globale_pct' => 'decimal:2',
        'taux_tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'actif' => 'boolean',
        'branding_snapshot' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function devis(): BelongsTo
    {
        return $this->belongsTo(Devis::class, 'devis_id');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(FactureLigne::class)->orderBy('ordre')->orderBy('id');
    }

    public function comptaMouvements(): HasMany
    {
        return $this->hasMany(ComptaMouvement::class);
    }

    public function getMontantLignesHtAttribute(): float
    {
        return (float) $this->lignes->sum('montant_ht');
    }

    /** Facture « une ligne » : le TTC saisi fait foi, HT et TVA en sont déduits. */
    public function aTotalTtcSaisi(): bool
    {
        return $this->total_ttc !== null;
    }

    public static function htDepuisTtc(float $ttc, float $tauxTva): float
    {
        return round($ttc / (1 + $tauxTva / 100), 2);
    }

    public function getMontantHtAttribute(): float
    {
        if ($this->aTotalTtcSaisi()) {
            return self::htDepuisTtc((float) $this->total_ttc, (float) $this->taux_tva);
        }

        $ht = $this->montant_lignes_ht;
        if ($this->remise_globale_pct > 0) {
            $ht = $ht * (1 - (float) $this->remise_globale_pct / 100);
        }

        return round($ht, 2);
    }

    public function getMontantTvaAttribute(): float
    {
        if ($this->aTotalTtcSaisi()) {
            return round((float) $this->total_ttc - $this->montant_ht, 2);
        }

        return round($this->montant_ht * ((float) $this->taux_tva / 100), 2);
    }

    public function getMontantTtcAttribute(): float
    {
        if ($this->aTotalTtcSaisi()) {
            return round((float) $this->total_ttc, 2);
        }

        return round($this->montant_ht + $this->montant_tva, 2);
    }

    public function getMontantEnLettresAttribute(): string
    {
        return MontantEnLettres::dirhams($this->montant_ttc);
    }

    /** Désignation unique affichée sur la facture (anciennes factures multi-lignes : désignations jointes). */
    public function getDesignationUniqueAttribute(): string
    {
        return $this->lignes->pluck('designation')->filter()->implode("\n");
    }

    public static function genererNumero(?int $idEntreprise = null): string
    {
        $year = date('Y');
        $eid = $idEntreprise ?? CurrentTenant::id() ?? Auth::user()?->id_entreprise;
        $q = static::query()->whereYear('created_at', $year);
        if ($eid !== null) {
            $q->where('id_entreprise', $eid);
        }
        $last = $q->orderByDesc('id')->first();
        $num = $last ? (int) substr($last->numero_facture, -4) + 1 : 1;

        return 'FAC-'.$year.'-'.str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * En-tête figé sur la facture (instantané) ; sinon devis d’origine ; sinon paramètres actuels (anciennes données).
     *
     * @return array{logo_path: ?string, logo_url: ?string, nom: string, adresse: string, telephone: string, email: string, mention_tva: string, accent_color: string, text_color: string}
     */
    public function getBrandingAfficheAttribute(): array
    {
        $snap = $this->branding_snapshot;
        if (is_array($snap) && $snap !== []) {
            $path = isset($snap['logo_path']) && is_string($snap['logo_path']) ? $snap['logo_path'] : null;
            $logoUrl = null;
            if ($path !== null && $path !== '' && Storage::disk('public')->exists($path)) {
                $logoUrl = asset('storage/'.$path);
            }

            $entete = isset($snap['entete_titre'])
                ? array_intersect_key($snap, array_flip(['entete_titre', 'entete_activite', 'pied_page']))
                : SocieteBranding::enteteDocument($this->id_entreprise);

            return $entete + [
                'logo_path' => $path,
                'logo_url' => $logoUrl,
                'nom' => (string) ($snap['nom'] ?? ''),
                'adresse' => (string) ($snap['adresse'] ?? ''),
                'telephone' => (string) ($snap['telephone'] ?? ''),
                'email' => (string) ($snap['email'] ?? ''),
                'mention_tva' => (string) ($snap['mention_tva'] ?? SocieteBranding::DEFAULT_MENTION_TVA),
                'accent_color' => (string) ($snap['accent_color'] ?? SocieteBranding::DEFAULT_ACCENT_COLOR),
                'text_color' => (string) ($snap['text_color'] ?? SocieteBranding::DEFAULT_TEXT_COLOR),
            ];
        }

        $devis = $this->devis;
        if ($devis) {
            return SocieteBranding::enteteDocument($this->id_entreprise) + $devis->branding_affiche;
        }

        return SocieteBranding::all($this->id_entreprise);
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'brouillon' => 'Brouillon',
            'emise' => 'Émise',
            'envoyee' => 'Envoyée',
            'payee_partielle' => 'Payée partiellement',
            'payee' => 'Payée',
            'annulee' => 'Annulée',
            'avoir' => 'Avoir',
            default => $this->statut,
        };
    }
}
