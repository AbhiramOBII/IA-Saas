<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TokenMacBinding;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ----------------------------------------------------------------
    //  POST /api/auth/login
    //  Body: { email, password, mac_address }
    // ----------------------------------------------------------------
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'       => 'required|email',
            'password'    => 'required|string',
            'mac_address' => ['required', 'string', 'regex:/^([0-9A-Fa-f]{2}[:\-]){5}([0-9A-Fa-f]{2})$|^[0-9A-Fa-f]{12}$/'],
        ], [
            'mac_address.required' => 'A MAC address is required to identify this device.',
            'mac_address.regex'    => 'The MAC address format is invalid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // 1. Verify credentials
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid email or password.'], 401);
        }

        // 2. Email must be verified
        if (! $user->email_verified_at) {
            return response()->json(['message' => 'Please verify your email address before signing in.'], 403);
        }

        // 3. Account must be active
        if ($user->status !== 'approved') {
            $msg = match ($user->status) {
                'pending'     => 'Your account is awaiting admin approval.',
                'disabled'    => 'Your account has been temporarily disabled. Please contact support.',
                'deactivated',
                'rejected'    => 'Your account has been deactivated. Please contact support.',
                default       => 'Your account is not active.',
            };

            return response()->json(['message' => $msg], 403);
        }

        // 4. Issue tokens via Passport password grant
        $tokenResponse = $this->requestPasswordToken($request->email, $request->password);

        if (! $tokenResponse->successful()) {
            return response()->json(['message' => 'Authentication failed. Please try again.'], 401);
        }

        $tokens = $tokenResponse->json();

        // 5. Bind the new token to this device's MAC address
        $jti = TokenMacBinding::jtiFromJwt($tokens['access_token']);
        if ($jti) {
            TokenMacBinding::bind($jti, $request->mac_address);
        }

        return response()->json([
            'token_type'    => $tokens['token_type'],
            'access_token'  => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_in'    => $tokens['expires_in'],
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    // ----------------------------------------------------------------
    //  POST /api/auth/refresh
    //  Body: { refresh_token, mac_address }
    //  Header: X-Mac-Address  (either location is accepted)
    // ----------------------------------------------------------------
    public function refresh(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
            'mac_address'   => ['required', 'string', 'regex:/^([0-9A-Fa-f]{2}[:\-]){5}([0-9A-Fa-f]{2})$|^[0-9A-Fa-f]{12}$/'],
        ], [
            'mac_address.required' => 'A MAC address is required to identify this device.',
            'mac_address.regex'    => 'The MAC address format is invalid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $response = Http::asForm()->post(config('app.url').'/oauth/token', [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id'     => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'scope'         => '',
        ]);

        if (! $response->successful()) {
            return response()->json(['message' => 'Invalid or expired refresh token.'], 401);
        }

        $tokens = $response->json();

        // Bind the new access token to the same MAC address
        $jti = TokenMacBinding::jtiFromJwt($tokens['access_token']);
        if ($jti) {
            TokenMacBinding::bind($jti, $request->mac_address);
        }

        return response()->json($tokens);
    }

    // ----------------------------------------------------------------
    //  POST /api/auth/logout
    //  Header: Authorization: Bearer <token>
    //          X-Mac-Address: <mac>   (validated by middleware before this)
    // ----------------------------------------------------------------
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->token();

        // Clean up the MAC binding
        TokenMacBinding::where('token_id', $token->id)->delete();

        // Revoke the token and its refresh tokens
        $token->revoke();
        optional($token->refreshToken)->revoke();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    // ----------------------------------------------------------------
    //  GET /api/auth/me
    //  Header: Authorization: Bearer <token>
    //          X-Mac-Address: <mac>   (validated by middleware)
    // ----------------------------------------------------------------
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'organization'=> $user->organization,
            'designation' => $user->designation,
            'status'      => $user->status,
        ]);
    }

    // ----------------------------------------------------------------
    //  Internal helper — request a new token from the OAuth server
    // ----------------------------------------------------------------
    private function requestPasswordToken(string $email, string $password)
    {
        return Http::asForm()->post(config('app.url').'/oauth/token', [
            'grant_type'    => 'password',
            'client_id'     => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username'      => $email,
            'password'      => $password,
            'scope'         => '',
        ]);
    }
}
