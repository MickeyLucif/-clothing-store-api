<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Admins;

use App\Actions\Admin\Admins\CreateAdminAction;
use App\DTO\Admin\Admins\CreateAdminDataDto;
use App\Enum\Guard;
use App\Enum\Role as RoleEnum;
use App\Exceptions\Authorization\NoPermissionsAvailableException;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws \Throwable
     */
    public function test_a_created_admin_gets_the_admin_role_with_every_admin_permission(): void
    {
        Permission::findOrCreate('roles.view', Guard::ADMIN->value);
        Permission::findOrCreate('roles.create', Guard::ADMIN->value);
        Permission::findOrCreate('orders.view', Guard::API->value);

        $admin = app(CreateAdminAction::class)(CreateAdminDataDto::from([
            'name' => 'Root',
            'email' => 'root@example.test',
            'password' => 'a-very-long-password',
        ]));

        $this->assertTrue($admin->hasRole(RoleEnum::ADMIN->value));
        $this->assertTrue($admin->hasPermissionTo('roles.create'));
        $this->assertFalse($admin->hasPermissionTo('orders.view'), 'api-guard permissions must not leak in');
        $this->assertDatabaseHas('roles', [
            'name' => RoleEnum::ADMIN->value,
            'guard_name' => Guard::ADMIN->value,
        ]);
    }

    public function test_an_empty_permission_table_is_refused_instead_of_stripping_the_role(): void
    {

        $permission = Permission::findOrCreate('roles.view', Guard::ADMIN->value);
        $role = Role::findOrCreate(RoleEnum::ADMIN->value, Guard::ADMIN->value);
        $role->syncPermissions(Permission::allForGuard(Guard::ADMIN->value));
        $permission->update(['guard_name' => Guard::API->value]);

        $this->expectException(NoPermissionsAvailableException::class);

        try {
            app(CreateAdminAction::class)(CreateAdminDataDto::from([
                'name' => 'Root',
                'email' => 'root@example.test',
                'password' => 'a-very-long-password',
            ]));
        } finally {
            $this->assertSame(
                1,
                $role->fresh()->permissions()->count(),
                'наявні права ролі мали залишитись недоторканими',
            );
            $this->assertDatabaseMissing('admins', ['email' => 'root@example.test']);
        }
    }
}
