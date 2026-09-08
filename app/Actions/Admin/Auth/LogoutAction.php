<?php

declare(strict_types=1);

namespace App\Actions\Admin\Auth;

use App\Enum\Guard;
use App\Services\Auth\AuthService;

final readonly class LogoutAction
{
    private const GUARD = Guard::ADMIN;

    public function __construct(
        private AuthService $authService,
    ) {}

    public function __invoke(): void
    {
        $this->authService->logout(self::GUARD);
    }
}
