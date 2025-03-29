<?php

namespace App\Livewire\Pages\Conferences;

use App\Models\Group;
use Livewire\Component;

class ViewConferences extends Component
{
    #[\Livewire\Attributes\Computed]
    public function conferences()
    {

        return Group::where('is_conference', 1)->orderBy('name')->get();
    }
}
