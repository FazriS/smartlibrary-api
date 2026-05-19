<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name'
    ];

    // Category belongsToMany Book [cite: 196]
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_category');
    }
}