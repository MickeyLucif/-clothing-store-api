<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Auth;

use App\DTO\Admin\Auth\LoginDataDto;
use App\Enum\Guard;
use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\Concerns\HasLoginThrottleKey;
use Illuminate\Contracts\Validation\ValidationRule;

class LoginRequest extends BaseFormRequest
{
    use HasLoginThrottleKey;

    /**
     * Get the validation rules that apply to the request.
     *
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
        return Guard::ADMIN;
    }

    public function toDto(): LoginDataDto
    {
        return LoginDataDto::from($this->validated());
    }
}
