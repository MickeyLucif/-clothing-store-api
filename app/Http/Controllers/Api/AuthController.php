<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\RegisterUserAction;
use App\DTO\Auth\RegisterDataDto;
use App\Exceptions\EmailNotVerifiedException;
use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserAction $action): JsonResponse
    {
        $data = RegisterDataDto::from($request->validated());
        $user = $action($data);

        return new JsonResponse(
            data: UserResource::make($user),
            status: 201
        );

    }

    /**
     * Get a JWT via given credentials.
     *
     * @throws EmailNotVerifiedException
     * @throws InvalidCredentialsException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! $token = auth('api')->attempt($credentials)) {
            throw new InvalidCredentialsException;
        }

        $user = auth('api')->user();

        if ($user instanceof User && ! $user->hasVerifiedEmail()) {
            auth('api')->logout();
            throw new EmailNotVerifiedException;
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     */
    public function user(): JsonResponse
    {
        $user = auth('api')->user();

        if (! $user instanceof User) {
            return new JsonResponse(
                data: ['error' => 'Unauthorized'],
                status: 401);
        }

        return new JsonResponse(
            data: UserResource::make($user),
            status: 200
        );
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
