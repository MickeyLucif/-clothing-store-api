<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Auth;

use App\Constants\Auth\AuthErrorCode;
use App\Constants\Http\HttpErrorCode;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_receives_a_token_with_valid_credentials(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->postJson(route('admin.auth.login'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['accessToken', 'tokenType', 'expiresIn'])
            ->assertJsonPath('tokenType', 'bearer');
    }

    public function test_admin_login_fails_with_wrong_password(): void
    {
        $admin = Admin::factory()->create();

        $this->postJson(route('admin.auth.login'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('domainError', AuthErrorCode::INVALID_CREDENTIALS);
    }

    public function test_admin_login_is_throttled_after_five_failed_attempts(): void
    {
        $admin = Admin::factory()->create();
        $credentials = ['email' => $admin->email, 'password' => 'wrong-password'];

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson(route('admin.auth.login'), $credentials)->assertUnauthorized();
        }

        $this->postJson(route('admin.auth.login'), $credentials)
            ->assertStatus(429)
            ->assertJsonPath('domainError', AuthErrorCode::TOO_MANY_LOGIN_ATTEMPTS);

        RateLimiter::clear('admin|'.mb_strtolower($admin->email).'|127.0.0.1');
    }

    public function test_admin_can_read_its_own_account(): void
    {
        $admin = Admin::factory()->create();

        $this->withToken($this->adminToken($admin))
            ->getJson(route('admin.auth.admin'))
            ->assertOk()
            ->assertJsonPath('email', $admin->email)
            ->assertJsonPath('roles', []);
    }

    public function test_admin_can_logout(): void
    {
        $this->withToken($this->adminToken())
            ->postJson(route('admin.auth.logout'))
            ->assertOk()
            ->assertJsonPath('message', 'Successfully logged out.');
    }

    public function test_refreshing_issues_a_working_token_and_retires_the_old_one(): void
    {
        $token = $this->adminToken();

        $refreshed = (string) $this->withToken($token)
            ->postJson(route('admin.auth.refresh'))
            ->assertOk()
            ->assertJsonStructure(['accessToken', 'tokenType', 'expiresIn'])
            ->json('accessToken');
        $this->forgetGuards();

        $this->assertNotSame($token, $refreshed);

        $this->withToken($refreshed)
            ->getJson(route('admin.auth.admin'))
            ->assertOk();
        $this->forgetGuards();

        $this->withToken($token)
            ->getJson(route('admin.auth.admin'))
            ->assertUnauthorized();
    }

    public function test_an_expired_token_can_still_be_refreshed(): void
    {
        $token = $this->adminToken();
        $this->travel(200)->minutes();

        $this->withToken($token)
            ->getJson(route('admin.auth.admin'))
            ->assertUnauthorized();
        $this->forgetGuards();

        $refreshed = (string) $this->withToken($token)
            ->postJson(route('admin.auth.refresh'))
            ->assertOk()
            ->json('accessToken');
        $this->forgetGuards();

        $this->withToken($refreshed)
            ->getJson(route('admin.auth.admin'))
            ->assertOk();
    }

    public function test_a_token_past_the_refresh_window_cannot_be_refreshed(): void
    {
        $token = $this->adminToken();
        $this->travel((int) config('jwt.refresh_ttl') + 60)->minutes();

        $this->withToken($token)
            ->postJson(route('admin.auth.refresh'))
            ->assertUnauthorized()
            ->assertJsonPath('domainError', HttpErrorCode::TOKEN_EXPIRED);
    }

    public function test_refreshing_without_a_token_is_rejected(): void
    {
        $this->postJson(route('admin.auth.refresh'))
            ->assertUnauthorized()
            ->assertJsonPath('domainError', HttpErrorCode::TOKEN_INVALID);
    }

    public function test_a_refreshed_customer_token_is_still_rejected_by_the_admin_guard(): void
    {
        Admin::factory()->create();
        $customerToken = $this->customerToken();

        $refreshed = (string) $this->withToken($customerToken)
            ->postJson(route('customer.auth.refresh'))
            ->assertOk()
            ->json('accessToken');
        $this->forgetGuards();

        $this->withToken($refreshed)
            ->getJson(route('admin.auth.admin'))
            ->assertUnauthorized();
    }

    public function test_admin_endpoints_reject_a_customer_token(): void
    {
        $this->withToken($this->customerToken())
            ->getJson(route('admin.auth.admin'))
            ->assertUnauthorized();
    }

    public function test_the_customer_login_cannot_lock_an_admin_out(): void
    {
        $admin = Admin::factory()->create();

        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->postJson(route('customer.auth.login'), [
                'email' => $admin->email,
                'password' => 'wrong-password',
            ]);
        }

        $this->postJson(route('admin.auth.login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertOk();
    }
}
