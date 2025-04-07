<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected $guarded = [];

    protected $casts = [
        'published' => 'datetime',
        'teams' => 'array',
        'story_images' => 'array',
        'story_videos' => 'array'
    ];

}