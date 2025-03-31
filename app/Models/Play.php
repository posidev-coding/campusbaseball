<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Play extends Model
{
    protected $guarded = [];

    protected $casts = [
        'runners' => 'array',
    ];

    protected $with = ['team'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
