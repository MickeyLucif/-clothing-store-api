<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /** @return Collection<int, self> */
    public static function allForEveryGuard(): Collection
    {
        return self::query()
            ->with('permissions')
            ->withCount('permissions')
            ->orderBy('name')
            ->get();
    }

    /** @return Collection<int, self> */
    public static function allForGuard(string $guardName): Collection
    {
        return self::query()
            ->where('guard_name', $guardName)
            ->with('permissions')
            ->withCount('permissions')
            ->orderBy('name')
            ->get();
    }

    public function legacyUsers(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
