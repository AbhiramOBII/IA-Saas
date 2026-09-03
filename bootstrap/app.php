<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\VerifyMacAddress;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register the admin-guard middleware alias
        $middleware->alias([
            'admin'      => EnsureAdmin::class,
            'verify.mac' => VerifyMacAddress::class,
        ]);

        // Redirect unauthenticated requests to the correct login page:
        //   - admin routes  → /admin/login
        //   - everything else → /register (no web login for regular users)
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }
            return route('register');
        });

        // Redirect already-authenticated users away from guest-only pages (login/register)
        // to the admin dashboard — prevents the redirect loop on /register
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // A stale "remember me" cookie (e.g. after migrate:fresh) causes a
        // TypeError in SessionGuard::userFromRecaller when the stored token is
        // null. Catch it and redirect to login so the user sees a clean page.
        $exceptions->render(function (\TypeError $e, $request) {
            if (str_contains($e->getMessage(), 'hash_equals') &&
                str_contains($e->getFile(), 'SessionGuard')) {
                Auth::logout();
                return redirect()->route('admin.login')
                    ->withErrors(['email' => 'Your session has expired. Please sign in again.']);
            }
        });
    })->create();
