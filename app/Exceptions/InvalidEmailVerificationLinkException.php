<?php

namespace App\Exceptions;

use App\Enum\Auth\AuthErrorCode;
use Symfony\Component\HttpFoundation\Response;

final class InvalidEmailVerificationLinkException extends ApiException
{
    public function __construct()
    {
       parent::__construct(
           errorCode: AuthErrorCode::INVALID_EMAIL_VERIFICATION_LINK->value,
           message: 'Invalid email verification link',
           statusCode: Response::HTTP_FORBIDDEN,
       );
    }

}
