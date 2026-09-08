<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use App\Constants\Auth\AuthErrorCode;
use App\Enum\Guard;
use App\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

final class AuthenticatedAccountUnavailableException extends DomainException
{
    public function __construct(public readonly Guard $guard)
    {
        parent::__construct(
            errorMessage: "The {$guard->value} guard issued a token but could not resolve the account.",
            errorCode: AuthErrorCode::AUTHENTICATED_ACCOUNT_UNAVAILABLE,
            statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }
}
