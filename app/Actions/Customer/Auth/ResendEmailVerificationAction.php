<?php

declare(strict_types=1);

namespace App\Actions\Customer\Auth;

use App\Services\Customer\UserService;

final readonly class ResendEmailVerificationAction
{
    public function __construct(
        private UserService $userService,
    ) {}

    public function __invoke(string $email): void
    {
        $user = $this->userService->findByEmail($email);

        if ($user === null || $user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }
}
