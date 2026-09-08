<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Auth;

use App\Actions\Customer\Auth\GetAuthenticatedAccountAction;
use App\Exceptions\Auth\UnauthenticatedException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\UserResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatedAccountController extends Controller
{
    /**
     * @throws UnauthenticatedException
     */
    public function __invoke(GetAuthenticatedAccountAction $action): JsonResponse
    {
        return new JsonResponse(
            data: UserResource::make($action()),
            status: Response::HTTP_OK,
        );
    }
}
