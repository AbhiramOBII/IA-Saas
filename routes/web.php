<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\EditUser;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\UsageMetrics;
use App\Livewire\Admin\Users as AdminUsers;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Verified;
use App\Livewire\Auth\VerifyOtp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Root
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('register'));

/*
|--------------------------------------------------------------------------
| USER-FACING ROUTES  (/register  /verify-email  /verify-email/success)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
});

// OTP + success are accessible without a session (user just registered)
Route::get('/verify-email',         VerifyOtp::class)->name('auth.verify-otp');
Route::get('/verify-email/success', Verified::class)->name('auth.verified');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES  (/admin/...)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // ── Guest-only admin auth ──────────────────────────────────────────
    Route::middleware('guest')->group(function () {
        Route::get('/login', Login::class)->name('login');
    });

    // ── Logout ────────────────────────────────────────────────────────
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('admin.login');
    })->middleware('auth')->name('logout');

    // ── Protected admin panel (auth + must be admin) ───────────────────
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/',              fn () => redirect()->route('admin.dashboard'));
        Route::get('/dashboard',     Dashboard::class)->name('dashboard');
        Route::get('/users',              AdminUsers::class)->name('users');
        Route::get('/users/{id}/edit',    EditUser::class)->name('users.edit');
        Route::get('/usage-metrics', UsageMetrics::class)->name('usage-metrics');
        Route::get('/settings',      Settings::class)->name('settings');
    });

});
