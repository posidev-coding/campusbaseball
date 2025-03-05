<?php

namespace App\Jobs\Feeds;

use App\Models\Team;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Batchable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SyncTeam implements ShouldQueue
{
    use Batchable, Queueable;

    public $tries = 1;

    private $url;

    public function __construct(String $req)
    {
        $this->url = Str::isUrl($req) ? $req : config('espn.teams') . '/' . $req;
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    public function handle(): void
    {

        $data = Http::get($this->url)->json();

        $model = Team::updateOrCreate(
            [
                'id' => $data['id']
            ],
            [
                'id' => $data['id'],
                'slug' => $data['slug'] ?? null,
                'location' => $data['location'] ?? null,
                'name' => $data['name'] ?? null,
                'nickname' => $data['nickname'] ?? null,
                'abbreviation' => $data['abbreviation'] ?? null,
                'display_name' => $data['displayName'] ?? null,
                'short_display_name' => $data['shortDisplayName'] ?? null,
                'color' => $data['color'] ?? null,
                'logos' => $data['logos'] ?? null,
            ]
        );
    }
}
