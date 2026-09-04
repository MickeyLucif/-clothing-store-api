<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\EmailVerificationUserNotFoundException;
use App\Exceptions\InvalidEmailVerificationLinkException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailVerificationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws InvalidEmailVerificationLinkException
     * @throws EmailVerificationUserNotFoundException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $userId = $request->route('id');
        $user = User::query()
            ->where('id', $userId)
            ->first();

        if ($user === null) {
            throw new EmailVerificationUserNotFoundException;
        }

        $linkHash = (string) $request->route('hash');
        $expectedHash = sha1($user->getEmailForVerification());
        if (! hash_equals($expectedHash, $linkHash)) {
            throw new InvalidEmailVerificationLinkException;
        }

        if ($user->hasVerifiedEmail()) {
            return new JsonResponse(
                data: [
                    'message' => 'Email already verified',
                ],
                status: Response::HTTP_OK
            );
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return new JsonResponse(
            data: [
                'message' => 'Email successfully verified',
            ],
            status: Response::HTTP_OK
        );
    }
}
