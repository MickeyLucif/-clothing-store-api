<?php

declare(strict_types=1);

namespace App\Actions\Admin\Roles;

use App\Enum\Guard;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetRolesAction
{
    public function __construct(
        private RoleService $roleService,
    ) {}

    /**
     * @return Collection<int, Role>
     */
    public function __invoke(?Guard $guard): Collection
    {
        return $this->roleService->allForGuard($guard);
    }
}
