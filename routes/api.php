<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes  (prefix: /api)
|--------------------------------------------------------------------------
|
| Desktop-app authentication via Laravel Passport password grant.
|
|  POST   /api/auth/login     – Exchange credentials for tokens
|  POST   /api/auth/refresh   – Exchange refresh_token for new access_token
|  POST   /api/auth/logout    – Revoke the current token      [auth:api]
|  GET    /api/auth/me        – Return authenticated user info [auth:api]
|
*/

Route::prefix('auth')->name('api.auth.')->group(function () {

    // Public endpoints
    Route::post('/login',   [AuthController::class, 'login'])->name('login');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');

    // Protected endpoints — require valid Passport token AND matching MAC address
    Route::middleware(['auth:api', 'verify.mac'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me',      [AuthController::class, 'me'])->name('me');
    });

});
