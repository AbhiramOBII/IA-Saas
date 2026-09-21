<?php

namespace App\Http\Middleware;

use App\Models\TokenMacBinding;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMacAddress
{
    public function handle(Request $request, Closure $next): Response
    {
        // Accept MAC from header or request body
        $clientMac = $request->header('X-Mac-Address') ?? $request->input('mac_address');

        // MAC address must be present
        if (empty($clientMac)) {
            return response()->json([
                'message' => 'MAC address is required. Send it via the X-Mac-Address header.',
                'code'    => 'mac_missing',
            ], 422);
        }

        $normalisedClient = TokenMacBinding::normaliseMac($clientMac);

        // Look up the binding for this token
        $passportToken = $request->user()->token();
        $binding       = TokenMacBinding::where('token_id', $passportToken->id)->first();

        // No binding record — token was issued before MAC binding was enforced,
        // or something is wrong. Revoke and force re-login.
        if (! $binding) {
            $passportToken->revoke();
            optional($passportToken->refreshToken)->revoke();

            return response()->json([
                'message' => 'Session invalid. Please log in again.',
                'code'    => 'mac_unbound',
            ], 401);
        }

        // MAC address mismatch — force logout
        if ($binding->mac_address !== $normalisedClient) {
            // Revoke access + refresh tokens
            $passportToken->revoke();
            optional($passportToken->refreshToken)->revoke();

            // Remove the binding
            $binding->delete();

            return response()->json([
                'message' => 'Device mismatch detected. Your session has been revoked for security.',
                'code'    => 'mac_mismatch',
            ], 401);
        }

        return $next($request);
    }
}
