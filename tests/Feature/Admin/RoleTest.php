<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Constants\Http\HttpErrorCode;
use App\Enum\Guard;
use App\Models\Role;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_are_listed_for_every_guard_by_default(): void
    {
        $admin = $this->privilegedAdmin();
        Role::findOrCreate('Manager', Guard::ADMIN->value);
        Role::findOrCreate('Customer', Guard::API->value);

        $this->withToken($this->adminToken($admin))
            ->getJson(route('admin.roles.index'))
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJsonStructure([['id', 'name', 'guard', 'permissionsCount', 'permissions']]);
    }

    public function test_roles_can_be_filtered_by_guard(): void
    {
        Role::findOrCreate('Manager', Guard::ADMIN->value);
        Role::findOrCreate('Customer', Guard::API->value);

        $this->withToken($this->adminToken($this->privilegedAdmin()))
            ->getJson(route('admin.roles.index', ['guard' => Guard::API->value]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'Customer');
    }

    public function test_a_role_can_be_created(): void
    {
        $this->withToken($this->adminToken($this->privilegedAdmin()))
            ->postJson(route('admin.roles.store'), [
                'name' => 'Support',
                'guard' => Guard::ADMIN->value,
            ])
            ->assertCreated()
            ->assertJsonPath('name', 'Support')
            ->assertJsonPath('guard', Guard::ADMIN->value);

        $this->assertDatabaseHas('roles', ['name' => 'Support', 'guard_name' => Guard::ADMIN->value]);
    }

    public function test_a_duplicate_role_name_within_the_same_guard_is_rejected(): void
    {
        Role::findOrCreate('Support', Guard::ADMIN->value);

        $this->withToken($this->adminToken($this->privilegedAdmin()))
            ->postJson(route('admin.roles.store'), [
                'name' => 'Support',
                'guard' => Guard::ADMIN->value,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_guests_cannot_list_roles(): void
    {
        $this->getJson(route('admin.roles.index'))->assertUnauthorized();
    }

    public function test_an_admin_without_the_permission_is_forbidden(): void
    {
        $this->seed(PermissionSeeder::class);
        $token = $this->adminToken();

        $this->withToken($token)
            ->getJson(route('admin.roles.index'))
            ->assertForbidden()
            ->assertJsonPath('domainError', HttpErrorCode::FORBIDDEN);

        $this->forgetGuards();

        $this->withToken($token)
            ->postJson(route('admin.roles.store'), ['name' => 'Support', 'guard' => Guard::ADMIN->value])
            ->assertForbidden()
            ->assertJsonPath('domainError', HttpErrorCode::FORBIDDEN);

        $this->assertDatabaseMissing('roles', ['name' => 'Support']);
    }
}
