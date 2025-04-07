<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Article;

class Articles extends Component
{

    public $stories;
    public $highlights;

    public $article;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->article = Article::where('article_type', 'Story')->latest('published')->first();
        $this->highlights = Article::where('article_type', 'Media')
                                        ->where('headline', 'not like','%: Game Highlights')
                                        ->latest('published')
                                        ->take(20)
                                        ->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $this->injectMedia();
        return view('components.articles');
    }

    public function injectMedia()
    {

        // $images = count($this->article->story_images);
        $videos = count($this->article->story_videos);

        for($v = 0; $v < $videos; $v++) {
            $tag = '<video' . ($v +1) . '>';
            $tagReplace = '<div class="iframe-wrapper"><iframe class="iframe" src="https://www.espn.com/core/video/iframe/_/id/' . $this->article->story_videos[$v] . '/endcard/false" allowfullscreen frameborder="0"></iframe></div>';
            $this->article->story = str_replace($tag, $tagReplace, $this->article->story);
        }

    }
}