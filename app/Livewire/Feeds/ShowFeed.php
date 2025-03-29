<?php

namespace App\Livewire\Feeds;

use App\Models\Feed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class ShowFeed extends Component
{
    use WithPagination;

    public Feed $feed;

    public $editing = false;

    #[Validate('required|string|min:3|max:50')]
    public $name = '';

    #[Validate('required|string|min:3|max:150')]
    public $description = '';

    #[Validate('required|string|min:3|max:100')]
    public $job = '';

    #[Validate('required')]
    public $frequency = '';

    public function mount(Feed $feed)
    {
        $this->name = $feed->name;
        $this->description = $feed->description;
        $this->job = $feed->job;
        $this->frequency = $feed->frequency;
    }

    public function render()
    {
        return view('livewire.feeds.show-feed', [
            'logs' => $this->feed->logs()->latest()->paginate(7),
        ]);
    }

    public function save()
    {
        $this->validate();

        $this->feed->update(
            $this->only(['name', 'description', 'job', 'frequency'])
        );

        // $this->feed->save();
        $this->editing = false;

        session()->flash('status', 'Feed successfully updated.');
    }

    public function run()
    {
        call_user_func([$this->feed->job, 'dispatchSync']);
    }
}
