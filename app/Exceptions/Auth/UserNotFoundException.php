<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use App\Constants\Auth\AuthErrorCode;
use App\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

final class UserNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            errorMessage: 'User for email verification not found',
            errorCode: AuthErrorCode::EMAIL_VERIFICATION_USER_NOT_FOUND,
            statusCode: Response::HTTP_NOT_FOUND
        );
    }
}
