<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer\Auth;

use App\Http\Requests\BaseFormRequest;

class ResendEmailVerificationRequest extends BaseFormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
        ];
    }

    public function email(): string
    {
        return (string) $this->validated('email');
    }
}
