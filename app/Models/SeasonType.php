<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeasonType extends Model
{
    protected $guarded = [];

    protected $casts = [
        'refs' => 'array',
    ];
}
