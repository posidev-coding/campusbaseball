<?php

namespace App\Models;

use App\Models\Team;
use App\Models\Conference;
use Illuminate\Database\Eloquent\Model;

class Standing extends Model
{
    protected $guarded = [];

    protected $with = ['team'];

    protected $casts = [
        'stats' => 'array',
    ];

    public function team()
    {
        return $this->hasOne(Team::class, 'id', 'team_id');
    }

    public function conference()
    {
        return $this->hasOne(Conference::class, 'id', 'team_id');
    }
}
