<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Admin\Admins\CreateAdminAction;
use App\DTO\Admin\Admins\CreateAdminDataDto;
use App\Exceptions\Authorization\NoPermissionsAvailableException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Throwable;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create';

    protected $description = 'Create an administrator account';

    /**
     * @throws Throwable
     */
    public function handle(CreateAdminAction $createAdmin): int
    {
        $name = text(
            label: 'Name',
            validate: ['name' => ['required', 'string', 'max:255']],
        );
        $email = text(
            label: 'Email',
            validate: ['email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email']],
        );
        $password = password(
            label: 'Password',
            validate: ['password' => ['required', 'string', 'min:12']],
        );
        password(
            label: 'Confirm password',
            validate: fn (string $confirmation): ?string => Validator::make(
                ['password' => $password, 'password_confirmation' => $confirmation],
                ['password' => ['confirmed'], 'password_confirmation' => ['required', 'string']],
            )->errors()->first('password'),
        );

        try {
            $admin = $createAdmin(CreateAdminDataDto::from([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]));
        } catch (NoPermissionsAvailableException $exception) {
            error($exception->errorMessage);

            return self::FAILURE;
        }

        info("Administrator {$admin->email} created successfully.");

        return self::SUCCESS;
    }
}
