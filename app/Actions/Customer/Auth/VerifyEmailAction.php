<?php

declare(strict_types=1);

namespace App\Actions\Customer\Auth;

use App\Exceptions\Auth\EmailVerificationFailedException;
use App\Exceptions\Auth\InvalidEmailVerificationException;
use App\Exceptions\Auth\UserNotFoundException;
use App\Services\Customer\UserService;
use Illuminate\Auth\Events\Verified;

final readonly class VerifyEmailAction
{
    public function __construct(
        private UserService $userService,
    ) {}

    /**
     * @throws EmailVerificationFailedException
     * @throws InvalidEmailVerificationException
     * @throws UserNotFoundException
     */
    public function __invoke(int $userId, string $hash): bool
    {
        $user = $this->userService->findById($userId);

        if ($user === null) {
            throw new UserNotFoundException;
        }

        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            throw new InvalidEmailVerificationException;
        }

        if ($user->hasVerifiedEmail()) {
            return false;
        }

        if (! $user->markEmailAsVerified()) {
            throw new EmailVerificationFailedException;
        }

        event(new Verified($user));

        return true;
    }
}
