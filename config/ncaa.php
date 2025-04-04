<?php

$baseUrl = 'https://api.wmt.games/api/statistics/games';
$season = env('SEASON', date('Y'));

return [

    /**
     * Current Season
     */
    'year' => $season,

    /**
     * API Endpoints
     */
    'game' => $baseUrl,
    'games' => $baseUrl . '?season_academic_year=' . $season . '&sport_code=MBA'

];
