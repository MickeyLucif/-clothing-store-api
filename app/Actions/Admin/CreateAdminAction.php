<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Enum\Guard;
use App\Enum\Role as RoleEnum;
use App\Models\Admin;
use App\Models\Role;
use App\Services\Admin\AdminService;
use App\Services\Admin\RoleService;
use Illuminate\Support\Facades\DB;

readonly class CreateAdminAction
{
    public function __construct(
        private AdminService $adminService,
        private RoleService $roleService,
    ) {}

    /**
     * @throws \Throwable
     */
    public function __invoke(string $name, string $email, string $password): Admin
    {
        return DB::transaction(function () use ($name, $email, $password): Admin {
            $admin = $this->adminService->create($name, $email, $password);
            $role = Role::findOrCreate(RoleEnum::ADMIN->value, Guard::ADMIN->value);

            $this->roleService->syncAllAvailablePermissions($role);
            $this->adminService->assignRole($admin, $role);

            return $admin;
        });
    }
}
