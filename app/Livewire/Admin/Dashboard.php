<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Laravel\Passport\Token;
use Livewire\Component;

class Dashboard extends Component
{
    public int $totalUsers    = 0;
    public int $approvedUsers = 0;
    public int $rejectedUsers = 0;
    public int $activeTokens  = 0;

    public function mount(): void
    {
        $base = User::where('is_admin', false);

        $this->totalUsers    = (clone $base)->count();
        $this->approvedUsers = (clone $base)->where('status', 'approved')->count();
        $this->rejectedUsers = (clone $base)->where('status', 'rejected')->count();
        $this->activeTokens  = Token::where('revoked', false)->count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
