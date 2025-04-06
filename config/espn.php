<?php

$baseUrl = 'http://sports.core.api.espn.com/v2/sports/baseball/leagues/college-baseball';
$season = env('SEASON', date('Y'));

return [

    /**
     * Current Season
     */
    'year' => $season,

    /**
     * API Endpoints
     */
    'base' => $baseUrl,
    'calendar' => $baseUrl.'/calendar',
    'seasons' => $baseUrl.'/seasons',
    'season' => $baseUrl.'/seasons/'.$season,
    'teams' => $baseUrl.'/teams',
    'groups' => $baseUrl.'/seasons/'.$season.'/types/2/groups',
    'rankings' => $baseUrl.'/rankings',
    'games' => $baseUrl.'/events',

    // Site API resources
    'news' => 'http://site.api.espn.com/apis/site/v2/sports/baseball/college-baseball/news?limit=100',

];
