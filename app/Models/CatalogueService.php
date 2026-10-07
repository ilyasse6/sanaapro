<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueService extends Model
{
    use BelongsToEntreprise;
    use HasFactory;

    protected $table = 'catalogue_services';

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
     * Relation avec les packs
     */
    public function packs()
    {
        return $this->belongsToMany(Pack::class, 'catalogue_pack_service', 'catalogue_service_id', 'pack_id');
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
