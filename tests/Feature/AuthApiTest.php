<?php

namespace Tests\Feature;

use App\Models\TokenMacBinding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
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

        // Personal-access client is required for createToken() to work.
        Artisan::call('passport:client', [
            '--personal'       => true,
            '--name'           => 'Test Personal Access Client',
            '--no-interaction' => true,
        ]);
    }

    // ── Factories / helpers ───────────────────────────────────────

    private function approvedUser(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'status'            => 'approved',
            'email_verified_at' => now(),
            'password'          => bcrypt('password'),
        ], $attrs));
    }

    /**
     * Issue a real device-bound token for a user.
     * Returns ['jwt' => string, 'id' => string].
     */
    private function issueToken(User $user, ?string $mac = null): array
    {
        $result = $user->createToken('desktop-app');

        if ($mac) {
            TokenMacBinding::bind($result->token->id, $mac);
        }

        return ['jwt' => $result->accessToken, 'id' => $result->token->id];
    }

    private function login(User $user, string $mac = self::MAC)
    {
        return $this->postJson('/api/auth/login', [
            'email'       => $user->email,
            'password'    => 'password',
            'mac_address' => $mac,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    //  POST /api/auth/login
    // ══════════════════════════════════════════════════════════════

    /** @test */
    public function login_returns_422_when_email_is_missing(): void
    {
        $this->postJson('/api/auth/login', [
            'password'    => 'password',
            'mac_address' => self::MAC,
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function login_returns_422_when_password_is_missing(): void
    {
        $this->postJson('/api/auth/login', [
            'email'       => 'user@example.com',
            'mac_address' => self::MAC,
        ])->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function login_returns_422_when_mac_address_is_missing(): void
    {
        $this->postJson('/api/auth/login', [
            'email'    => 'user@example.com',
            'password' => 'password',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['mac_address'])
          ->assertJsonPath('errors.mac_address.0', 'A MAC address is required to identify this device.');
    }

    /** @test */
    public function login_returns_422_for_invalid_mac_format(): void
    {
        $this->postJson('/api/auth/login', [
            'email'       => 'user@example.com',
            'password'    => 'password',
            'mac_address' => 'NOT-VALID-MAC',
        ])->assertStatus(422)
          ->assertJsonPath('errors.mac_address.0', 'The MAC address format is invalid.');
    }

    /** @test */
    public function login_accepts_hyphen_separated_mac(): void
    {
        $this->login($this->approvedUser(), 'AA-BB-CC-DD-EE-FF')->assertStatus(200);
    }

    /** @test */
    public function login_accepts_plain_hex_mac(): void
    {
        $this->login($this->approvedUser(), 'AABBCCDDEEFF')->assertStatus(200);
    }

    /** @test */
    public function login_returns_401_for_wrong_password(): void
    {
        $user = $this->approvedUser();

        $this->postJson('/api/auth/login', [
            'email'       => $user->email,
            'password'    => 'wrong-password',
            'mac_address' => self::MAC,
        ])->assertStatus(401)->assertJson(['message' => 'Invalid email or password.']);
    }

    /** @test */
    public function login_returns_401_for_non_existent_user(): void
    {
        $this->postJson('/api/auth/login', [
            'email'       => 'nobody@example.com',
            'password'    => 'password',
            'mac_address' => self::MAC,
        ])->assertStatus(401)->assertJson(['message' => 'Invalid email or password.']);
    }

    /** @test */
    public function login_returns_403_for_unverified_email(): void
    {
        $user = User::factory()->unverified()->create([
            'status'   => 'approved',
            'password' => bcrypt('password'),
        ]);

        $this->login($user)->assertStatus(403)
            ->assertJson(['message' => 'Please verify your email address before signing in.']);
    }

    /** @test */
    public function login_returns_403_for_pending_account(): void
    {
        $this->login($this->approvedUser(['status' => 'pending']))
            ->assertStatus(403)
            ->assertJson(['message' => 'Your account is awaiting admin approval.']);
    }

    /** @test */
    public function login_returns_403_for_disabled_account(): void
    {
        $this->login($this->approvedUser(['status' => 'disabled']))->assertStatus(403);
    }

    /** @test */
    public function login_returns_403_for_deactivated_account(): void
    {
        $this->login($this->approvedUser(['status' => 'deactivated']))->assertStatus(403);
    }

    /** @test */
    public function login_returns_token_and_user_for_approved_user(): void
    {
        $user = $this->approvedUser();

        $this->login($user)
            ->assertStatus(200)
            ->assertJsonStructure([
                'token_type',
                'access_token',
                'expires_in',
                'user' => ['id', 'name', 'email'],
            ])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', $user->email);
    }

    /** @test */
    public function login_stores_a_mac_binding(): void
    {
        $user = $this->approvedUser();

        $this->login($user)->assertStatus(200);

        $this->assertDatabaseHas('token_mac_bindings', [
            'mac_address' => TokenMacBinding::normaliseMac(self::MAC),
        ]);
    }

    /** @test */
    public function login_token_can_be_used_to_access_protected_route(): void
    {
        $user = $this->approvedUser();

        $token = $this->login($user)->json('access_token');

        $this->withHeader('Authorization', 'Bearer ' . $token)
             ->withHeader('X-Mac-Address', self::MAC)
             ->getJson('/api/auth/me')
             ->assertStatus(200)
             ->assertJsonPath('email', $user->email);
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
        $token = $this->issueToken($this->approvedUser(), self::MAC);

        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->getJson('/api/auth/me')
             ->assertStatus(422)
             ->assertJsonPath('code', 'mac_missing');
    }

    /** @test */
    public function me_returns_401_for_mac_mismatch(): void
    {
        $token = $this->issueToken($this->approvedUser(), self::MAC);

        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->withHeader('X-Mac-Address', self::MAC2)
             ->getJson('/api/auth/me')
             ->assertStatus(401)
             ->assertJsonPath('code', 'mac_mismatch');
    }

    /** @test */
    public function me_returns_401_when_no_binding_exists(): void
    {
        $token = $this->issueToken($this->approvedUser()); // no MAC bound

        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->withHeader('X-Mac-Address', self::MAC)
             ->getJson('/api/auth/me')
             ->assertStatus(401)
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

        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->withHeader('X-Mac-Address', self::MAC)
             ->getJson('/api/auth/me')
             ->assertStatus(200)
             ->assertJsonStructure(['id', 'name', 'email', 'organization', 'designation', 'status'])
             ->assertJsonPath('email',        $user->email)
             ->assertJsonPath('organization', 'Emergent Consulting')
             ->assertJsonPath('designation',  'Analyst')
             ->assertJsonPath('status',       'approved');
    }

    /** @test */
    public function me_revokes_binding_on_mac_mismatch(): void
    {
        $token = $this->issueToken($this->approvedUser(), self::MAC);

        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->withHeader('X-Mac-Address', self::MAC2)
             ->getJson('/api/auth/me')
             ->assertStatus(401);

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
        $token = $this->issueToken($this->approvedUser(), self::MAC);

        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->withHeader('X-Mac-Address', self::MAC)
             ->postJson('/api/auth/logout')
             ->assertStatus(200)
             ->assertJson(['message' => 'Logged out successfully.']);

        $this->assertDatabaseMissing('token_mac_bindings', ['token_id' => $token['id']]);
    }

    /** @test */
    public function logout_revokes_the_access_token(): void
    {
        $token = $this->issueToken($this->approvedUser(), self::MAC);

        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->withHeader('X-Mac-Address', self::MAC)
             ->postJson('/api/auth/logout')
             ->assertStatus(200);

        // Same token can no longer be used
        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->withHeader('X-Mac-Address', self::MAC)
             ->getJson('/api/auth/me')
             ->assertStatus(401);
    }

    // ══════════════════════════════════════════════════════════════
    //  POST /api/auth/refresh  (bearer + MAC; rotates the token)
    // ══════════════════════════════════════════════════════════════

    /** @test */
    public function refresh_returns_401_without_token(): void
    {
        $this->postJson('/api/auth/refresh')->assertStatus(401);
    }

    /** @test */
    public function refresh_returns_422_when_mac_header_is_missing(): void
    {
        $token = $this->issueToken($this->approvedUser(), self::MAC);

        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->postJson('/api/auth/refresh')
             ->assertStatus(422)
             ->assertJsonPath('code', 'mac_missing');
    }

    /** @test */
    public function refresh_returns_a_new_token_bound_to_the_same_mac(): void
    {
        $token = $this->issueToken($this->approvedUser(), self::MAC);

        $res = $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
                    ->withHeader('X-Mac-Address', self::MAC)
                    ->postJson('/api/auth/refresh');

        $res->assertStatus(200)
            ->assertJsonStructure(['token_type', 'access_token', 'expires_in', 'user']);

        // The new token is a different string from the old one
        $this->assertNotSame($token['jwt'], $res->json('access_token'));

        // The new token is bound to the same MAC
        $this->assertDatabaseHas('token_mac_bindings', [
            'mac_address' => TokenMacBinding::normaliseMac(self::MAC),
        ]);
    }

    /** @test */
    public function refresh_revokes_the_old_token(): void
    {
        $token = $this->issueToken($this->approvedUser(), self::MAC);

        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->withHeader('X-Mac-Address', self::MAC)
             ->postJson('/api/auth/refresh')
             ->assertStatus(200);

        // Old binding removed and old token unusable
        $this->assertDatabaseMissing('token_mac_bindings', ['token_id' => $token['id']]);

        $this->withHeader('Authorization', 'Bearer ' . $token['jwt'])
             ->withHeader('X-Mac-Address', self::MAC)
             ->getJson('/api/auth/me')
             ->assertStatus(401);
    }

    // ══════════════════════════════════════════════════════════════
    //  MAC normalisation
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
}
