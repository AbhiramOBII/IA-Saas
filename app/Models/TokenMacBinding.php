<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenMacBinding extends Model
{
    protected $fillable = ['token_id', 'mac_address'];

    // ── Helpers ────────────────────────────────────────────────────

    /**
     * Normalise a MAC address to lowercase colon-separated format.
     * Accepts: AA:BB:CC:DD:EE:FF  or  AA-BB-CC-DD-EE-FF  or  AABBCCDDEEFF
     */
    public static function normaliseMac(string $mac): string
    {
        // Strip any separator and uppercase
        $clean = strtoupper(preg_replace('/[^a-fA-F0-9]/', '', $mac));

        if (strlen($clean) !== 12) {
            return strtolower($mac); // pass through if unexpected length
        }

        return strtolower(implode(':', str_split($clean, 2)));
    }

    /**
     * Store a new MAC binding, replacing any existing binding for this token.
     */
    public static function bind(string $tokenId, string $mac): self
    {
        return self::updateOrCreate(
            ['token_id' => $tokenId],
            ['mac_address' => self::normaliseMac($mac)]
        );
    }

    /**
     * Extract the jti (token ID) from a raw JWT string without verifying
     * the signature — we only need the claim, not to re-verify the token.
     */
    public static function jtiFromJwt(string $jwt): ?string
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

        return $payload['jti'] ?? null;
    }
}
