<?php

declare(strict_types=1);

namespace App\Actions\Admin\Auth;

use App\Enum\Guard;
use App\Exceptions\Auth\UnauthenticatedException;
use App\Models\Admin;
use App\Services\Auth\AuthService;

final readonly class GetAuthenticatedAccountAction
{
    private const GUARD = Guard::ADMIN;

    public function __construct(
        private AuthService $authService,
    ) {}

    /**
     * @throws UnauthenticatedException
     */
    public function __invoke(): Admin
    {
        $admin = $this->authService->user(self::GUARD);

        if (! $admin instanceof Admin) {
            throw new UnauthenticatedException;
        }

        return $admin;
    }
}
