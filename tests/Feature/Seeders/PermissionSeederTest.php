<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Enum\Guard;
use App\Enum\Permission as PermissionEnum;
use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_running_it_twice_leaves_exactly_the_enum(): void
    {
        $this->seedPermissions();
        $this->seedPermissions();

        $this->assertSame(count(PermissionEnum::cases()), Permission::query()->count());
        $this->assertEqualsCanonicalizing(
            array_map(static fn (PermissionEnum $case): string => $case->value, PermissionEnum::cases()),
            Permission::query()->where('guard_name', Guard::ADMIN->value)->pluck('name')->all(),
        );
    }

    public function test_a_permission_missing_from_the_enum_is_removed(): void
    {
        $this->seedPermissions();
        Permission::findOrCreate('legacy.view', Guard::ADMIN->value);

        $this->seedPermissions();

        $this->assertDatabaseMissing('permissions', [
            'name' => 'legacy.view',
            'guard_name' => Guard::ADMIN->value,
        ]);
        $this->assertSame(count(PermissionEnum::cases()), Permission::query()->count());
    }

    public function test_a_permission_deleted_by_hand_is_recreated(): void
    {
        $this->seedPermissions();
        Permission::query()->where('name', PermissionEnum::VIEW_ROLES->value)->delete();

        $this->seedPermissions();

        $this->assertDatabaseHas('permissions', [
            'name' => PermissionEnum::VIEW_ROLES->value,
            'guard_name' => Guard::ADMIN->value,
        ]);
    }

    public function test_other_guards_are_left_alone(): void
    {
        Permission::findOrCreate('orders.view', Guard::API->value);

        $this->seedPermissions();

        $this->assertDatabaseHas('permissions', [
            'name' => 'orders.view',
            'guard_name' => Guard::API->value,
        ]);
    }

    public function test_removing_a_permission_detaches_it_from_roles_and_keeps_the_rest(): void
    {
        $this->seedPermissions();
        $legacy = Permission::findOrCreate('legacy.view', Guard::ADMIN->value);
        $role = Role::findOrCreate('Manager', Guard::ADMIN->value);
        $role->syncPermissions(Permission::allForGuard(Guard::ADMIN->value));
        $before = $role->fresh()->permissions()->count();

        $this->seedPermissions();

        $this->assertDatabaseMissing('role_has_permissions', ['permission_id' => $legacy->id]);
        $this->assertSame($before - 1, $role->fresh()->permissions()->count());
    }

    private function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
    }
}
