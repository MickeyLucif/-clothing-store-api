<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Auth;

use App\Actions\Customer\Auth\VerifyEmailAction;
use App\Exceptions\Auth\InvalidEmailVerificationException;
use App\Exceptions\Auth\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailController extends Controller
{
    /**
     * @throws InvalidEmailVerificationException
     * @throws UserNotFoundException
     */
    public function __invoke(int $id, string $hash, VerifyEmailAction $action): JsonResponse
    {
        $verified = $action($id, $hash);

        return new JsonResponse(
            data: MessageResource::make(
                $verified ? 'Email successfully verified' : 'Email already verified',
            ),
            status: Response::HTTP_OK,
        );
    }
}
