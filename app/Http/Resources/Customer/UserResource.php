<?php

declare(strict_types=1);

namespace App\Http\Resources\Customer;

use App\Models\User;

final class UserResource
{
    /** @param list<string> $roles */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly bool $emailVerified,
        public readonly array $roles,
    ) {}

    public static function make(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            emailVerified: $user->hasVerifiedEmail(),
            roles: $user->roles->pluck('name')->all(),
        );
    }
}
