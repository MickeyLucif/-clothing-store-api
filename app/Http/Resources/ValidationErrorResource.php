<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Constants\Http\HttpErrorCode;
use Symfony\Component\HttpFoundation\Response;

final class ValidationErrorResource
{
    /** @param array<string, list<string>> $errors */
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly string $domainError,
        public readonly array $errors,
    ) {}

    /** @param array<string, list<string>> $errors */
    public static function make(string $message, array $errors): self
    {
        return new self(
            code: Response::HTTP_UNPROCESSABLE_ENTITY,
            message: $message,
            domainError: HttpErrorCode::VALIDATION_FAILED,
            errors: $errors,
        );
    }
}
