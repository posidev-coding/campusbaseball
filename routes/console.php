<?php

use App\Jobs\Feeds\SyncGames;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SyncGames('live'))->everyTenMinutes();
