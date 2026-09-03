<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerificationOtp extends Model
{
    protected $fillable = ['email', 'otp', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime'];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /** Generate, persist, and return a fresh 6-digit OTP for the given email. */
    public static function generate(string $email): self
    {
        // Remove any existing OTP for this email
        static::where('email', $email)->delete();

        return static::create([
            'email'      => $email,
            'otp'        => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => now()->addMinutes(10),
        ]);
    }
}
