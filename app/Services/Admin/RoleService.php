<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Permission;
use Spatie\Permission\Contracts\Role;

class RoleService
{
    public function syncAllAvailablePermissions(Role $role): void
    {
        $role->syncPermissions(Permission::allForGuard($role->guard_name));
    }
}
