<?php

namespace App\Livewire\Conferences;

use App\Models\Conference;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Standings')]
class ViewConferences extends Component
{
    #[\Livewire\Attributes\Computed]
    public function conferences()
    {

        return Conference::whereHas('standings')->orderBy('name')->get();
    }
}
