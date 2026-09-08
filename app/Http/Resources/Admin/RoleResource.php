<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

final class RoleResource
{
    /** @param list<PermissionResource> $permissions */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $guard,
        public readonly int $permissionsCount,
        public readonly array $permissions,
    ) {}

    public static function make(Role $role): self
    {
        return new self(
            id: (int) $role->getKey(),
            name: (string) $role->name,
            guard: (string) $role->guard_name,
            permissionsCount: (int) ($role->permissions_count ?? $role->permissions->count()),
            permissions: $role->permissions
                ->map(fn (mixed $permission): PermissionResource => PermissionResource::make($permission))
                ->all(),
        );
    }

    /**
     * @param  EloquentCollection<int, Role>  $roles
     * @return list<self>
     */
    public static function collection(EloquentCollection $roles): array
    {
        return $roles
            ->map(fn (Role $role): self => self::make($role))
            ->all();
    }
}
