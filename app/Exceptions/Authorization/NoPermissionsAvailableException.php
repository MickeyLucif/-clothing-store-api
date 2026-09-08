<?php

declare(strict_types=1);

namespace App\Exceptions\Authorization;

use App\Constants\Authorization\AuthorizationErrorCode;
use App\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

final class NoPermissionsAvailableException extends DomainException
{
    public function __construct(public readonly string $guardName)
    {
        parent::__construct(
            errorMessage: "No permissions are registered for the {$guardName} guard. "
                .'Run the PermissionSeeder before granting them.',
            errorCode: AuthorizationErrorCode::NO_PERMISSIONS_AVAILABLE_FOR_GUARD,
            statusCode: Response::HTTP_CONFLICT,
        );
    }
}
