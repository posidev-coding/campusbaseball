<?php

$baseUrl = 'http://sports.core.api.espn.com/v2/sports/baseball/leagues/college-baseball';
$season = date('Y');

return [

    /**
     * Current Season
     */
    // 'season' => env('SEASON', 2024),

    /**
     * API Endpoints
     */
    'about' => $baseUrl,
    'seasons' => $baseUrl.'/seasons',
    'season' => $baseUrl.'/seasons/'.$season,
    'teams' => $baseUrl.'/teams',
    'groups' => $baseUrl.'/seasons/'.$season.'/types/2/groups',
    'rankings' => $baseUrl.'/rankings',
];
