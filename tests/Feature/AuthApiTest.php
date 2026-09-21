<?php

namespace Tests\Feature;

use App\Models\TokenMacBinding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private const MAC  = 'AA:BB:CC:DD:EE:FF';
    private const MAC2 = '11:22:33:44:55:66'; // different device

    // ── Test setup ────────────────────────────────────────────────

    protected function setUp(): void
    {
        parent::setUp();

        // Create a Passport personal-access client so createToken() works
        Artisan::call('passport:client', [
            '--personal' => true,
            '--name'     => 'Test Personal Access Client',
            '--no-interaction' => true,
        ]);
    }

    // ── Factories ─────────────────────────────────────────────────

    private function approvedUser(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'status'            => 'approved',
            'email_verified_at' => now(),
            'password'          => bcrypt('password'),
        ], $attrs));
    }

    /**
     * Issue a real Passport personal-access token for a user
     * and optionally bind a MAC address to it.
     * Returns ['jwt' => string, 'id' => string]
     */
    private function issueToken(User $user, ?string $mac = null): array
    {
        $result = $user->createToken('desktop-app');
        $jwt    = $result->accessToken;
        $id     = $result->token->id;

        if ($mac) {
            TokenMacBinding::bind($id, $mac);
        }

        return ['jwt' => $jwt, 'id' => $id];
    }

    // ══════════════════════════════════════════════════════════════
    //  POST /api/auth/login
    // ══════════════════════════════════════════════════════════════

    /** @test */
    public function login_returns_422_when_email_is_missing(): void
    {
        $res = $this->postJson('/api/auth/login', [
            'password'    => 'password',
            'mac_address' => self::MAC,
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function login_returns_422_when_password_is_missing(): void
    {
        $res = $this->postJson('/api/auth/login', [
            'email'       => 'user@example.com',
            'mac_address' => self::MAC,
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function login_returns_422_when_mac_address_is_missing(): void
    {
        $res = $this->postJson('/api/auth/login', [
            'email'    => 'user@example.com',
            'password' => 'password',
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['mac_address'])
            ->assertJsonPath('errors.mac_address.0', 'A MAC address is required to identify this device.');
    }

    /** @test */
    public function login_returns_422_for_invalid_mac_format(): void
    {
        $res = $this->postJson('/api/auth/login', [
            'email'       => 'user@example.com',
            'password'    => 'password',
            'mac_address' => 'NOT-VALID-MAC',
        ]);

        $res->assertStatus(422)
            ->assertJsonPath('errors.mac_address.0', 'The MAC address format is invalid.');
    }

    /** @test */
    public function login_accepts_hyphen_separated_mac(): void
    {
        $user = $this->approvedUser();

        Http::fake(['*/oauth/token' => Http::response($this->fakeTokenPayload(), 200)]);

        $res = $this->postJson('/api/auth/login', [
            'email'       => $user->email,
            'password'    => 'password',
            'mac_address' => 'AA-BB-CC-DD-EE-FF', // hyphens
        ]);

        $res->assertStatus(200);
    }

    /** @test */
    public function login_accepts_plain_hex_mac(): void
    {
        $user = $this->approvedUser();

        Http::fake(['*/oauth/token' => Http::response($this->fakeTokenPayload(), 200)]);

        $res = $this->postJson('/api/auth/login', [
            'email'       => $user->email,
            'password'    => 'password',
            'mac_address' => 'AABBCCDDEEFF', // no separators
        ]);

        $res->assertStatus(200);
    }

    /** @test */
    public function login_returns_401_for_wrong_password(): void
    {
        $user = $this->approvedUser();

        $res = $this->postJson('/api/auth/login', [
            'email'       => $user->email,
            'password'    => 'wrong-password',
            'mac_address' => self::MAC,
        ]);

        $res->assertStatus(401)
            ->assertJson(['message' => 'Invalid email or password.']);
    }

    /** @test */
    public function login_returns_401_for_non_existent_user(): void
    {
        $res = $this->postJson('/api/auth/login', [
            'email'       => 'nobody@example.com',
            'password'    => 'password',
            'mac_address' => self::MAC,
        ]);

        $res->assertStatus(401)
            ->assertJson(['message' => 'Invalid email or password.']);
    }

    /** @test */
    public function login_returns_403_for_unverified_email(): void
    {
        $user = User::factory()->unverified()->create([
            'status'   => 'approved',
            'password' => bcrypt('password'),
        ]);

        $res = $this->postJson('/api/auth/login', [
            'email'       => $user->email,
            'password'    => 'password',
            'mac_address' => self::MAC,
        ]);

        $res->assertStatus(403)
            ->assertJson(['message' => 'Please verify your email address before signing in.']);
    }

    /** @test */
    public function login_returns_403_for_pending_account(): void
    {
        $user = $this->approvedUser(['status' => 'pending']);

        $res = $this->postJson('/api/auth/login', [
            'email'       => $user->email,
            'password'    => 'password',
            'mac_address' => self::MAC,
        ]);

        $res->assertStatus(403)
            ->assertJson(['message' => 'Your account is awaiting admin approval.']);
    }

    /** @test */
    public function login_returns_403_for_disabled_account(): void
    {
        $user = $this->approvedUser(['status' => 'disabled']);

        $res = $this->postJson('/api/auth/login', [
            'email'       => $user->email,
            'password'    => 'password',
            'mac_address' => self::MAC,
        ]);

        $res->assertStatus(403);
    }

    /** @test */
    public function login_returns_403_for_deactivated_account(): void
    {
        $user = $this->approvedUser(['status' => 'deactivated']);

        $res = $this->postJson('/api/auth/login', [
            'email'       => $user->email,
            'password'    => 'password',
            'mac_address' => self::MAC,
        ]);

        $res->assertStatus(403);
    }

    /** @test */
    public function login_returns_tokens_and_user_for_approved_user(): void
    {
        $user = $this->approvedUser();

        Http::fake(['*/oauth/token' => Http::response($this->fakeTokenPayload(), 200)]);

        $res = $this->postJson('/api/auth/login', [
            'email'       => $user->email,
            'password'    => 'password',
            'mac_address' => self::MAC,
        ]);

        $res->assertStatus(200)
            ->assertJsonStructure([
                'token_type',
                'access_token',
                'refresh_token',
                'expires_in',
                'user' => ['id', 'name', 'email'],
            ])
            ->assertJsonPath('user.email', $user->email);
    }

    /** @test */
    public function login_stores_mac_binding_on_success(): void
    {
        $user = $this->approvedUser();
        $jti  = 'test-jti-' . uniqid();

        Http::fake(['*/oauth/token' => Http::response($this->fakeTokenPayload($jti), 200)]);

        $this->postJson('/api/auth/login', [
            'email'       => $user->email,
            'password'    => 'password',
            'mac_address' => self::MAC,
        ])->assertStatus(200);

        $this->assertDatabaseHas('token_mac_bindings', [
            'token_id'    => $jti,
            'mac_address' => TokenMacBinding::normaliseMac(self::MAC),
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    //  GET /api/auth/me
    // ══════════════════════════════════════════════════════════════

    /** @test */
    public function me_returns_401_without_token(): void
    {
        $this->getJson('/api/auth/me')->assertStatus(401);
    }

    /** @test */
    public function me_returns_422_when_mac_header_is_missing(): void
    {
        $user  = $this->approvedUser();
        $token = $this->issueToken($user, self::MAC);

        $res = $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
                    ->getJson('/api/auth/me');

        $res->assertStatus(422)
            ->assertJsonPath('code', 'mac_missing');
    }

    /** @test */
    public function me_returns_401_for_mac_mismatch(): void
    {
        $user  = $this->approvedUser();
        $token = $this->issueToken($user, self::MAC);

        $res = $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
                    ->withHeader('X-Mac-Address', self::MAC2)
                    ->getJson('/api/auth/me');

        $res->assertStatus(401)
            ->assertJsonPath('code', 'mac_mismatch');
    }

    /** @test */
    public function me_returns_401_when_no_binding_exists(): void
    {
        $user  = $this->approvedUser();
        $token = $this->issueToken($user); // no MAC bound

        $res = $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
                    ->withHeader('X-Mac-Address', self::MAC)
                    ->getJson('/api/auth/me');

        $res->assertStatus(401)
            ->assertJsonPath('code', 'mac_unbound');
    }

    /** @test */
    public function me_returns_user_data_with_valid_token_and_mac(): void
    {
        $user  = $this->approvedUser([
            'organization' => 'Emergent Consulting',
            'designation'  => 'Analyst',
        ]);
        $token = $this->issueToken($user, self::MAC);

        $res = $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
                    ->withHeader('X-Mac-Address', self::MAC)
                    ->getJson('/api/auth/me');

        $res->assertStatus(200)
            ->assertJsonStructure(['id', 'name', 'email', 'organization', 'designation', 'status'])
            ->assertJsonPath('email',        $user->email)
            ->assertJsonPath('organization', 'Emergent Consulting')
            ->assertJsonPath('designation',  'Analyst')
            ->assertJsonPath('status',       'approved');
    }

    /** @test */
    public function me_revokes_token_on_mac_mismatch(): void
    {
        $user  = $this->approvedUser();
        $token = $this->issueToken($user, self::MAC);

        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->withHeader('X-Mac-Address', self::MAC2)
             ->getJson('/api/auth/me')
             ->assertStatus(401);

        // After mismatch the binding should be gone
        $this->assertDatabaseMissing('token_mac_bindings', ['token_id' => $token['id']]);
    }

    // ══════════════════════════════════════════════════════════════
    //  POST /api/auth/logout
    // ══════════════════════════════════════════════════════════════

    /** @test */
    public function logout_returns_401_without_token(): void
    {
        $this->postJson('/api/auth/logout')->assertStatus(401);
    }

    /** @test */
    public function logout_returns_200_and_removes_mac_binding(): void
    {
        $user  = $this->approvedUser();
        $token = $this->issueToken($user, self::MAC);

        $res = $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
                    ->withHeader('X-Mac-Address', self::MAC)
                    ->postJson('/api/auth/logout');

        $res->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully.']);

        $this->assertDatabaseMissing('token_mac_bindings', ['token_id' => $token['id']]);
    }

    /** @test */
    public function logout_revokes_the_access_token(): void
    {
        $user  = $this->approvedUser();
        $token = $this->issueToken($user, self::MAC);

        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->withHeader('X-Mac-Address', self::MAC)
             ->postJson('/api/auth/logout')
             ->assertStatus(200);

        // Subsequent call with same token should be rejected
        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->withHeader('X-Mac-Address', self::MAC)
             ->getJson('/api/auth/me')
             ->assertStatus(401);
    }

    // ══════════════════════════════════════════════════════════════
    //  POST /api/auth/refresh
    // ══════════════════════════════════════════════════════════════

    /** @test */
    public function refresh_returns_422_when_refresh_token_is_missing(): void
    {
        $this->postJson('/api/auth/refresh', [
            'mac_address' => self::MAC,
        ])->assertStatus(422)->assertJsonValidationErrors(['refresh_token']);
    }

    /** @test */
    public function refresh_returns_422_when_mac_is_missing(): void
    {
        $this->postJson('/api/auth/refresh', [
            'refresh_token' => 'some-token',
        ])->assertStatus(422)->assertJsonValidationErrors(['mac_address']);
    }

    /** @test */
    public function refresh_returns_401_for_invalid_refresh_token(): void
    {
        Http::fake(['*/oauth/token' => Http::response(['error' => 'invalid_grant'], 400)]);

        $res = $this->postJson('/api/auth/refresh', [
            'refresh_token' => 'expired-or-bad-token',
            'mac_address'   => self::MAC,
        ]);

        $res->assertStatus(401)
            ->assertJson(['message' => 'Invalid or expired refresh token.']);
    }

    /** @test */
    public function refresh_returns_new_tokens_on_valid_refresh_token(): void
    {
        $jti = 'new-jti-' . uniqid();

        Http::fake([
            '*/oauth/token' => Http::response($this->fakeTokenPayload($jti), 200),
        ]);

        $res = $this->postJson('/api/auth/refresh', [
            'refresh_token' => 'valid-refresh-token',
            'mac_address'   => self::MAC,
        ]);

        $res->assertStatus(200)
            ->assertJsonStructure(['token_type', 'access_token', 'refresh_token', 'expires_in']);
    }

    /** @test */
    public function refresh_binds_new_token_to_mac_address(): void
    {
        $jti = 'refresh-jti-' . uniqid();

        Http::fake([
            '*/oauth/token' => Http::response($this->fakeTokenPayload($jti), 200),
        ]);

        $this->postJson('/api/auth/refresh', [
            'refresh_token' => 'valid-refresh-token',
            'mac_address'   => self::MAC,
        ])->assertStatus(200);

        $this->assertDatabaseHas('token_mac_bindings', [
            'token_id'    => $jti,
            'mac_address' => TokenMacBinding::normaliseMac(self::MAC),
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    //  MAC normalisation (unit-style, lives here for convenience)
    // ══════════════════════════════════════════════════════════════

    /** @test */
    public function mac_normalisation_handles_colons(): void
    {
        $this->assertSame('aa:bb:cc:dd:ee:ff', TokenMacBinding::normaliseMac('AA:BB:CC:DD:EE:FF'));
    }

    /** @test */
    public function mac_normalisation_handles_hyphens(): void
    {
        $this->assertSame('aa:bb:cc:dd:ee:ff', TokenMacBinding::normaliseMac('AA-BB-CC-DD-EE-FF'));
    }

    /** @test */
    public function mac_normalisation_handles_plain_hex(): void
    {
        $this->assertSame('aa:bb:cc:dd:ee:ff', TokenMacBinding::normaliseMac('AABBCCDDEEFF'));
    }

    // ── Private helpers ───────────────────────────────────────────

    /**
     * Build a fake OAuth token response payload with a parseable JWT.
     */
    private function fakeTokenPayload(string $jti = 'fake-jti'): array
    {
        return [
            'token_type'    => 'Bearer',
            'access_token'  => $this->fakeJwt($jti),
            'refresh_token' => 'fake-refresh-' . uniqid(),
            'expires_in'    => 3600,
        ];
    }

    /**
     * Build a 3-part JWT-like string whose payload contains the given jti.
     */
    private function fakeJwt(string $jti = 'fake-jti'): string
    {
        $header  = rtrim(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '=');
        $payload = rtrim(base64_encode(json_encode(['jti' => $jti, 'sub' => 1])), '=');
        return $header . '.' . $payload . '.fake-signature';
    }
}
