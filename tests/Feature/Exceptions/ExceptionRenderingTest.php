<?php

declare(strict_types=1);

namespace Tests\Feature\Exceptions;

use App\Constants\Auth\AuthErrorCode;
use App\Constants\Http\HttpErrorCode;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExceptionRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_gets_json_even_when_asking_for_html(): void
    {
        $this->get(route('admin.roles.index'), ['Accept' => 'text/html'])
            ->assertUnauthorized()
            ->assertJsonPath('code', 401)
            ->assertJsonPath('domainError', HttpErrorCode::UNAUTHENTICATED)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_validation_failure_returns_json_even_when_asking_for_html(): void
    {
        $this->withToken($this->adminToken($this->privilegedAdmin()))
            ->post(route('admin.roles.store'), [], ['Accept' => 'text/html'])
            ->assertUnprocessable()
            ->assertJsonPath('code', 422)
            ->assertJsonPath('domainError', HttpErrorCode::VALIDATION_FAILED)
            ->assertJsonStructure(['code', 'message', 'domainError', 'errors' => ['name', 'guard']]);
    }

    public function test_an_unknown_api_route_returns_the_same_envelope(): void
    {
        $this->getJson('/api/admin/nope')
            ->assertNotFound()
            ->assertJsonPath('code', 404)
            ->assertJsonPath('domainError', HttpErrorCode::NOT_FOUND);
    }

    public function test_a_wrong_method_returns_the_same_envelope_and_keeps_the_allow_header(): void
    {
        $response = $this->putJson(route('admin.roles.index'))
            ->assertStatus(405)
            ->assertJsonPath('code', 405)
            ->assertJsonPath('domainError', HttpErrorCode::METHOD_NOT_ALLOWED);

        $this->assertNotEmpty($response->headers->get('Allow'));
    }

    public function test_route_throttling_returns_the_same_envelope_with_retry_after(): void
    {
        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->postJson(route('verification.send'), ['email' => 'ghost@example.test'])->assertOk();
        }

        $response = $this->postJson(route('verification.send'), ['email' => 'ghost@example.test'])
            ->assertStatus(429)
            ->assertJsonPath('code', 429)
            ->assertJsonPath('domainError', HttpErrorCode::TOO_MANY_REQUESTS);

        $this->assertNotNull($response->headers->get('Retry-After'));
    }

    public function test_a_malformed_token_returns_the_same_envelope(): void
    {
        $this->withToken('not-a-jwt')
            ->getJson(route('admin.auth.admin'))
            ->assertUnauthorized()
            ->assertJsonPath('code', 401)
            ->assertJsonPath('domainError', HttpErrorCode::UNAUTHENTICATED);
    }

    public function test_an_expired_token_returns_the_same_envelope(): void
    {
        $token = $this->adminToken();
        $this->travel(200)->minutes();

        $this->withToken($token)
            ->getJson(route('admin.auth.admin'))
            ->assertUnauthorized()
            ->assertJsonPath('code', 401)
            ->assertJsonPath('domainError', HttpErrorCode::UNAUTHENTICATED);
    }

    public function test_domain_exceptions_keep_rendering_themselves(): void
    {
        $admin = Admin::factory()->create();

        $this->postJson(route('admin.auth.login'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('code', 401)
            ->assertJsonPath('domainError', AuthErrorCode::INVALID_CREDENTIALS)
            ->assertJsonMissingPath('errors');
    }
}
