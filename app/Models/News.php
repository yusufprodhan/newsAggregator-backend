<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'source_id',
        'source_name',
        'author',
        'category',
        'content',
        'description',
        'url',
        'urlToImage',
        'publishedAt',
    ];
}
