<?php

declare(strict_types=1);

namespace App\Http\Resources;

final class MessageResource
{
    public function __construct(
        public readonly string $message,
    ) {}

    public static function make(string $message): self
    {
        return new self(message: $message);
    }
}
