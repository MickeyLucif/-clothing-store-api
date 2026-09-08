<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Enum\Guard;
use Illuminate\Support\Str;

trait HasLoginThrottleKey
{
    abstract protected function throttleGuard(): Guard;

    public function throttleKey(): string
    {
        return implode('|', [
            $this->throttleGuard()->value,
            Str::transliterate(Str::lower($this->string('email')->toString())),
            (string) $this->ip(),
        ]);
    }
}
