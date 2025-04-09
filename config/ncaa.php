<?php

$baseUrl = 'https://sdataprod.ncaa.com';
$season = env('SEASON', date('Y'));

return [

    /**
     * Current Season
     */
    'year' => intval($season) - 1,

    /**
     * API Endpoints
     */
    'game' => $baseUrl . '?meta=GetGamecenterGameById_web&extensions=' . urlencode('{"persistedQuery":{"version":1,"sha256Hash":"93a02c7193c89d85bcdda8c1784925d9b64657f73ef584382e2297af555acd4b"}}'),
    'games' => $baseUrl . '?meta=GetContests_web&extensions='  . urlencode('{"persistedQuery":{"version":1,"sha256Hash":"47fadb7f5e08911fc697a7a44253d317311219413c19d63b5a94d0ec8b5cad33"}}'),

];
