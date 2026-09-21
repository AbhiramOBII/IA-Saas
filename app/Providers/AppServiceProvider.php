<?php

namespace App\Providers;

use App\Mail\Transport\ZeptoMailTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Desktop-app tokens are issued directly via personal access tokens.
        // Give them a long lifetime so desktop sessions persist; they are
        // still revocable (logout) and device-bound (MAC binding).
        Passport::personalAccessTokensExpireIn(now()->addYear());

        Mail::extend('zepto', function () {
            return new ZeptoMailTransport(
                config('services.zepto.api_key')
            );
        });
    }
}
