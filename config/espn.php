<?php

$baseUrl = 'http://sports.core.api.espn.com/v2/sports/baseball/leagues/college-baseball';

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

];
