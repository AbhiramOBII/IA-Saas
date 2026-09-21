<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes  (prefix: /api)
|--------------------------------------------------------------------------
|
| Desktop-app authentication via Laravel Passport personal access tokens.
|
|  POST   /api/auth/login     - Exchange credentials for a device-bound token
|  POST   /api/auth/refresh   - Rotate the current token       [auth:api]
|  POST   /api/auth/logout    - Revoke the current token        [auth:api]
|  GET    /api/auth/me        - Return authenticated user info  [auth:api]
|
*/

Route::prefix('auth')->name('api.auth.')->group(function () {

    // Public endpoint
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Protected endpoints - require valid Passport token AND matching MAC address
    Route::middleware(['auth:api', 'verify.mac'])->group(function () {
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');
        Route::get('/me',       [AuthController::class, 'me'])->name('me');
    });

});
