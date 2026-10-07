<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'modules';

    protected $fillable = [
        'id',
        'nom',
        'created_at',
        'updated_at',
    ];
    // Relations
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'id_module');
    }
}
