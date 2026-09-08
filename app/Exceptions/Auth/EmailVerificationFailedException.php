<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use App\Constants\Auth\AuthErrorCode;
use App\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

final class EmailVerificationFailedException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            errorMessage: 'Email verification could not be saved. Please try again.',
            errorCode: AuthErrorCode::EMAIL_VERIFICATION_FAILED,
            statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }
}
