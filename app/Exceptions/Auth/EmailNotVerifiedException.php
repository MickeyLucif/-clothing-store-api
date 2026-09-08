<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use App\Constants\Auth\AuthErrorCode;
use App\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

final class EmailNotVerifiedException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            errorMessage: 'Email not verified',
            errorCode: AuthErrorCode::EMAIL_NOT_VERIFIED,
            statusCode: Response::HTTP_FORBIDDEN
        );
    }
}
