<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Roles;

use App\Enum\Guard;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class IndexRoleRequest extends BaseFormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guard' => ['nullable', Rule::enum(Guard::class)],
        ];
    }

    public function guard(): ?Guard
    {
        $guard = $this->validated('guard');

        return is_string($guard) ? Guard::from($guard) : null;
    }
}
