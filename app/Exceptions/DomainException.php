<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Http\Resources\ErrorResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class DomainException extends Exception
{
    public function __construct(
        public readonly string $errorMessage,
        public readonly string $errorCode,
        public readonly int $statusCode = Response::HTTP_BAD_REQUEST,
    ) {
        parent::__construct($errorMessage, $statusCode);
    }

    public function render(Request $request): JsonResponse
    {
        return new JsonResponse(
            data: ErrorResource::make($this->statusCode, $this->errorMessage, $this->errorCode),
            status: $this->statusCode,
        );
    }
}
