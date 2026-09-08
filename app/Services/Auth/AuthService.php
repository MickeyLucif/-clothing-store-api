<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Enum\Guard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTGuard;

class AuthService
{
    /**
     * @param  array<string, string>  $credentials
     */
    public function attempt(Guard $guard, array $credentials): ?string
    {
        $token = $this->guard($guard)->attempt($credentials);

        return is_string($token) ? $token : null;
    }

    public function logout(Guard $guard): void
    {
        $this->guard($guard)->logout();
    }

    public function refresh(Guard $guard): string
    {
        return $this->guard($guard)->refresh();
    }

    public function user(Guard $guard): ?Authenticatable
    {
        return $this->guard($guard)->user();
    }

    private function guard(Guard $guard): JWTGuard
    {

        $jwtGuard = Auth::guard($guard->value);

        return $jwtGuard;
    }
}
