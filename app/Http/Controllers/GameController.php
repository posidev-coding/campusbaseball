<?php

namespace App\Http\Controllers;

use App\Jobs\Feeds\SyncTeam;
use App\Models\Game;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public static function sync(int $gameId, string $mode = 'default'): Game
    {

        $data = Http::get(config('espn.games').'/'.$gameId)->json();

        // Instantiate a working model
        $game = self::game($gameId, $data); // 0 callouts
        $game = self::status($game, $data);
        $game = self::scores($game, $data);

        if ($mode != 'default') {
            $game = self::stats($game, $data);
            $game = self::boxes($game, $data);
        }

        if ($mode == 'full') {
            $game = self::records($game, $data);
            $game = self::broadcasts($game, $data);

            $away = Team::findOr($game->away_id, function () use ($game) {
                SyncTeam::dispatch($game->away_id);
            });

            $home = Team::findOr($game->home_id, function () use ($game) {
                SyncTeam::dispatch($game->home_id);
            });

            // $game = self::rosters($game, $data); // offload to job
        }

        return self::store($game);
    }

    public static function store($game)
    {
        $model = Game::find($game->id);

        if ($model) {
            $model->fill($game->toArray());
            $model->save();
        } else {
            $model = Game::create($game->toArray());
        }

        return $model;
    }

    public static function game(int $gameId, mixed $data): Game
    {

        $seasons = config('espn.seasons').'/';

        $season = Str::of(Str::chopStart($data['season']['$ref'], $seasons))->take(4);

        $season_type = Str::of(Str::chopStart($data['seasonType']['$ref'], $seasons.$season.'/types/'))->take(1);

        $comp = $data['competitions'][0];

        $away_team = $comp['competitors'][0]['homeAway'] == 'away' ? $comp['competitors'][0] : $comp['competitors'][1];
        $home_team = $comp['competitors'][0]['homeAway'] == 'away' ? $comp['competitors'][1] : $comp['competitors'][0];

        $venue = $comp['venue'] ?? null;
        if ($venue) {
            unset($venue['$ref']);
        }

        $game = new Game([
            'id' => $gameId,
            'game_date' => Carbon::parse($data['date'])->setTimezone('America/New_York')->toDateString(),
            'game_time' => Carbon::parse($data['date']),
            'name' => $data['name'] ?? null,
            'short_name' => $data['shortName'] ?? null,
            'season_id' => $season,
            'season_type_id' => $season_type,
            'away_id' => $away_team['id'],
            'away_rank' => $away_team['curatedRank']['current'] ?? 0,
            'away_winner' => $away_team['winner'] ?? 0,
            'home_id' => $home_team['id'],
            'home_rank' => $home_team['curatedRank']['current'] ?? 0,
            'home_winner' => $home_team['winner'] ?? 0,
            'venue' => $venue ?? null,
        ]);

        return $game;
    }

    public static function status(Game $game, mixed $data): Game
    {

        $comp = $data['competitions'][0];

        // GET #2
        $status = Http::get($comp['status']['$ref'])->json();
        unset($status['$ref']);

        $game->status_id = $status['type']['id'];
        $game->status = $status;

        return $game;
    }

    public static function scores(Game $game, mixed $data): Game
    {
        $comp = $data['competitions'][0];

        $away_team = $comp['competitors'][0]['homeAway'] == 'away' ? $comp['competitors'][0] : $comp['competitors'][1];
        $home_team = $comp['competitors'][0]['homeAway'] == 'away' ? $comp['competitors'][1] : $comp['competitors'][0];

        // GET #3
        $away_score = Http::get($away_team['score']['$ref'])->json();
        // GET #4
        $home_score = Http::get($home_team['score']['$ref'])->json();

        $game->away_runs = $away_score['value'] ?? 0;
        $game->away_hits = $away_score['hits'] ?? 0;
        $game->away_errors = $away_score['errors'] ?? 0;
        $game->away_winner = $away_team['winner'] ?? 0;

        $game->home_runs = $home_score['value'] ?? 0;
        $game->home_hits = $home_score['hits'] ?? 0;
        $game->home_errors = $home_score['errors'] ?? 0;
        $game->home_winner = $home_team['winner'] ?? 0;

        return $game;
    }

    public static function broadcasts(Game $game, mixed $data): Game
    {

        $comp = $data['competitions'][0];
        // GET #5
        $broadcasts = Http::get($comp['broadcasts']['$ref'])->json()['items'];
        $game->broadcasts = $broadcasts;

        return $game;
    }

    public static function boxes(Game $game, mixed $data): Game
    {

        $comp = $data['competitions'][0];

        $away_team = $comp['competitors'][0]['homeAway'] == 'away' ? $comp['competitors'][0] : $comp['competitors'][1];
        $home_team = $comp['competitors'][0]['homeAway'] == 'away' ? $comp['competitors'][1] : $comp['competitors'][0];

        $game->away_box = isset($away_team['linescores']) ? self::box($away_team['linescores']['$ref'].'&limit=100') : [];
        $game->home_box = isset($home_team['linescores']) ? self::box($home_team['linescores']['$ref'].'&limit=100') : [];

        return $game;
    }

    public static function box(string $ref): array
    {

        // GET #6 & 7
        $box = Http::get($ref)->json()['items'];

        foreach ($box as $i => $items) {

            $box[$i]['inning'] = $box[$i]['period'];
            $box[$i]['runs'] = $box[$i]['value'];

            unset($box[$i]['$ref']);
            unset($box[$i]['source']);
            unset($box[$i]['value']);
            unset($box[$i]['displayValue']);
            unset($box[$i]['period']);
        }

        return $box;
    }

    public static function records(Game $game, mixed $data): Game
    {

        $comp = $data['competitions'][0];

        $away_team = $comp['competitors'][0]['homeAway'] == 'away' ? $comp['competitors'][0] : $comp['competitors'][1];
        $home_team = $comp['competitors'][0]['homeAway'] == 'away' ? $comp['competitors'][1] : $comp['competitors'][0];

        $game->away_records = isset($away_team['record']) ? self::teamRecords($away_team['record']['$ref'].'&limit=100') : [];
        $game->home_records = isset($home_team['record']) ? self::teamRecords($home_team['record']['$ref'].'&limit=100') : [];

        return $game;
    }

    public static function teamRecords(string $ref): array
    {

        // GET #8 & 9
        $records = Http::get($ref)->json()['items'];

        foreach ($records as $i => $record) {

            unset($records[$i]['$ref']);
            unset($records[$i]['id']);
            unset($records[$i]['value']);
            unset($records[$i]['displayValue']);
            unset($records[$i]['stats']);
        }

        return $records;
    }

    public static function rosters(Game $game, mixed $data): Game
    {

        $comp = $data['competitions'][0];

        $away_team = $comp['competitors'][0]['homeAway'] == 'away' ? $comp['competitors'][0] : $comp['competitors'][1];
        $home_team = $comp['competitors'][0]['homeAway'] == 'away' ? $comp['competitors'][1] : $comp['competitors'][0];

        dd($away_team['roster']['$ref']);

        $game->away_roster = isset($away_team['roster']) ? self::teamRoster($away_team['roster']['$ref']) : [];
        $game->home_roster = isset($home_team['roster']) ? self::teamRoster($home_team['roster']['$ref']) : [];

        return $game;
    }

    public static function teamRoster(string $ref): array
    {

        $data = [];

        // GET #10 & 11
        $roster = Http::get($ref)->json();

        foreach ($roster['entries'] as $i => $player) {

            $athlete = [
                'id' => $player['playerId'],
                'starter' => $player['starter'],
                'batOrder' => $player['batOrder'],
                'position' => Http::get($player['position']['$ref'])->json()['abbreviation'],
                'subbedIn' => $player['subbedIn']['didSub'],
                'subbedOut' => $player['subbedOut']['didSub'],
                'batOrder' => $player['batOrder'],
                'stats' => [],
            ];

            // cant do this
            $cats = Http::get($player['statistics']['$ref'])->json()['splits']['categories'];

            foreach ($cats as $cat) {

                $category = $cat['name'];

                $stats = [];

                foreach ($cat['stats'] as $stat) {
                    array_push($stats, [
                        'key' => $stat['name'],
                        'name' => $stat['displayName'],
                        'abbr' => $stat['abbreviation'],
                        'value' => $stat['value'] ?? null,
                    ]);
                }

                $athlete['stats'][$category] = $stats;
                // array_push($athlete['stats'][$category], $stats);
            }

            array_push($data, $athlete);
        }

        return $data;
    }

    public static function stats(Game $game, mixed $data): Game
    {

        $comp = $data['competitions'][0];

        $away_team = $comp['competitors'][0]['homeAway'] == 'away' ? $comp['competitors'][0] : $comp['competitors'][1];
        $home_team = $comp['competitors'][0]['homeAway'] == 'away' ? $comp['competitors'][1] : $comp['competitors'][0];

        $game->away_stats = isset($away_team['statistics']) ? self::teamStats($away_team['statistics']['$ref']) : [];
        $game->home_stats = isset($home_team['statistics']) ? self::teamStats($home_team['statistics']['$ref']) : [];

        return $game;
    }

    public static function teamStats(string $ref): array
    {

        $data = [];

        $stats = Http::get($ref)->json()['splits']['categories'];

        foreach ($stats as $i => $val) {

            $category = $stats[$i]['name'];
            $categoryData = [];

            foreach ($stats[$i]['stats'] as $stat) {
                array_push($categoryData, [
                    'key' => $stat['name'],
                    'name' => $stat['displayName'],
                    'abbr' => $stat['abbreviation'],
                    'value' => $stat['value'] ?? null,
                ]);
            }

            array_push($data, [
                'category' => $category,
                'stats' => $categoryData,
            ]);
        }

        return $data;
    }
}
