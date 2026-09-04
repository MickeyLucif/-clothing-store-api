<?php

namespace App\Enum;

enum RoleName: string
{
    case USER = 'customer';
    case ADMIN = 'admin';
    case MANAGER = 'manager';
}
