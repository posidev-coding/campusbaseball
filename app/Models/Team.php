<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $guarded = [];

    protected $casts = [
        'logos' => 'array'
    ];
}
