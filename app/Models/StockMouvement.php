<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMouvement extends Model
{
    use BelongsToEntreprise;

    protected $fillable = [
        'id_entreprise',
        'article_id', 'type', 'quantite', 'motif', 'reference_piece', 'user_id',
    ];

    protected $casts = ['quantite' => 'decimal:3'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
