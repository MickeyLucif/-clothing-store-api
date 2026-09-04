<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\ResendEmailVerificationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResendEmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResendEmailVerificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        ResendEmailVerificationRequest $request,
        ResendEmailVerificationAction $action
    ): JsonResponse {
        $email = (string) $request->validated('email');
        $action($email);

        return new JsonResponse(
            data: [
                'message' => 'If the account exists and is not verified, a verification link has been sent',
            ],
            status: Response::HTTP_OK
        );
    }
}
