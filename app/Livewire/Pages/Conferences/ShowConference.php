<?php

namespace App\Livewire\Pages\Conferences;

use App\Models\Group as Conference;
use Livewire\Component;

class ShowConference extends Component
{
    public Conference $conference;

    public function mount(Conference $conference)
    {
        $this->conference = $conference;
    }
}
