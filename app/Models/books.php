<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class books extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'publisher_id',
        'author_id',
        'category_id',
        'user_id',
        'cover_image',
        'isbn',
        'publication_date',
        'page_count',
        'created_at',
        'updated_at',
    ];
}
