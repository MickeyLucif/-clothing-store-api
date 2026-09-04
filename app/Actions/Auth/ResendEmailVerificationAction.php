<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;

class ResendEmailVerificationAction
{
    public function __invoke(string $email): void
    {
        $user = User::query()
            ->where('email', $email)
            ->first();

        if ($user === null) {
            return;
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }
}
