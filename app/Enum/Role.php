<?php

declare(strict_types=1);

namespace App\Enum;

enum Role: string
{
    case CUSTOMER = 'Customer';
    case ADMIN = 'Admin';
    case MANAGER = 'Manager';
}
