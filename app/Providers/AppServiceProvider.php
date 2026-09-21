<?php

namespace App\Providers;

use App\Mail\Transport\ZeptoMailTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Mail::extend('zepto', function () {
            return new ZeptoMailTransport(
                config('services.zepto.api_key')
            );
        });
    }
}
