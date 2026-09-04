<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTO\Auth\RegisterDataDto;
use App\Enum\RoleName;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class RegisterUserAction
{
    public function __invoke(RegisterDataDto $data): User
    {
        $role = Role::query()
            ->where('name', RoleName::USER->value)
            ->firstOrFail();

        $user = new User;
        $user->name = $data->name;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->role()->associate($role);
        $user->save();

        event(new Registered($user));

        return $user;
    }
}
