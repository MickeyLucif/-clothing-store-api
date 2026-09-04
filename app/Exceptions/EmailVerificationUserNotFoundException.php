<?php

namespace App\Exceptions;

use App\Enum\Auth\AuthErrorCode;
use Symfony\Component\HttpFoundation\Response;

final class EmailVerificationUserNotFoundException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            errorCode: AuthErrorCode::EMAIL_VERIFICATION_USER_NOT_FOUND->value,
            message: 'User for email verification not found',
            statusCode: Response::HTTP_NOT_FOUND,
        );
    }
}
