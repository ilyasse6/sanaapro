<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use BelongsToEntreprise;
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'id_entreprise',
        'client_id',
        'date_rdv',
        'heure_rdv',
        'statut',
        'notes',
        'prix_total',
        'actif',
        'user_insert',
        'user_update',
    ];

    protected $casts = [
        'date_rdv' => 'date',
        'prix_total' => 'decimal:2',
        'actif' => 'boolean',
    ];

    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec les services
     */
    public function services()
    {
        return $this->belongsToMany(CatalogueService::class, 'rendez_vous_services', 'rendez_vous_id', 'catalogue_service_id')
            ->withPivot('prix')
            ->withTimestamps();
    }

    /**
     * Relation avec les packs
     */
    public function packs()
    {
        return $this->belongsToMany(Pack::class, 'rendez_vous_packs', 'rendez_vous_id', 'pack_id')
            ->withPivot('prix')
            ->withTimestamps();
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatutLibelleAttribute()
    {
        $statuts = [
            'en_attente' => 'En attente',
            'confirme' => 'Confirmé',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé',
        ];

        return $statuts[$this->statut] ?? $this->statut;
    }

    /**
     * Obtenir la couleur du badge selon le statut
     */
    public function getStatutCouleurAttribute()
    {
        $couleurs = [
            'en_attente' => 'warning',
            'confirme' => 'info',
            'en_cours' => 'primary',
            'termine' => 'success',
            'annule' => 'danger',
        ];

        return $couleurs[$this->statut] ?? 'secondary';
    }
}
