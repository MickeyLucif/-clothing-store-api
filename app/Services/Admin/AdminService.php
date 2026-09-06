<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Admin;
use Spatie\Permission\Contracts\Role;

class AdminService
{
    public function create(string $name, string $email, string $password): Admin
    {
        $admin = new Admin;
        $admin->name = $name;
        $admin->email = $email;
        $admin->password = $password;
        $admin->save();

        return $admin;
    }

    public function assignRole(Admin $admin, Role $role): void
    {
        $admin->assignRole($role);
    }
}
