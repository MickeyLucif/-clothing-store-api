<?php

declare(strict_types=1);

namespace Tests;

use App\Enum\Guard;
use App\Enum\Role as RoleEnum;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function adminToken(?Admin $admin = null): string
    {
        $admin ??= Admin::factory()->create();

        $token = (string) $this->postJson(route('admin.auth.login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertOk()->json('accessToken');

        $this->forgetGuards();

        return $token;
    }

    protected function customerToken(?User $user = null): string
    {
        $user ??= User::factory()->create();

        $token = (string) $this->postJson(route('customer.auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk()->json('accessToken');

        $this->forgetGuards();

        return $token;
    }

    protected function privilegedAdmin(): Admin
    {
        $this->seed(PermissionSeeder::class);

        $role = Role::findOrCreate(RoleEnum::ADMIN->value, Guard::ADMIN->value);
        $role->syncPermissions(Permission::allForGuard(Guard::ADMIN->value));

        return Admin::factory()->create()->assignRole($role);
    }

    protected function forgetGuards(): void
    {
        $this->app['auth']->forgetGuards();
        $this->app['tymon.jwt']->unsetToken();
        $this->app['tymon.jwt.auth']->unsetToken();
    }
}
