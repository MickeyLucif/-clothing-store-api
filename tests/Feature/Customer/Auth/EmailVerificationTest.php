<?php

declare(strict_types=1);

namespace Tests\Feature\Customer\Auth;

use App\Constants\Auth\AuthErrorCode;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_signed_link_verifies_the_email(): void
    {
        Event::fake([Verified::class]);
        $user = User::factory()->unverified()->create();

        $this->getJson($this->verificationUrl($user, sha1($user->email)))
            ->assertOk()
            ->assertJsonPath('message', 'Email successfully verified');

        $this->assertTrue($user->refresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    public function test_a_tampered_hash_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $this->getJson($this->verificationUrl($user, sha1('someone-else@example.test')))
            ->assertForbidden()
            ->assertJsonPath('domainError', AuthErrorCode::INVALID_EMAIL_VERIFICATION_LINK);

        $this->assertFalse($user->refresh()->hasVerifiedEmail());
    }

    public function test_an_unknown_user_is_reported_as_not_found(): void
    {
        $this->getJson($this->verificationUrlFor(999_999, sha1('ghost@example.test')))
            ->assertNotFound()
            ->assertJsonPath('domainError', AuthErrorCode::EMAIL_VERIFICATION_USER_NOT_FOUND);
    }

    public function test_resending_never_reveals_whether_the_account_exists(): void
    {
        $this->postJson(route('verification.send'), ['email' => 'ghost@example.test'])
            ->assertOk()
            ->assertJsonPath(
                'message',
                'If the account exists and is not verified, a verification link has been sent',
            );
    }

    private function verificationUrl(User $user, string $hash): string
    {
        return $this->verificationUrlFor($user->id, $hash);
    }

    private function verificationUrlFor(int $id, string $hash): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $id, 'hash' => $hash],
        );
    }
}
