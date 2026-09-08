<?php

declare(strict_types=1);

namespace App\Actions\Admin\Auth;

use App\DTO\Admin\Auth\LoginDataDto;
use App\Enum\Guard;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\TooManyLoginAttemptsException;
use App\Services\Auth\AuthService;
use App\Services\Auth\LoginThrottleService;

final readonly class LoginAction
{
    private const GUARD = Guard::ADMIN;

    public function __construct(
        private AuthService $authService,
        private LoginThrottleService $loginThrottleService,
    ) {}

    /**
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

        return $token;
    }
}
