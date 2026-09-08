<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use App\Constants\Auth\AuthErrorCode;
use App\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

final class UnauthenticatedException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            errorMessage: 'Unauthenticated.',
            errorCode: AuthErrorCode::UNAUTHENTICATED,
            statusCode: Response::HTTP_UNAUTHORIZED,
        );
    }
}
