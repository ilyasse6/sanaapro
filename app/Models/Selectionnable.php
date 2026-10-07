<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Selectionnable extends Model
{
    use HasFactory;

    protected $table = 'selectionnables';

    protected $fillable = [
        'nom',
        'type',
        'form',
        'form_evenement',
        'form_confirmation',
    ];
} 