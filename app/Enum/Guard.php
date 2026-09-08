<?php

declare(strict_types=1);

namespace App\Enum;

enum Guard: string
{
    case ADMIN = 'admin';
    case API = 'api';

}
