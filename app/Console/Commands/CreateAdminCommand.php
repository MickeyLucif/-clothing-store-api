<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Admin\CreateAdminAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create';

    protected $description = 'Create an administrator account';

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

        $admin = $createAdmin(
            name: $name,
            email: $email,
            password: $password,
        );

        info("Administrator {$admin->email} created successfully.");

        return self::SUCCESS;
    }
}
