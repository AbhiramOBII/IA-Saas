<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class Verified extends Component
{
    public function render()
    {
        return view('livewire.auth.verified')
            ->layout('layouts.user-auth', ['title' => 'Request Received']);
    }
}
