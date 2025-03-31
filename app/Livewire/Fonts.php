<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Fonts extends Component
{
    public string $font = 'font-sans';

    public string $path = '/';

    public $fonts = [
        'font-sans' => 'Noto Sans',
        'font-poppins' => 'Poppins',
        'font-montserrat' => 'Montserrat',
        'font-inter' => 'Inter',
        'font-roboto' => 'Roboto',
        'font-roboto-condensed' => 'Roboto Condensed',
        'font-lato' => 'Lato',
        'font-fira-sans' => 'Fira Sans',
        'font-kanit' => 'Kanit',
    ];

    public function mount()
    {
        $this->path = Route::getCurrentRequest()->getRequestUri();
        $this->font = Cache::get('font', 'font-sans');
    }

    public function setFont($value)
    {
        Cache::put('font', $value);
        Cache::put('font-name', $this->fonts[$value]);
        $this->font = $value;

        return redirect()->to($this->path);

    }

    public function render()
    {
        return view('livewire.fonts');
    }
}
