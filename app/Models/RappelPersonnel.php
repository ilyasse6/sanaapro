<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RappelPersonnel extends Model
{
    use BelongsToEntreprise;

    protected $table = 'rappels_personnels';

    protected $fillable = [
        'id_entreprise',
        'user_id',
        'titre',
        'notes',
        'date_rappel',
        'heure_rappel',
    ];

    protected function casts(): array
    {
        return [
            'date_rappel' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
