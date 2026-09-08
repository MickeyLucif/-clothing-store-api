<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer\Auth;

use App\DTO\Customer\Auth\RegisterDataDto;
use App\Http\Requests\BaseFormRequest;

class RegisterRequest extends BaseFormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function toDto(): RegisterDataDto
    {
        return RegisterDataDto::from($this->validated());
    }
}
