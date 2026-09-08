<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\Guard;
use App\Exceptions\Authorization\NoPermissionsAvailableException;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    /** @return Collection<int, Role> */
    public function allForGuard(?Guard $guard): Collection
    {
        return $guard === null
            ? Role::allForEveryGuard()
            : Role::allForGuard($guard->value);
    }

    public function create(string $name, Guard $guard): Role
    {
        $role = new Role;
        $role->name = $name;
        $role->guard_name = $guard->value;
        $role->save();

        return $role;
    }

    public function findOrCreate(string $name, Guard $guard): Role
    {
        return Role::findOrCreate($name, $guard->value);
    }

    /**
     * @throws NoPermissionsAvailableException
     */
    public function syncAllAvailablePermissions(Role $role): void
    {
        $permissions = Permission::allForGuard($role->guard_name);

        if ($permissions->isEmpty()) {
            throw new NoPermissionsAvailableException($role->guard_name);
        }

        $role->syncPermissions($permissions);
    }
}
