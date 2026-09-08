<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Auth;

use App\Actions\Customer\Auth\RefreshTokenAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TokenResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenController extends Controller
{
    public function __invoke(RefreshTokenAction $action): JsonResponse
    {
        return new JsonResponse(
            data: TokenResource::make($action()),
            status: Response::HTTP_OK,
        );
    }
}
