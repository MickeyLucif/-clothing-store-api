<?php

declare(strict_types=1);

namespace App\DTO\Admin\Admins;

use Spatie\LaravelData\Dto;

class CreateAdminDataDto extends Dto
{
    public string $name;

    public string $email;

    public string $password;
}
