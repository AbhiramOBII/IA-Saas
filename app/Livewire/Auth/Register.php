<?php

namespace App\Livewire\Auth;

use App\Mail\VerifyEmailOtp;
use App\Models\EmailVerificationOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Register extends Component
{
    public string $name            = '';
    public string $organization    = '';
    public string $designation     = '';
    public string $email           = '';
    public string $password        = '';
    public string $password_confirmation = '';
    public bool   $terms           = false;

    protected function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:100'],
            'organization' => ['required', 'string', 'max:150'],
            'designation'  => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'terms'        => ['accepted'],
        ];
    }

    protected array $messages = [
        'terms.accepted'  => 'You must accept the Terms of Use to continue.',
        'password.confirmed' => 'Passwords do not match.',
    ];

    public function register(): void
    {
        $this->validate();

        $user = User::create([
            'name'         => $this->name,
            'organization' => $this->organization,
            'designation'  => $this->designation,
            'email'        => $this->email,
            'password'     => Hash::make($this->password),
            'is_admin'     => false,
        ]);

        $otp = EmailVerificationOtp::generate($user->email);

        Mail::to($user->email)->send(new VerifyEmailOtp($otp, $user->name));

        session()->flash('registered_email', $user->email);

        $this->redirect(route('auth.verify-otp'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.user-auth', ['title' => 'Create Account']);
    }
}
