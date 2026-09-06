<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enum\Guard;
use App\Enum\Permission as PermissionEnum;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('permissions')->truncate();
        foreach (PermissionEnum::cases() as $permission) {
            Permission::findOrCreate($permission->value, Guard::ADMIN->value);
        }
    }
}
