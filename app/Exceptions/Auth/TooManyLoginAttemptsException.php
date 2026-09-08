<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use App\Constants\Auth\AuthErrorCode;
use App\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

final class TooManyLoginAttemptsException extends DomainException
{
    public function __construct(public readonly int $availableInSeconds)
    {
        parent::__construct(
            errorMessage: "Too many login attempts. Please try again in {$availableInSeconds} seconds.",
            errorCode: AuthErrorCode::TOO_MANY_LOGIN_ATTEMPTS,
            statusCode: Response::HTTP_TOO_MANY_REQUESTS,
        );
    }
}
