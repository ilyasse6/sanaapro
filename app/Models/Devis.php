<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use App\Support\CurrentTenant;
use App\Support\SocieteBranding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Devis extends Model
{
    use BelongsToEntreprise;
    use HasFactory;

    protected $table = 'devis';

    protected $fillable = [
        'id_entreprise',
        'client_id',
        'numero_devis',
        'date_emission',
        'date_validite',
        'statut',
        'remise_globale_pct',
        'marge_pct',
        'taux_tva',
        'conditions_paiement',
        'notes',
        'objet',
        'branding_snapshot',
        'actif',
        'user_insert',
        'user_update',
    ];

    protected $casts = [
        'date_emission' => 'date',
        'date_validite' => 'date',
        'remise_globale_pct' => 'decimal:2',
        'marge_pct' => 'decimal:2',
        'taux_tva' => 'decimal:2',
        'actif' => 'boolean',
        'branding_snapshot' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function lignes()
    {
        return $this->hasMany(DevisLigne::class)->orderBy('ordre')->orderBy('id');
    }

    public function facture()
    {
        return $this->hasOne(Facture::class, 'devis_id');
    }

    /**
     * Montant total HT des lignes (avant remise globale)
     */
    public function getMontantLignesHtAttribute()
    {
        return $this->lignes->sum('montant_ht');
    }

    /**
     * Montant HT après remise globale
     */
    public function getMontantHtAttribute()
    {
        $ht = $this->montant_lignes_ht;
        if ($this->remise_globale_pct > 0) {
            $ht = $ht * (1 - $this->remise_globale_pct / 100);
        }

        return round($ht, 2);
    }

    /**
     * Montant TVA
     */
    public function getMontantTvaAttribute()
    {
        return round($this->montant_ht * ($this->taux_tva / 100), 2);
    }

    /**
     * Montant TTC
     */
    public function getMontantTtcAttribute()
    {
        return round($this->montant_ht + $this->montant_tva, 2);
    }

    /**
     * Format type auto-entrepreneur : D-YYYY-MM-XXX (séquence mensuelle).
     */
    public static function genererNumero(?int $idEntreprise = null): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "D-{$year}-{$month}-";

        $eid = $idEntreprise ?? CurrentTenant::id() ?? Auth::user()?->id_entreprise;
        $q = static::query()->where('numero_devis', 'like', $prefix.'%');
        if ($eid !== null) {
            $q->where('id_entreprise', $eid);
        }

        $last = $q->orderByDesc('id')->first();

        $seq = 1;
        if ($last && preg_match('/^D-\d{4}-\d{2}-(\d+)$/', $last->numero_devis, $m)) {
            $seq = (int) $m[1] + 1;
        }

        return $prefix.$seq;
    }

    public function getStatutLabelAttribute()
    {
        return match ($this->statut) {
            'brouillon' => 'Brouillon',
            'envoye' => 'Envoyé',
            'accepte' => 'Accepté',
            'refuse' => 'Refusé',
            default => $this->statut,
        };
    }

    /**
     * En-tête affiché sur le devis : instantané enregistré, sinon paramètres actuels (anciens devis sans colonne).
     *
     * @return array{logo_path: ?string, logo_url: ?string, nom: string, adresse: string, telephone: string, email: string, mention_tva: string, accent_color: string, text_color: string}
     */
    public function getBrandingAfficheAttribute(): array
    {
        $snap = $this->branding_snapshot;
        if (is_array($snap) && ($snap !== [])) {
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

        return SocieteBranding::all($this->id_entreprise);
    }
}
