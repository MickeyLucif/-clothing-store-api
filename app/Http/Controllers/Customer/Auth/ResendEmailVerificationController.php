<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Auth;

use App\Actions\Customer\Auth\ResendEmailVerificationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Auth\ResendEmailVerificationRequest;
use App\Http\Resources\MessageResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResendEmailVerificationController extends Controller
{
    public function __invoke(
        ResendEmailVerificationRequest $request,
        ResendEmailVerificationAction $action,
    ): JsonResponse {
        $action($request->email());

        return new JsonResponse(
            data: MessageResource::make(
                'If the account exists and is not verified, a verification link has been sent',
            ),
            status: Response::HTTP_OK,
        );
    }
}
