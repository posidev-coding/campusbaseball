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

    public function articles()
    {
        return Article::whereJsonContains('teams', $this->id)->orderBy('published', 'desc')->get();
    }

    public function record()
    {
        return $this->hasOne(Record::class)->where('scope', 'overall');
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
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

    public function getLogoAttribute()
    {
        return $this->logos[0]['href'] ?? null;
    }

    public function getDarkLogoAttribute()
    {
        return $this->logos[1]['href'] ?? null;
    }
}
