<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Roles;

use App\Actions\Admin\Roles\GetRolesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Roles\IndexRoleRequest;
use App\Http\Resources\Admin\RoleResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class IndexRoleController extends Controller
{
    public function __invoke(IndexRoleRequest $request, GetRolesAction $action): JsonResponse
    {
        return new JsonResponse(
            data: RoleResource::collection($action($request->guard())),
            status: Response::HTTP_OK,
        );
    }
}
