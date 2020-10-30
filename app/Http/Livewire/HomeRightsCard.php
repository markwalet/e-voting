<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HomeRightsCard extends Component
{
    public function render()
    {
        return view('livewire.home-rights-card', [
            'user' => Auth::user()
        ]);
    }
}
