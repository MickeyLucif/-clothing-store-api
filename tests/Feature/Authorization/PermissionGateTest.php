<?php

declare(strict_types=1);

namespace Tests\Feature\Authorization;

use App\Enum\Guard;
use App\Enum\Permission as PermissionEnum;
use App\Enum\Role as RoleEnum;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PermissionGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_granted_permission_is_allowed_without_any_defined_ability(): void
    {
        $admin = $this->adminWith(PermissionEnum::VIEW_ROLES);

        $this->assertTrue(Gate::forUser($admin)->allows(PermissionEnum::VIEW_ROLES->value));
    }

    public function test_a_missing_permission_is_denied_even_when_an_ability_grants_it(): void
    {
        $admin = $this->adminWith(PermissionEnum::VIEW_ROLES);
        Gate::define(PermissionEnum::CREATE_ROLES->value, static fn (): bool => true);

        $this->assertFalse(Gate::forUser($admin)->allows(PermissionEnum::CREATE_ROLES->value));
    }

    public function test_an_ability_that_is_not_a_permission_still_reaches_its_own_check(): void
    {
        $admin = $this->adminWith(PermissionEnum::VIEW_ROLES);
        Gate::define('update-own-order', static fn (): bool => true);

        $this->assertTrue(Gate::forUser($admin)->allows('update-own-order'));
    }

    private function adminWith(PermissionEnum $permission): Admin
    {
        $admin = Admin::factory()->create();
        Permission::findOrCreate($permission->value, Guard::ADMIN->value);

        $role = Role::findOrCreate(RoleEnum::ADMIN->value, Guard::ADMIN->value);
        $role->syncPermissions(Permission::allForGuard(Guard::ADMIN->value));
        $admin->assignRole($role);

        return $admin;
    }
}
