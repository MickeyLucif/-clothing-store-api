<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'name',
        'guard_name',
    ];

    public static function findOrCreate(string $name, string $guardName): self
    {
        return self::query()->firstOrCreate([
            'name' => $name,
            'guard_name' => $guardName,
        ]);
    }

    /** @return Collection<int, self> */
    public static function allForGuard(string $guardName): Collection
    {
        return self::query()
            ->where('guard_name', $guardName)
            ->orderBy('name')
            ->get();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }
}
