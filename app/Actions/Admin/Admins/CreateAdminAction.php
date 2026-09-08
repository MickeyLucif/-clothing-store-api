<?php

declare(strict_types=1);

namespace App\Actions\Admin\Admins;

use App\DTO\Admin\Admins\CreateAdminDataDto;
use App\Enum\Guard;
use App\Enum\Role as RoleEnum;
use App\Models\Admin;
use App\Services\Admin\AdminService;
use App\Services\RoleService;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateAdminAction
{
    private const GUARD = Guard::ADMIN;

    public function __construct(
        private AdminService $adminService,
        private RoleService $roleService,
    ) {}

    /**
     * @throws Throwable
     */
    public function __invoke(CreateAdminDataDto $data): Admin
    {
        return DB::transaction(function () use ($data): Admin {
            $admin = $this->adminService->create($data->name, $data->email, $data->password);
            $role = $this->roleService->findOrCreate(RoleEnum::ADMIN->value, self::GUARD);

            $this->roleService->syncAllAvailablePermissions($role);
            $this->adminService->assignRole($admin, $role);

            return $admin;
        });
    }
}
