<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $downloadUrl = '#',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to Intelligent Accelerators — You\'re approved!');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.welcome-approved');
    }
}
