<?php

namespace App\Http\Resources;

use App\Models\User;

final class UserResource
{
    public int $id;
    public string $name;
    public string $email;
    public string $role;

    public static function make(User $user): self
    {
        $resource = new self();
        $resource->id = $user->id;
        $resource->name = $user->name;
        $resource->email = $user->email;
        $resource->role = $user->role->name;
        return $resource;
    }
}
