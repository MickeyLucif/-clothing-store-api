<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use App\Constants\Auth\AuthErrorCode;
use App\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidEmailVerificationException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            errorMessage: 'Invalid email verification link',
            errorCode: AuthErrorCode::INVALID_EMAIL_VERIFICATION_LINK,
            statusCode: Response::HTTP_FORBIDDEN
        );
    }
}
