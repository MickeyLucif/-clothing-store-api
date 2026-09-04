<?php

namespace App\Exceptions;

use App\Enum\Auth\AuthErrorCode;
use Symfony\Component\HttpFoundation\Response;

final class InvalidCredentialsException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            errorCode: AuthErrorCode::INVALID_CREDENTIALS->value,
            message: 'Invalid credentials',
            statusCode: Response::HTTP_UNAUTHORIZED,
        );
    }
}
