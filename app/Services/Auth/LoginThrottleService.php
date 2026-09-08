<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Exceptions\Auth\TooManyLoginAttemptsException;
use Illuminate\Support\Facades\RateLimiter;

class LoginThrottleService
{
    private const MAX_ATTEMPTS = 5;

    /**
     * @throws TooManyLoginAttemptsException
     */
    public function ensureIsNotRateLimited(string $throttleKey): void
    {
        if (! RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            return;
        }

        throw new TooManyLoginAttemptsException(RateLimiter::availableIn($throttleKey));
    }

    public function hit(string $throttleKey): void
    {
        RateLimiter::hit($throttleKey);
    }

    public function clear(string $throttleKey): void
    {
        RateLimiter::clear($throttleKey);
    }
}
