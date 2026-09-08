<?php

declare(strict_types=1);

namespace App\Constants\Http;

final class HttpErrorCode
{
    public const VALIDATION_FAILED = 'VALIDATION_FAILED';

    public const UNAUTHENTICATED = 'UNAUTHENTICATED';

    public const TOKEN_EXPIRED = 'TOKEN_EXPIRED';

    public const TOKEN_INVALID = 'TOKEN_INVALID';

    public const TOKEN_BLACKLISTED = 'TOKEN_BLACKLISTED';

    public const FORBIDDEN = 'FORBIDDEN';

    public const NOT_FOUND = 'NOT_FOUND';

    public const METHOD_NOT_ALLOWED = 'METHOD_NOT_ALLOWED';

    public const TOO_MANY_REQUESTS = 'TOO_MANY_REQUESTS';

    public const HTTP_ERROR = 'HTTP_ERROR';

    public const SERVER_ERROR = 'SERVER_ERROR';

    private function __construct() {}
}
