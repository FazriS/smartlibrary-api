<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'description',
        'publish_year'
    ];

    // Many-to-Many [cite: 190]
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category');
    }

    public function readingLists()
    {
        return $this->hasMany(ReadingList::class);
    }
}