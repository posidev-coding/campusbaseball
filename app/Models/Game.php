<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => 'array',
        'venue' => 'array',
        'broadcasts' => 'array',
        'away_box' => 'array',
        'away_stats' => 'array',
        'away_records' => 'array',
        'away_roster' => 'array',
        'home_box' => 'array',
        'home_stats' => 'array',
        'home_records' => 'array',
        'home_roster' => 'array',
    ];

    protected $with = ['home', 'away'];

    // game statuses
    /*
    1 = Scheduled
    2 = In Progress
    3 = Final
    7 = Delayed
    */

    public function home()
    {
        return $this->hasOne(Team::class, 'id', 'home_id');
    }

    public function away()
    {
        return $this->hasOne(Team::class, 'id', 'away_id');
    }
}
