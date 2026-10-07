<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostic extends Model
{
    use BelongsToEntreprise;
    use HasFactory;

    protected $fillable = [
        'id_entreprise',
        'client_id',
        'deja_fait_laser',
        'autre_centre',
        'nb_seances_anterieures',
        'type_peau',
        'antecedents_medicaux',
        'medications_cours',
        'allergies',
        'grossesse_allaitement',
        'zones_traiter',
        'zone_cible',
        'contre_indications',
        'consentement_eclaire',
        'date_prise_rdv',
        'heure_rdv',
        'type_traitement',
        'nb_seances_prevues',
        'notes_diagnostic',
        'actif',
        'user_insert',
        'user_update',
    ];

    protected $casts = [
        'deja_fait_laser' => 'boolean',
        'grossesse_allaitement' => 'boolean',
        'consentement_eclaire' => 'boolean',
        'actif' => 'boolean',
        'date_prise_rdv' => 'date',
        'nb_seances_anterieures' => 'integer',
        'nb_seances_prevues' => 'integer',
    ];

    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
