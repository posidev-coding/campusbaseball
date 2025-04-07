<?php

namespace App\Livewire\Articles;

use App\Models\Article;
use Livewire\Component;

class ShowArticle extends Component
{

    public Article $article;

    public function mount(Article $article) {
        $this->article = $article;
    }
    
    public function render()
    {
        $this->injectMedia();
        return view('livewire.articles.show-article');
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
