<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use App\Support\CurrentTenant;
use App\Support\SocieteBranding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BonLivraison extends Model
{
    use BelongsToEntreprise;

    protected $table = 'bons_livraison';

    protected $fillable = [
        'id_entreprise',
        'client_id',
        'numero',
        'date_bl',
        'lieu',
        'numero_commande',
        'reference_client',
        'contact_client',
        'emis_par',
        'informations_additionnelles',
        'recu_le',
        'livre_le',
        'statut',
        'branding_snapshot',
        'actif',
        'user_insert',
        'user_update',
    ];

    protected $casts = [
        'date_bl' => 'date',
        'recu_le' => 'date',
        'livre_le' => 'date',
        'branding_snapshot' => 'array',
        'actif' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(BonLivraisonLigne::class)->orderBy('ordre')->orderBy('id');
    }

    public static function genererNumero(?int $idEntreprise = null): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "BL-{$year}-{$month}-";

        $eid = $idEntreprise ?? CurrentTenant::id() ?? Auth::user()?->id_entreprise;
        $q = static::query()->where('numero', 'like', $prefix.'%');
        if ($eid !== null) {
            $q->where('id_entreprise', $eid);
        }
        $last = $q->orderByDesc('id')->first();

        $seq = 1;
        if ($last && preg_match('/^BL-\d{4}-\d{2}-(\d+)$/', $last->numero, $m)) {
            $seq = (int) $m[1] + 1;
        }

        return $prefix.$seq;
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'brouillon' => 'Brouillon',
            'valide' => 'Validé',
            'annule' => 'Annulé',
            default => $this->statut,
        };
    }

    /**
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

            return [
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
