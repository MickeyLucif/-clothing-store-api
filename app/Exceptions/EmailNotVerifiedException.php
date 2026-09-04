<?php

namespace App\Exceptions;

use App\Enum\Auth\AuthErrorCode;
use Symfony\Component\HttpFoundation\Response;

final class EmailNotVerifiedException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            errorCode: AuthErrorCode::EMAIL_NOT_VERIFIED->value,
            message: 'Email not verified',
            statusCode: Response::HTTP_FORBIDDEN,
        );
    }
}
