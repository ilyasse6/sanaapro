<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profiles';

    protected $fillable = [
        'id_user',
        'id_profile',
        'actif',
        'created_at',
        'updated_at',
    ];

    /**
     * Relation Many-to-One avec User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relation Many-to-One avec Profile
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'id_profile');
    }
}
