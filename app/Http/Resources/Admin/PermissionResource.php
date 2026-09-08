<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Permission;

final class PermissionResource
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $guard,
    ) {}

    public static function make(Permission $permission): self
    {
        return new self(
            id: (int) $permission->getKey(),
            name: (string) $permission->name,
            guard: (string) $permission->guard_name,
        );
    }
}
