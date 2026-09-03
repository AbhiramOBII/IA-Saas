<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class EditUser extends Component
{
    public User $user;

    public string $name         = '';
    public string $organization = '';
    public string $designation  = '';

    protected function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:100'],
            'organization' => ['nullable', 'string', 'max:150'],
            'designation'  => ['nullable', 'string', 'max:100'],
        ];
    }

    public function mount(int $id): void
    {
        $this->user = User::whereNot('is_admin', true)->findOrFail($id);

        $this->name         = $this->user->name;
        $this->organization = $this->user->organization ?? '';
        $this->designation  = $this->user->designation  ?? '';
    }

    public function save(): void
    {
        $this->validate();

        $this->user->update([
            'name'         => $this->name,
            'organization' => $this->organization,
            'designation'  => $this->designation,
        ]);

        session()->flash('flash', "✓ {$this->user->name}'s profile updated.");

        $this->redirect(route('admin.users'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.edit-user')
            ->layout('layouts.admin', ['title' => 'Edit User']);
    }
}
