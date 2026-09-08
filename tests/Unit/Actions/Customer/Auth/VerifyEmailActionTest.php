<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Customer\Auth;

use App\Actions\Customer\Auth\VerifyEmailAction;
use App\Exceptions\Auth\EmailVerificationFailedException;
use App\Models\User;
use App\Services\Customer\UserService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class VerifyEmailActionTest extends TestCase
{
    public function test_a_failed_save_is_reported_instead_of_returning_success(): void
    {
        Event::fake([Verified::class]);

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getEmailForVerification')->andReturn('katya@example.test');
        $user->shouldReceive('hasVerifiedEmail')->andReturn(false);
        $user->shouldReceive('markEmailAsVerified')->once()->andReturn(false);

        $userService = Mockery::mock(UserService::class);
        $userService->shouldReceive('findById')->with(1)->andReturn($user);

        $action = new VerifyEmailAction($userService);

        $this->expectException(EmailVerificationFailedException::class);

        try {
            $action(1, sha1('katya@example.test'));
        } finally {
            Event::assertNotDispatched(Verified::class);
        }
    }

    public function test_a_successful_save_dispatches_the_event(): void
    {
        Event::fake([Verified::class]);

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getEmailForVerification')->andReturn('katya@example.test');
        $user->shouldReceive('hasVerifiedEmail')->andReturn(false);
        $user->shouldReceive('markEmailAsVerified')->once()->andReturn(true);

        $userService = Mockery::mock(UserService::class);
        $userService->shouldReceive('findById')->with(1)->andReturn($user);

        $this->assertTrue((new VerifyEmailAction($userService))(1, sha1('katya@example.test')));
        Event::assertDispatched(Verified::class);
    }
}
