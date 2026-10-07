<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevisLigne extends Model
{
    use HasFactory;

    protected $table = 'devis_lignes';

    protected $fillable = [
        'devis_id',
        'type_entree',
        'catalogue_service_id',
        'catalogue_pack_id',
        'designation',
        'largeur',
        'hauteur',
        'calcul_details',
        'quantite',
        'unite',
        'prix_unitaire_ht',
        'remise_pct',
        'ordre',
    ];

    /** Calculs du prix unitaire : clé => libellé (même ordre que dans le formulaire). */
    public const CALCULS = [
        'm2' => 'Mètre carré',
        'ml' => 'Mètre linéaire',
        'fc' => 'Faux cadre',
        'vr' => 'Volet roulant',
        'mot' => 'Moteur',
        'ps' => 'Prix société',
    ];

    /** Calculs auxquels s'applique la marge de bénéfice du devis. */
    public const CALCULS_AVEC_MARGE = ['m2', 'ml', 'fc', 'ps'];

    public const MOTEUR_KG_PAR_M2 = 11;

    /** Hauteur ajoutée par défaut au volet roulant (m). */
    public const VOLET_AJOUT_HAUTEUR = 0.40;

    protected $casts = [
        'largeur' => 'decimal:3',
        'hauteur' => 'decimal:3',
        'calcul_details' => 'array',
        'quantite' => 'decimal:3',
        'prix_unitaire_ht' => 'decimal:2',
        'remise_pct' => 'decimal:2',
    ];

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }

    public function catalogueService()
    {
        return $this->belongsTo(CatalogueService::class, 'catalogue_service_id');
    }

    public function cataloguePack()
    {
        return $this->belongsTo(Pack::class, 'catalogue_pack_id');
    }

    /**
     * Nettoie le JSON envoyé par le formulaire ; null si aucun calcul n'est utilisé.
     */
    public static function normaliserCalcul($brut): ?array
    {
        $data = is_string($brut) ? json_decode($brut, true) : $brut;
        if (! is_array($data)) {
            return null;
        }

        $calcul = [];
        foreach (array_keys(self::CALCULS) as $cle) {
            $item = is_array($data[$cle] ?? null) ? $data[$cle] : [];
            $calcul[$cle] = [
                'on' => filter_var($item['on'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'prix' => max(0, round((float) ($item['prix'] ?? 0), 2)),
            ];
            if ($cle === 'ml') {
                $calcul[$cle]['coef'] = max(0, (float) ($item['coef'] ?? 4));
            }
            if ($cle === 'vr') {
                $calcul[$cle]['ajout'] = max(0, round((float) ($item['ajout'] ?? self::VOLET_AJOUT_HAUTEUR), 3));
            }
        }

        $utilise = collect($calcul)->contains(fn ($c) => $c['on'] || $c['prix'] > 0);

        return $utilise ? $calcul : null;
    }

    /**
     * Somme des calculs cochés, ou null si aucun n'est coché.
     * La marge (%) multiplie m², ml, faux cadre et prix société (saisi HT).
     */
    public static function calculerPrixUnitaire(?array $calcul, $largeur, $hauteur, $margePct = 0): ?float
    {
        if (! $calcul || ! collect($calcul)->contains(fn ($c) => ! empty($c['on']))) {
            return null;
        }

        $l = (float) $largeur;
        $h = (float) $hauteur;
        $coefMarge = 1 + (float) $margePct / 100;
        $montants = [
            'm2' => fn ($p) => $l * $h * $p,
            'ml' => fn ($p) => ($l * 2 * $p) + ($h * ($calcul['ml']['coef'] ?? 4) * $p),
            'fc' => fn ($p) => ($l * 2 * $p) + ($h * 2 * $p),
            'vr' => fn ($p) => $l * ($h + ($calcul['vr']['ajout'] ?? self::VOLET_AJOUT_HAUTEUR)) * $p,
            'mot' => fn ($p) => $p,
            'ps' => fn ($p) => $p,
        ];

        $total = 0;
        foreach ($montants as $cle => $formule) {
            if (! empty($calcul[$cle]['on'])) {
                $montant = $formule((float) ($calcul[$cle]['prix'] ?? 0));
                $total += in_array($cle, self::CALCULS_AVEC_MARGE, true) ? $montant * $coefMarge : $montant;
            }
        }

        return round($total, 2);
    }

    /**
     * Montant HT de la ligne (quantite * prix unitaire après remise)
     */
    public function getMontantHtAttribute()
    {
        $pu = $this->prix_unitaire_ht;
        if ($this->remise_pct > 0) {
            $pu = $pu * (1 - $this->remise_pct / 100);
        }
        return round($this->quantite * $pu, 2);
    }
}
