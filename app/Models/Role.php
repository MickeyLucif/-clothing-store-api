<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Role extends Model
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

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    /** @param Collection<int, Permission> $permissions */
    public function syncPermissions(Collection $permissions): void
    {
        $this->permissions()->sync($permissions->modelKeys());
        $this->unsetRelation('permissions');
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles');
    }

    public function admins(): MorphToMany
    {
        return $this->morphedByMany(Admin::class, 'model', 'model_has_roles');
    }
}
