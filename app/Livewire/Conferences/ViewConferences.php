<?php

namespace App\Livewire\Conferences;

use App\Models\Group;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Standings')]
class ViewConferences extends Component
{
    #[\Livewire\Attributes\Computed]
    public function conferences()
    {

        return Group::where('is_conference', 1)->orderBy('name')->get();
    }
}
