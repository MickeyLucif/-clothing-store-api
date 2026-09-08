<?php

declare(strict_types=1);

namespace App\Services\Customer;

use App\Models\Role;
use App\Models\User;

class UserService
{
    public function create(string $name, string $email, string $password): User
    {
        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->save();

        return $user;
    }

    public function assignRole(User $user, Role $role): void
    {
        $user->assignRole($role);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()
            ->where('email', $email)
            ->first();
    }

    public function findById(int $id): ?User
    {
        return User::query()->find($id);
    }
}
