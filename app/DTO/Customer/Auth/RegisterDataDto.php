<?php

declare(strict_types=1);

namespace App\DTO\Customer\Auth;

use Spatie\LaravelData\Dto;

class RegisterDataDto extends Dto
{
    public string $name;

    public string $email;

    public string $password;
}
