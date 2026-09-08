<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Roles;

use App\Actions\Admin\Roles\CreateRoleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Roles\StoreRoleRequest;
use App\Http\Resources\Admin\RoleResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StoreRoleController extends Controller
{
    public function __invoke(StoreRoleRequest $request, CreateRoleAction $action): JsonResponse
    {
        return new JsonResponse(
            data: RoleResource::make($action($request->toDto())),
            status: Response::HTTP_CREATED,
        );
    }
}
