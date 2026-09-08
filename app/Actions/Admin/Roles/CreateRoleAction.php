<?php

declare(strict_types=1);

namespace App\Actions\Admin\Roles;

use App\DTO\Admin\Roles\CreateRoleDataDto;
use App\Models\Role;
use App\Services\RoleService;

final readonly class CreateRoleAction
{
    public function __construct(
        private RoleService $roleService,
    ) {}

    public function __invoke(CreateRoleDataDto $data): Role
    {
        return $this->roleService->create($data->name, $data->guard);
    }
}
