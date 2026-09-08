<?php

declare(strict_types=1);

namespace App\Actions\Customer\Auth;

use App\Enum\Guard;
use App\Exceptions\Auth\UnauthenticatedException;
use App\Models\User;
use App\Services\Auth\AuthService;

final readonly class GetAuthenticatedAccountAction
{
    private const GUARD = Guard::API;

    public function __construct(
        private AuthService $authService,
    ) {}

    /**
     * @throws UnauthenticatedException
     */
    public function __invoke(): User
    {
        $user = $this->authService->user(self::GUARD);

        if (! $user instanceof User) {
            throw new UnauthenticatedException;
        }

        return $user;
    }
}
