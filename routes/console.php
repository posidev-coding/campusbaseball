<?php

use App\Jobs\Feeds\SyncGames;
use App\Jobs\Feeds\SyncTeams;
use App\Jobs\Feeds\SyncGroups;
use App\Jobs\Feeds\SyncArticles;
use App\Jobs\Feeds\SyncRankings;
use Illuminate\Support\Facades\Schedule;

// Game Syncing
Schedule::job(new SyncGames('live'))->everyFiveMinutes();
// Schedule::job(new SyncGames('today'))->hourly();
// Schedule::job(new SyncGames('tomorrow'))->everyOddHour();
// Schedule::job(new SyncGames('yesterday'))->twiceDaily();
// Schedule::job(new SyncGames('future'))->dailyAt(6);

// Conferences, Teams, Articles & Rankings
// Schedule::job(new SyncGroups())->everyTwoHours();
// Schedule::job(new SyncTeams())->dailyAt(5);
// Schedule::job(new SyncArticles())->everyTwoHours(15);
// Schedule::job(new SyncRankings())->everyOddHour()->mondays();