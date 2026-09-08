<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Auth;

use App\Actions\Customer\Auth\LoginAction;
use App\Exceptions\Auth\EmailNotVerifiedException;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\TooManyLoginAttemptsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Auth\LoginRequest;
use App\Http\Resources\TokenResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * @throws EmailNotVerifiedException
     * @throws InvalidCredentialsException
     * @throws TooManyLoginAttemptsException
     */
    public function __invoke(LoginRequest $request, LoginAction $action): JsonResponse
    {
        $token = $action($request->toDto(), $request->throttleKey());

        return new JsonResponse(
            data: TokenResource::make($token),
            status: Response::HTTP_OK,
        );
    }
}
