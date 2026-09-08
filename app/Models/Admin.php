<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\Guard;
use App\Models\Concerns\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property-read int $id
 * @property string $name
 * @property string $email
 * @property string $password
 */
class Admin extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<AdminFactory> */
    use HasFactory, HasRolesAndPermissions;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function authorizationGuard(): Guard
    {
        return Guard::ADMIN;
    }
}
