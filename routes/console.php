<?php

use App\Jobs\Feeds\SyncGames;
use App\Jobs\Feeds\SyncTeams;
use App\Jobs\Feeds\SyncGroups;
use App\Jobs\Feeds\SyncRankings;
use Illuminate\Support\Facades\Schedule;

// Game Syncing
Schedule::job(new SyncGames('live'))->everyFiveMinutes()->unlessBetween('1:00', '11:00');
Schedule::job(new SyncGames('today'))->hourly()->between('6:00', '23:00');
Schedule::job(new SyncGames('tomorrow'))->everyOddHour()->between('10:00', '23:00');
Schedule::job(new SyncGames('yesterday'))->twiceDaily(2, 8);
Schedule::job(new SyncGames('future'))->dailyAt(6);

// Conferences, Team & Rankings
Schedule::job(new SyncGroups())->everyTwoHours();
Schedule::job(new SyncTeams())->dailyAt(5);
Schedule::job(new SyncRankings())->everyOddHour()->mondays()->between('4:00', '4:00');