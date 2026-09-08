<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use App\Constants\Auth\AuthErrorCode;
use App\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidCredentialsException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            errorMessage: 'These credentials do not match our records.',
            errorCode: AuthErrorCode::INVALID_CREDENTIALS,
            statusCode: Response::HTTP_UNAUTHORIZED
        );
    }
}
