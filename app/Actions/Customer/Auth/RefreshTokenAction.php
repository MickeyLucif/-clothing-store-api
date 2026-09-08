<?php

declare(strict_types=1);

namespace App\Actions\Customer\Auth;

use App\Enum\Guard;
use App\Services\Auth\AuthService;

final readonly class RefreshTokenAction
{
    private const GUARD = Guard::API;

    public function __construct(
        private AuthService $authService,
    ) {}

    public function __invoke(): string
    {
        return $this->authService->refresh(self::GUARD);
    }
}
