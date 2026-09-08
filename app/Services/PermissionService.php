<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\Guard;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    /** @param list<string> $names */
    public function syncForGuard(array $names, Guard $guard): void
    {
        DB::transaction(function () use ($names, $guard): void {
            $existing = Permission::query()
                ->where('guard_name', $guard->value)
                ->pluck('name')
                ->all();

            $missing = array_values(array_diff($names, $existing));
            $stale = array_values(array_diff($existing, $names));

            if ($missing !== []) {
                $this->insert($missing, $guard);
            }

            if ($stale !== []) {
                $this->delete($stale, $guard);
            }
        });
    }

    /** @param list<string> $names */
    private function insert(array $names, Guard $guard): void
    {
        $now = now();

        Permission::query()->insertOrIgnore(array_map(
            static fn (string $name): array => [
                'name' => $name,
                'guard_name' => $guard->value,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $names,
        ));
    }

    /** @param list<string> $names */
    private function delete(array $names, Guard $guard): void
    {
        Permission::query()
            ->where('guard_name', $guard->value)
            ->whereIn('name', $names)
            ->delete();
    }
}
