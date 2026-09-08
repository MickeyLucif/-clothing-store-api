<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Auth;

use App\Actions\Admin\Auth\LogoutAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    public function __invoke(LogoutAction $action): JsonResponse
    {
        $action();

        return new JsonResponse(
            data: MessageResource::make('Successfully logged out.'),
            status: Response::HTTP_OK,
        );
    }
}
