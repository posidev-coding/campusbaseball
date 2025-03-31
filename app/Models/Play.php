<?php

namespace App\Models;

use App\Models\Team;
use Illuminate\Database\Eloquent\Model;

class Play extends Model
{
    protected $guarded = [];

    protected $casts = [
        'runners' => 'array'
    ];

    protected $with = ['team'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
