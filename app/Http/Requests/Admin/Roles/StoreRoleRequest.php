<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Roles;

use App\DTO\Admin\Roles\CreateRoleDataDto;
use App\Enum\Guard;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends BaseFormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')->where(
                    fn (Builder $query): Builder => $query->where(
                        'guard_name', $this->string('guard')->toString(),
                    ),
                ),
            ],
            'guard' => ['required', Rule::enum(Guard::class)],
        ];
    }

    public function toDto(): CreateRoleDataDto
    {
        return CreateRoleDataDto::from([
            'name' => (string) $this->validated('name'),
            'guard' => Guard::from((string) $this->validated('guard')),
        ]);
    }
}
