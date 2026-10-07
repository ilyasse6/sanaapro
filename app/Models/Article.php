<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use BelongsToEntreprise;

    protected $fillable = [
        'id_entreprise',
        'reference', 'nom', 'description', 'unite', 'prix_achat_ht', 'prix_vente_ht',
        'stock_quantity', 'seuil_alerte', 'fournisseur_id', 'catalogue_service_id',
        'actif', 'user_insert', 'user_update',
    ];

    protected $casts = [
        'prix_achat_ht' => 'decimal:2',
        'prix_vente_ht' => 'decimal:2',
        'stock_quantity' => 'decimal:3',
        'seuil_alerte' => 'decimal:3',
        'actif' => 'boolean',
    ];

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function catalogueService(): BelongsTo
    {
        return $this->belongsTo(CatalogueService::class, 'catalogue_service_id');
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(StockMouvement::class);
    }

    public function getStockBasAttribute(): bool
    {
        return $this->stock_quantity <= $this->seuil_alerte;
    }
}
