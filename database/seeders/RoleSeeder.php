<?php

namespace Database\Seeders;

use App\Enum\RoleName;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (RoleName::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value]);
        }
    }
}
