<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    protected $guarded = [];

    protected $with = ['team'];

    public function team()
    {
        return $this->hasOne(Team::class, 'id', 'team_id');
    }
}
