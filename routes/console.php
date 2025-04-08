<?php

use App\Jobs\Feeds\SyncGames;
use App\Jobs\Feeds\SyncTeams;
use App\Jobs\Feeds\SyncGroups;
use App\Jobs\Feeds\SyncArticles;
use App\Jobs\Feeds\SyncRankings;
use Illuminate\Support\Facades\Schedule;

// Game Syncing
Schedule::job(new SyncGames('live', 'Live Games (5m)'))->everyMinute()->between('11:00', '23:59');
Schedule::job(new SyncGames('today', 'Games Today (hourly)'))->hourly();
Schedule::job(new SyncGames('tomorrow', 'Games Tomorrow (odd hours)'))->everyOddHour();
Schedule::job(new SyncGames('yesterday', 'Games Yesterday (2x day)'))->twiceDaily();
Schedule::job(new SyncGames('future', 'Future Games (6am daily)'))->dailyAt(6);

// Conferences, Teams, Articles & Rankings
Schedule::job(new SyncGroups())->everyTwoHours();
Schedule::job(new SyncTeams())->dailyAt(5);
Schedule::job(new SyncArticles())->everyTwoHours(15);
Schedule::job(new SyncRankings())->everyOddHour()->mondays();