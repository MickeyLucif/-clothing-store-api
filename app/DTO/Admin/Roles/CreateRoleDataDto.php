<?php

declare(strict_types=1);

namespace App\DTO\Admin\Roles;

use App\Enum\Guard;
use Spatie\LaravelData\Dto;

class CreateRoleDataDto extends Dto
{
    public string $name;

    public Guard $guard;
}
