<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $guarded = [];

    protected $with = ['record', 'conference'];

    protected $casts = [
        'logos' => 'array',
    ];

    public function record()
    {
        return $this->hasOne(Record::class)->where('scope', 'overall');
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class)->where('is_conference', 1);
    }

    public function liveHome()
    {
        return $this->hasOne(Game::class, 'home_id', 'id')->where('status_id', 2);
    }

    public function liveAway()
    {
        return $this->hasOne(Game::class, 'away_id', 'id')->where('status_id', 2);
    }

    public function getLiveAttribute()

    {

        return $this->liveHome ?? $this->liveAway;

    }
}
