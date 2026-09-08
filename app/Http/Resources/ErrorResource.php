<?php

declare(strict_types=1);

namespace App\Http\Resources;

final class ErrorResource
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly string $domainError,
    ) {}

    public static function make(int $code, string $message, string $domainError): self
    {
        return new self(
            code: $code,
            message: $message,
            domainError: $domainError,
        );
    }
}
