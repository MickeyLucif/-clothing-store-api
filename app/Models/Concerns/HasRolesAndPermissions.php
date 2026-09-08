<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enum\Guard;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use InvalidArgumentException;

trait HasRolesAndPermissions
{
    abstract public function authorizationGuard(): Guard;

    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            Role::class,
            'model',
            'model_has_roles',
            'model_id',
            'role_id',
        );
    }

    public function permissions(): MorphToMany
    {
        return $this->morphToMany(
            Permission::class,
            'model',
            'model_has_permissions',
            'model_id',
            'permission_id',
        );
    }

    public function assignRole(Role $role): static
    {
        $this->ensureGuardMatches($role->guard_name);
        $this->roles()->syncWithoutDetaching([$role->getKey()]);
        $this->unsetRelation('roles');

        return $this;
    }

    public function givePermissionTo(Permission $permission): static
    {
        $this->ensureGuardMatches($permission->guard_name);
        $this->permissions()->syncWithoutDetaching([$permission->getKey()]);
        $this->unsetRelation('permissions');

        return $this;
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()
            ->where('name', $roleName)
            ->where('guard_name', $this->authorizationGuard()->value)
            ->exists();
    }

    public function hasPermissionTo(string $permissionName): bool
    {
        $guardName = $this->authorizationGuard()->value;

        $hasDirectPermission = $this->permissions()
            ->where('name', $permissionName)
            ->where('guard_name', $guardName)
            ->exists();

        if ($hasDirectPermission) {
            return true;
        }

        return $this->roles()
            ->where('guard_name', $guardName)
            ->whereHas('permissions', static function ($query) use ($permissionName, $guardName): void {
                $query
                    ->where('permissions.name', $permissionName)
                    ->where('permissions.guard_name', $guardName);
            })
            ->exists();
    }

    private function ensureGuardMatches(string $guardName): void
    {
        if ($guardName !== $this->authorizationGuard()->value) {
            throw new InvalidArgumentException('Role or permission guard does not match the model guard.');
        }
    }
}
