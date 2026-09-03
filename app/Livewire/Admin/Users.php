<?php

namespace App\Livewire\Admin;

use App\Mail\WelcomeApproved;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $filter    = 'all'; // all | approved | disabled | deactivated

    public ?string $flash    = null;
    public string $flashType = 'success'; // success | error

    // ──────────────────────────────────────────────────────────────
    //  Query
    // ──────────────────────────────────────────────────────────────
    #[Computed]
    public function users()
    {
        return User::where('is_admin', false)
            ->when($this->search, fn ($q) =>
                $q->where(fn ($q) =>
                    $q->where('name',         'like', "%{$this->search}%")
                      ->orWhere('email',        'like', "%{$this->search}%")
                      ->orWhere('organization', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filter !== 'all', fn ($q) =>
                $q->where('status', $this->filter)
            )
            ->latest()
            ->paginate(15);
    }

    // ──────────────────────────────────────────────────────────────
    //  Status actions
    // ──────────────────────────────────────────────────────────────
    public function approve(int $userId): void
    {
        $user = User::whereNot('is_admin', true)->findOrFail($userId);
        $user->update(['status' => 'approved']);

        Mail::to($user->email)->send(
            new WelcomeApproved($user, config('app.download_url', '#'))
        );

        $this->setFlash("✓ {$user->name} approved — welcome email sent.");
    }

    public function disable(int $userId): void
    {
        $user = User::whereNot('is_admin', true)->findOrFail($userId);
        $user->update(['status' => 'disabled']);
        $this->setFlash("{$user->name} has been disabled.", 'error');
    }

    public function enable(int $userId): void
    {
        $user = User::whereNot('is_admin', true)->findOrFail($userId);
        $user->update(['status' => 'approved']);
        $this->setFlash("✓ {$user->name} has been re-enabled.");
    }

    public function deactivate(int $userId): void
    {
        $user = User::whereNot('is_admin', true)->findOrFail($userId);
        $user->update(['status' => 'deactivated']);
        $this->setFlash("{$user->name}'s account has been deactivated.", 'error');
    }

    // ──────────────────────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────────────────────
    private function setFlash(string $msg, string $type = 'success'): void
    {
        $this->flash     = $msg;
        $this->flashType = $type;
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilter(): void { $this->resetPage(); }

    public function render()
    {
        return view('livewire.admin.users')
            ->layout('layouts.admin', ['title' => 'Users']);
    }
}
