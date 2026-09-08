<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer\Auth;

use App\DTO\Customer\Auth\LoginDataDto;
use App\Enum\Guard;
use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\Concerns\HasLoginThrottleKey;

class LoginRequest extends BaseFormRequest
{
    use HasLoginThrottleKey;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    protected function throttleGuard(): Guard
    {
        return Guard::API;
    }

    public function toDto(): LoginDataDto
    {
        return LoginDataDto::from($this->validated());
    }
}
