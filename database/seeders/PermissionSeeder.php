<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enum\Guard;
use App\Enum\Permission as PermissionEnum;
use App\Services\PermissionService;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    private const GUARD = Guard::ADMIN;

    public function __construct(
        private readonly PermissionService $permissionService,
    ) {}

    public function run(): void
    {
        $this->permissionService->syncForGuard($this->names(), self::GUARD);
    }

    /** @return list<string> */
    private function names(): array
    {
        return array_map(
            static fn (PermissionEnum $permission): string => $permission->value,
            PermissionEnum::cases(),
        );
    }
}
