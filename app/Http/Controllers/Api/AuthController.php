<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TokenMacBinding;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // MAC address: AA:BB:CC:DD:EE:FF | AA-BB-CC-DD-EE-FF | AABBCCDDEEFF
    private const MAC_REGEX = '/^([0-9A-Fa-f]{2}[:\-]){5}([0-9A-Fa-f]{2})$|^[0-9A-Fa-f]{12}$/';

    // ----------------------------------------------------------------
    //  POST /api/auth/login
    //  Body: { email, password, mac_address }
    // ----------------------------------------------------------------
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'       => 'required|email',
            'password'    => 'required|string',
            'mac_address' => ['required', 'string', 'regex:'.self::MAC_REGEX],
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
            return response()->json(['message' => $this->inactiveMessage($user->status)], 403);
        }

        // 4. Issue a token directly (no OAuth round-trip) and bind it to the device
        return $this->issueToken($user, $request->mac_address);
    }

    // ----------------------------------------------------------------
    //  POST /api/auth/refresh
    //  Header: Authorization: Bearer <token>, X-Mac-Address: <mac>
    //  Rotates the current token for a fresh one on the same device.
    //  (auth:api + verify.mac run before this.)
    // ----------------------------------------------------------------
    public function refresh(Request $request): JsonResponse
    {
        $user      = $request->user();
        $oldToken  = $user->token();
        $clientMac = $request->header('X-Mac-Address') ?? $request->input('mac_address');

        // Issue the replacement token bound to the same device
        $response = $this->issueToken($user, $clientMac);

        // Revoke the old token + its MAC binding
        TokenMacBinding::where('token_id', $oldToken->id)->delete();
        $oldToken->revoke();

        return $response;
    }

    // ----------------------------------------------------------------
    //  POST /api/auth/logout
    //  Header: Authorization: Bearer <token>, X-Mac-Address: <mac>
    // ----------------------------------------------------------------
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->token();

        TokenMacBinding::where('token_id', $token->id)->delete();
        $token->revoke();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    // ----------------------------------------------------------------
    //  GET /api/auth/me
    //  Header: Authorization: Bearer <token>, X-Mac-Address: <mac>
    // ----------------------------------------------------------------
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'           => $user->id,
            'name'         => $user->name,
            'email'        => $user->email,
            'organization' => $user->organization,
            'designation'  => $user->designation,
            'status'       => $user->status,
        ]);
    }

    // ----------------------------------------------------------------
    //  Helpers
    // ----------------------------------------------------------------

    /**
     * Create a personal access token for the user, bind it to the given
     * MAC address, and build the JSON auth response.
     */
    private function issueToken(User $user, string $mac): JsonResponse
    {
        $result = $user->createToken('desktop-app');
        $token  = $result->token;

        TokenMacBinding::bind($token->id, $mac);

        $expiresIn = $token->expires_at
            ? (int) round(now()->diffInSeconds($token->expires_at, false))
            : null;

        return response()->json([
            'token_type'   => 'Bearer',
            'access_token' => $result->accessToken,
            'expires_in'   => $expiresIn,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    private function inactiveMessage(string $status): string
    {
        return match ($status) {
            'pending'     => 'Your account is awaiting admin approval.',
            'disabled'    => 'Your account has been temporarily disabled. Please contact support.',
            'deactivated',
            'rejected'    => 'Your account has been deactivated. Please contact support.',
            default       => 'Your account is not active.',
        };
    }
}
