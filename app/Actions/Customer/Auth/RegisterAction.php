<?php

declare(strict_types=1);

namespace App\Actions\Customer\Auth;

use App\DTO\Customer\Auth\RegisterDataDto;
use App\Enum\Guard;
use App\Enum\Role as RoleEnum;
use App\Models\User;
use App\Services\Customer\UserService;
use App\Services\RoleService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class RegisterAction
{
    private const GUARD = Guard::API;

    public function __construct(
        private UserService $userService,
        private RoleService $roleService,
    ) {}

    /**
     * @throws Throwable
     */
    public function __invoke(RegisterDataDto $data): User
    {
        $user = DB::transaction(function () use ($data): User {
            $user = $this->userService->create($data->name, $data->email, $data->password);
            $role = $this->roleService->findOrCreate(RoleEnum::CUSTOMER->value, self::GUARD);

            $this->userService->assignRole($user, $role);

            return $user;
        });

        event(new Registered($user));

        return $user;
    }
}
