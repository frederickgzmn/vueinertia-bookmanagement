<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer_reviews extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'comment',
        'rating_stars',
        'user_id',
        'book_id',
    ];

}
