<?php

declare(strict_types=1);

namespace App\Http\Resources;

final class TokenResource
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $tokenType,
        public readonly int $expiresIn,
    ) {}

    public static function make(string $token): self
    {
        return new self(
            accessToken: $token,
            tokenType: 'bearer',
            expiresIn: (int) config('jwt.ttl', 60) * 60,
        );
    }
}
