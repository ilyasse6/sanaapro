<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    use HasFactory;

    /** Codes stockés en base → libellés affichés. */
    public const TYPE_LABELS = [
        'SARL' => 'SARL',
        'SARL_AU' => 'SARL à associé unique',
        'SA' => 'SA (société anonyme)',
        'SAS' => 'SAS (société par actions simplifiée)',
        'SNC' => 'SNC (société en nom collectif)',
        'SCS' => 'SCS (société en commandite simple)',
        'GIE' => "GIE (groupement d'intérêt économique)",
        'COOPERATIVE' => 'Coopérative',
        'ASSOCIATION' => 'Association',
        'AUTO_ENTREPRENEUR' => 'Auto-entrepreneur',
    ];

    public $timestamps = true;

    protected $table = 'entreprises';

    protected $fillable = [
        'description',
        'actif',
        'abbreviation',
        'type',
        'user_insert',
        'user_update',
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_entreprise');
    }

    public function profilePermissions()
    {
        return $this->hasMany(ProfilePermission::class, 'id_entreprise');
    }

    public function getTypeLabelAttribute(): string
    {
        $code = $this->attributes['type'] ?? '';

        return self::TYPE_LABELS[$code] ?? $code;
    }
}
