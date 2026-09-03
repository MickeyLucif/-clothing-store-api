<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidEmailVerificationLinkException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailVerificationController extends Controller
{
    /**
     * Handle the incoming request.
     * @throws InvalidEmailVerificationLinkException
     */
    public function __invoke(Request $request)
    {
        $userId = $request->route('id');
        $user = User::query()
            ->where('id', $userId)
            ->firstOrFail();

        $linkHash = (string) $request->route('hash');
        $expectedHash = sha1($user->getEmailForVerification());
        if (! hash_equals($expectedHash, $linkHash)) {
            throw new InvalidEmailVerificationLinkException();
        }

        if ($user->hasVerifiedEmail()) {
            return new JsonResponse(
                data: [
                    'message' => 'Email already verified',
                ],
                status: Response::HTTP_OK
            );
        }

//        if ($user->markEmailAsVerified()) {
//
//        }



    }
}
