<?php

namespace App\Models;

use App\Jobs\Feeds\SyncGame;
use App\Jobs\Feeds\SyncTeam;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $guarded = [];

    protected $casts = [
        'finalized' => 'boolean',
        'game_date' => 'date',
        'game_time' => 'datetime',
        'status' => 'array',
        'resources' => 'array',
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

    public function plays()
    {
        return $this->hasMany(Play::class);
    }

    public function getFinalAttribute()
    {
        return $this->status_id == 3;
    }

    public function getCancelledAttribute()
    {
        return $this->status_id == 5;
    }

    public function getLiveAttribute()
    {
        return $this->status_id == 2;
    }

    protected static function booted(): void
    {

        static::saved(function (Game $game) {

            if (($game->final || $game->cancelled) && !$game->finalized) {
                SyncGame::dispatch($game->id, 'final');
            }

        });

        static::updated(function (Game $game) {

            if ($game->isDirty('status_id') && $game->status_id == 3) {

                // Game just went final

                // Sync teams for updated records
                SyncTeam::dispatch($game->away_id);
                SyncTeam::dispatch($game->away_id)->delay(now()->addMinutes(15));
                SyncTeam::dispatch($game->home_id);
                SyncTeam::dispatch($game->home_id)->delay(now()->addMinutes(15));

                // Run a full sync for stats
                SyncGame::dispatch($game->id, 'full');
                SyncGame::dispatch($game->id, 'full')->delay(now()->addMinutes(15));

            }

        });
    }
}
