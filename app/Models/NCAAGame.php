<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NCAAGame extends Model
{
    protected $table = 'ncaa_games';
    
    protected $guarded = [];

    protected $casts = [
        'boxscore_available' =>'boolean',
        'pbp_available' =>'boolean',
        'summary_available' =>'boolean',
        'game_date' => 'date',
        'game_time' => 'datetime',
        'linescores' => 'array',
        'stats' => 'array',
    ];

}
