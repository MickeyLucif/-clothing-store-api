<?php

declare(strict_types=1);

namespace App\Actions\Customer\Auth;

use App\DTO\Customer\Auth\LoginDataDto;
use App\Enum\Guard;
use App\Exceptions\Auth\AuthenticatedAccountUnavailableException;
use App\Exceptions\Auth\EmailNotVerifiedException;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\TooManyLoginAttemptsException;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Auth\LoginThrottleService;

final readonly class LoginAction
{
    private const GUARD = Guard::API;

    public function __construct(
        private AuthService $authService,
        private LoginThrottleService $loginThrottleService,
    ) {}

    /**
     * @throws AuthenticatedAccountUnavailableException
     * @throws EmailNotVerifiedException
     * @throws InvalidCredentialsException
     * @throws TooManyLoginAttemptsException
     */
    public function __invoke(LoginDataDto $data, string $throttleKey): string
    {
        $this->loginThrottleService->ensureIsNotRateLimited($throttleKey);

        $token = $this->authService->attempt(self::GUARD, $data->credentials());

        if ($token === null) {
            $this->loginThrottleService->hit($throttleKey);

            throw new InvalidCredentialsException;
        }

        $this->loginThrottleService->clear($throttleKey);
        $this->ensureEmailIsVerified();

        return $token;
    }

    /**
     * @throws AuthenticatedAccountUnavailableException
     * @throws EmailNotVerifiedException
     */
    private function ensureEmailIsVerified(): void
    {
        $user = $this->authService->user(self::GUARD);

        if (! $user instanceof User) {
            $this->authService->logout(self::GUARD);

            throw new AuthenticatedAccountUnavailableException(self::GUARD);
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $this->authService->logout(self::GUARD);

        throw new EmailNotVerifiedException;
    }
}
