<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enum\Permission as PermissionEnum;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(static function (Authenticatable $user, string $ability): ?bool {
            if (! $user instanceof Admin && ! $user instanceof User) {
                return null;
            }

            if (PermissionEnum::tryFrom($ability) === null) {
                return null;
            }

            return $user->hasPermissionTo($ability);
        });
    }
}
