<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Auth;

use App\Actions\Customer\Auth\RegisterAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Auth\RegisterRequest;
use App\Http\Resources\Customer\UserResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RegisterController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(RegisterRequest $request, RegisterAction $action): JsonResponse
    {
        return new JsonResponse(
            data: UserResource::make($action($request->toDto())),
            status: Response::HTTP_CREATED,
        );
    }
}
