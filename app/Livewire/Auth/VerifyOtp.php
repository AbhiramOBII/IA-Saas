<?php

namespace App\Livewire\Auth;

use App\Mail\VerifyEmailOtp;
use App\Mail\WelcomeApproved;
use App\Models\EmailVerificationOtp;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Component;

class VerifyOtp extends Component
{
    public string $otp   = '';
    public string $email = '';

    public bool $resent  = false;

    public function mount(): void
    {
        $this->email = session('registered_email', '');

        if (empty($this->email)) {
            $this->redirect(route('register'), navigate: true);
        }
    }

    #[Computed]
    public function maskedEmail(): string
    {
        [$local, $domain] = explode('@', $this->email);
        $masked = substr($local, 0, 2) . str_repeat('*', max(strlen($local) - 2, 3));
        return $masked . '@' . $domain;
    }

    public function verify(): void
    {
        $this->validate(['otp' => ['required', 'digits:6']]);

        $record = EmailVerificationOtp::where('email', $this->email)
            ->where('otp', $this->otp)
            ->first();

        if (! $record) {
            $this->addError('otp', 'Invalid code. Please check and try again.');
            return;
        }

        if ($record->isExpired()) {
            $record->delete();
            $this->addError('otp', 'This code has expired. Please request a new one.');
            return;
        }

        // Mark email as verified and auto-approve
        $user = User::where('email', $this->email)->first();
        $user->update([
            'email_verified_at' => now(),
            'status'            => 'approved',
        ]);

        $record->delete();
        session()->forget('registered_email');

        // Send welcome email with download link
        Mail::to($user->email)->send(
            new WelcomeApproved($user, config('app.download_url', '#'))
        );

        $this->redirect(route('auth.verified'), navigate: true);
    }

    public function resend(): void
    {
        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->redirect(route('register'), navigate: true);
            return;
        }

        $otp = EmailVerificationOtp::generate($user->email);
        Mail::to($user->email)->send(new VerifyEmailOtp($otp, $user->name));

        $this->resent = true;
        $this->otp    = '';
    }

    public function render()
    {
        return view('livewire.auth.verify-otp')
            ->layout('layouts.user-auth', ['title' => 'Verify Email']);
    }
}
