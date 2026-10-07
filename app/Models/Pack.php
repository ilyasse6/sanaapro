<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    use BelongsToEntreprise;
    use HasFactory;

    protected $table = 'catalogue_packs';

    protected $fillable = [
        'id_entreprise',
        'nom',
        'description',
        'image',
        'prix',
        'reduction',
        'actif',
        'user_insert',
        'user_update',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'reduction' => 'decimal:2',
        'actif' => 'boolean',
    ];

    /**
     * Relation avec les services
     */
    public function services()
    {
        return $this->belongsToMany(CatalogueService::class, 'catalogue_pack_service', 'pack_id', 'catalogue_service_id');
    }

    /**
     * Calculer le prix après réduction
     */
    public function getPrixApresReductionAttribute()
    {
        if ($this->reduction > 0) {
            return $this->prix * (1 - $this->reduction / 100);
        }

        return $this->prix;
    }
}
