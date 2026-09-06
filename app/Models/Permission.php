<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    /** @return Collection<int, self> */
    public static function allForGuard(string $guardName): Collection
    {
        return self::query()
            ->where('guard_name', $guardName)
            ->orderBy('name')
            ->get();
    }
}
