<?php

use App\Jobs\Feeds\SyncGames;
use App\Jobs\Feeds\SyncTeams;
use App\Jobs\Feeds\SyncGroups;
use App\Jobs\Feeds\SyncArticles;
use App\Jobs\Feeds\SyncRankings;
use Illuminate\Support\Facades\Schedule;

// Game Syncing
Schedule::job(new SyncGames('live', 'Live Games (5m)'))->everyFiveMinutes()->unlessBetween('02:00', '11:00');
Schedule::job(new SyncGames('today', 'Games Today (hourly)'))->hourly(5)->between('11:00', '23:59');
Schedule::job(new SyncGames('tomorrow', 'Games Tomorrow (odd hours)'))->everyOddHour(15)->between('11:00', '23:59');
Schedule::job(new SyncGames('yesterday', 'Games Yesterday (2x day)'))->twiceDaily(3, 15);
Schedule::job(new SyncGames('future', 'Future Games (6:45am daily)'))->dailyAt('6:45');

// Conferences, Teams, Articles & Rankings
Schedule::job(new SyncGroups())->everyTwoHours(10)->between('6:00', '23:59');
Schedule::job(new SyncTeams())->dailyAt(4);
Schedule::job(new SyncArticles())->everyTwoHours(30)->between('6:00', '23:59');
Schedule::job(new SyncRankings())->everyOddHour(45)->mondays()->between('4:00', '23:59');