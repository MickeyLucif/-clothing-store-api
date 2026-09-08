<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Admin;

final class AdminResource
{
    /** @param list<string> $roles */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly array $roles,
    ) {}

    public static function make(Admin $admin): self
    {
        return new self(
            id: $admin->id,
            name: $admin->name,
            email: $admin->email,
            roles: $admin->roles->pluck('name')->all(),
        );
    }
}
