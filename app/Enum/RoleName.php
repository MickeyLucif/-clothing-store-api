<?php

namespace App\Enum;

enum RoleName: string
{
    case USER = 'user';
    case ADMIN = 'admin';
    case MANAGER = 'manager';
}
