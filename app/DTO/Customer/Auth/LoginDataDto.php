<?php

declare(strict_types=1);

namespace App\DTO\Customer\Auth;

use Spatie\LaravelData\Dto;

class LoginDataDto extends Dto
{
    public string $email;

    public string $password;

    /** @return array<string, string> */
    public function credentials(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
