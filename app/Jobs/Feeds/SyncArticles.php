<?php

namespace App\Jobs\Feeds;

use Exception;

use Carbon\Carbon;
use App\Models\Game;

use App\Models\Team;
use App\Models\Article;
use App\Models\Ranking;

use Illuminate\Support\Str;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SyncArticles implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 600; // Ten minutes

    private $team;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($team = null)
    {
        $this->team = $team;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // If this is parent job, go ahead and batch out the other team-specific jobs
        if(!$this->team) {

            $teams = [];

            // Iterate the Top 25 for some extra team news and previews
            $week = Ranking::where('season_id', config('espn.year'))->max('week_nbr');
            $ranks = Ranking::where('season_id', config('espn.year'))->where('week_nbr', $week)->get();
            
            foreach($ranks as $rank) {
                array_push($teams, $rank->team_id);
            }

            $games = Game::select('home_id','away_id')
                            ->where('status_id', 3)
                            ->orderBy('game_date', 'desc')
                            ->take(200)
                            ->get();

            foreach($games as $game) {
                array_push($teams, $game->away_id);
                array_push($teams, $game->home_id);
            }

            $teams = array_unique($teams);

            $jobs = [];

            foreach($teams as $team) {

                array_push($jobs, new SyncArticles($team));

            }

            if ($this->batch() && ! $this->batch()->cancelled()) {
                $this->batch()->add($jobs);
            } else {
                Bus::batch($jobs)
                    ->name('Team News')
                    ->dispatch();
            }

        }

        $url = $this->team ? config('espn.news') . '&team=' . $this->team : config('espn.news');

        $response = Http::get($url);

        $articles = $response->json()['articles'];

        foreach ($articles as $a) {
            $this->article($a);
        }

    }

    public function article(mixed $a)
    {

        $isBaseball = false;

        $image = isset($a['images'][0]['url']) ? $a['images'][0]['url'] : null;

        $url = isset($a['links']['api']['news']['href']) ? $a['links']['api']['news']['href'] : $a['links']['api']['self']['href'];

        $articleType = $a['type'];

        $article = Http::get($url)->json();

        if (isset($article['headlines']) || isset($article['videos'])) {

            $article = isset($article['headlines']) ? $article['headlines'][0] : $article['videos'][0];

            foreach($article['categories'] as $category) {
                if($category['type'] == 'league' && $category['description'] == 'NCAA Baseball') {
                    $isBaseball = true;
                }
            }

            // Skip if not an NCAA Baseball related article
            if(!$isBaseball) return;

            $published = Carbon::parse($article['published'] ?? $article['originalPublishDate'], 'UTC');
            $published_date = Carbon::createFromFormat('Y-m-d H:i:s', $published);

            $teams = [];

            if($this->team) {
                array_push($teams, $this->team);
            }

            if(isset($article['gameId'])) {
                if($game = Game::find($article['gameId'])) {
                    array_push($teams, $game->away_id);
                    array_push($teams, $game->home_id);
                }
            }
            
            if(Str::contains($article['headline'], ': Game Highlights')) {

                $matchup = explode(':', $article['headline'])[0];
                $teamNames = explode(' vs. ', $matchup);
                $teamModels =  Team::whereIn('location', $teamNames)
                                ->orWhereIn('nickname', $teamNames)
                                ->orWhereIn('display_name', $teamNames)
                                ->get();

                foreach($teamModels as $tm) {
                    array_push($teams, $tm->id);
                }
            }

            $teams = array_unique($teams);

            $storyImages = [];
            $storyVideos = [];

            if($articleType == 'Story' && isset($article['images'])) {
                foreach($article['images'] as $ai) {
                    array_push($storyImages, $ai['url']);
                }
            }

            if($articleType == 'Story' && isset($article['video'])) {
                foreach($article['video'] as $av) {
                    array_push($storyVideos, $av['id']);
                }
            }

            $transaction = Article::updateOrCreate(
                [
                    'id' => $article['id']
                ],
                [
                    'article_type' => $articleType ?? null,
                    'link' => $article['links']['web']['href'] ?? null,
                    'image' => $image,
                    'teams' => $teams,
                    'game_id' => $article['gameId'] ?? null,
                    'headline' => $article['headline'] ?? null,
                    'description' => $article['description'] ?? null,
                    'story' => $article['story'] ?? null,
                    'story_images' => $storyImages,
                    'story_videos' => $storyVideos,
                    'published' => $published_date
                ]
            );
        }
    }

}