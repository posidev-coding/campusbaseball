<?php

use App\Livewire\Feeds\Feeds;
use App\Livewire\Pages\Conferences\ShowConference as Conference;
use App\Livewire\Pages\Conferences\ViewConferences as Conferences;
use App\Livewire\Pages\Games\ShowGame as Game;
use App\Livewire\Pages\Games\ViewGames as Scores;
use App\Livewire\Pages\Rankings\ViewRankings as Rankings;
use App\Livewire\Pages\Teams\ShowTeam as Team;
use App\Livewire\Pages\Teams\ViewTeams as Teams;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'home')->name('home');

Route::get('/scores', Scores::class)->name('scores');
Route::get('/scores/{game}', Game::class)->name('game');

Route::get('/teams', Teams::class)->name('teams');
Route::get('/teams/{team}', Team::class)->name('team');

Route::get('/conferences', Conferences::class)->name('conferences');
Route::get('/conferences/{conference}', Conference::class)->name('conference');

Route::get('/rankings', Rankings::class)->name('rankings');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Feeds
    Route::get('/feeds', Feeds::class)->name('feeds');
});

require __DIR__.'/auth.php';
