<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'bio'
    ];

    // Profile belongsTo User [cite: 185]
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}