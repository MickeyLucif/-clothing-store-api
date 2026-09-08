<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Customer\Auth;

use App\Actions\Customer\Auth\LoginAction;
use App\DTO\Customer\Auth\LoginDataDto;
use App\Enum\Guard;
use App\Exceptions\Auth\AuthenticatedAccountUnavailableException;
use App\Exceptions\Auth\EmailNotVerifiedException;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Auth\LoginThrottleService;
use Mockery;
use Tests\TestCase;

class LoginActionTest extends TestCase
{
    public function test_an_unresolvable_account_is_not_reported_as_an_unverified_email(): void
    {
        $authService = Mockery::mock(AuthService::class);
        $authService->shouldReceive('attempt')->once()->andReturn('a-token');
        $authService->shouldReceive('user')->once()->with(Guard::API)->andReturnNull();
        $authService->shouldReceive('logout')->once()->with(Guard::API);

        $this->expectException(AuthenticatedAccountUnavailableException::class);

        $this->action($authService)($this->credentials(), 'throttle-key');
    }

    public function test_an_unverified_account_still_reports_the_email_error(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('hasVerifiedEmail')->andReturn(false);

        $authService = Mockery::mock(AuthService::class);
        $authService->shouldReceive('attempt')->once()->andReturn('a-token');
        $authService->shouldReceive('user')->once()->with(Guard::API)->andReturn($user);
        $authService->shouldReceive('logout')->once()->with(Guard::API);

        $this->expectException(EmailNotVerifiedException::class);

        $this->action($authService)($this->credentials(), 'throttle-key');
    }

    private function action(AuthService $authService): LoginAction
    {
        $throttle = Mockery::mock(LoginThrottleService::class);
        $throttle->shouldReceive('ensureIsNotRateLimited')->once();
        $throttle->shouldReceive('clear')->once();

        return new LoginAction($authService, $throttle);
    }

    private function credentials(): LoginDataDto
    {
        return LoginDataDto::from([
            'email' => 'katya@example.test',
            'password' => 'password',
        ]);
    }
}
