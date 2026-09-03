<?php

namespace App\Mail;

use App\Models\EmailVerificationOtp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailOtp extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly EmailVerificationOtp $otp,
        public readonly string $userName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Intelligent Accelerators verification code');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.verify-email-otp');
    }
}
