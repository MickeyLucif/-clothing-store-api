<?php

declare(strict_types=1);

namespace Tests\Feature\Customer\Auth;

use App\Constants\Auth\AuthErrorCode;
use App\Enum\Guard;
use App\Enum\Role as RoleEnum;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CustomerAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_a_customer_with_the_customer_role(): void
    {
        Event::fake([Registered::class]);

        $response = $this->postJson(route('customer.auth.register'), [
            'name' => 'Kateryna',
            'email' => 'kateryna@example.test',
            'password' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('email', 'kateryna@example.test')
            ->assertJsonPath('emailVerified', false)
            ->assertJsonPath('roles', [RoleEnum::CUSTOMER->value]);

        Event::assertDispatched(Registered::class);

        $user = User::query()->where('email', 'kateryna@example.test')->sole();
        $this->assertTrue($user->hasRole(RoleEnum::CUSTOMER->value));
        $this->assertSame(Guard::API, $user->authorizationGuard());
    }

    public function test_customer_receives_a_token_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('customer.auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonStructure(['accessToken', 'tokenType', 'expiresIn']);
    }

    public function test_customer_with_unverified_email_cannot_login(): void
    {
        $user = User::factory()->unverified()->create();

        $this->postJson(route('customer.auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertForbidden()
            ->assertJsonPath('domainError', AuthErrorCode::EMAIL_NOT_VERIFIED);
    }

    public function test_customer_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('customer.auth.login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('domainError', AuthErrorCode::INVALID_CREDENTIALS);
    }

    public function test_customer_can_read_its_own_account_and_logout(): void
    {
        $user = User::factory()->create();
        $token = $this->customerToken($user);

        $this->withToken($token)
            ->getJson(route('customer.auth.user'))
            ->assertOk()
            ->assertJsonPath('email', $user->email)
            ->assertJsonPath('emailVerified', true);

        $this->withToken($token)
            ->postJson(route('customer.auth.logout'))
            ->assertOk()
            ->assertJsonPath('message', 'Successfully logged out.');
    }

    public function test_customer_endpoints_reject_an_admin_token(): void
    {
        $this->withToken($this->adminToken())
            ->getJson(route('customer.auth.user'))
            ->assertUnauthorized();
    }
}
