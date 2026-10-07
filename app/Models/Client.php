<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use BelongsToEntreprise;
    use HasFactory;

    protected $fillable = [
        'id_entreprise',
        'nom',
        'prenom',
        'date_naissance',
        'cin',
        'email',
        'telephone',
        'adresse',
        'ville',
        'code_postal',
        'ice',
        'rc',
        'secteur_activite',
        'notes',
        'besoin_diagnostic',
        'actif',
        'user_insert',
        'user_update',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'actif' => 'boolean',
        'besoin_diagnostic' => 'boolean',
    ];

    /**
     * Client actif portant cette raison sociale, ou nouvelle fiche minimale.
     */
    public static function trouverOuCreerParNom(string $nom, ?int $userId = null): self
    {
        $nom = trim(preg_replace('/\s+/u', ' ', $nom));

        return static::query()->where('actif', 1)->where('nom', $nom)->orderBy('id')->first()
            ?? static::create([
                'nom' => $nom,
                'prenom' => '',
                'date_naissance' => now()->toDateString(),
                'besoin_diagnostic' => 0,
                'actif' => 1,
                'user_insert' => $userId,
            ]);
    }

    /**
     * Devis du client
     */
    public function devis()
    {
        return $this->hasMany(Devis::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function marches()
    {
        return $this->hasMany(Marche::class);
    }

    /**
     * Relation avec les diagnostics
     */
    public function diagnostics()
    {
        return $this->hasMany(Diagnostic::class);
    }

    /**
     * Dernier diagnostic actif
     */
    public function dernierDiagnostic()
    {
        return $this->hasOne(Diagnostic::class)->where('actif', 1)->latest();
    }

    /**
     * Vérifier si le client est diagnostiqué
     */
    public function estDiagnostique()
    {
        return $this->diagnostics()->where('actif', 1)->exists();
    }

    /**
     * Vérifier si le client n'est pas diagnostiqué depuis 2 mois
     */
    public function nonDiagnostiqueDepuis2Mois()
    {
        $dernierDiagnostic = $this->diagnostics()
            ->where('actif', 1)
            ->latest()
            ->first();

        if (! $dernierDiagnostic) {
            // Si jamais diagnostiqué, vérifier la date de création du client
            return $this->created_at->lt(now()->subMonths(2));
        }

        return $dernierDiagnostic->created_at->lt(now()->subMonths(2));
    }
}
