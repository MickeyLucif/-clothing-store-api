<?php

namespace App\Http\Controllers\Web;

use App\Enum\Guard;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->string('query')->toString();
        $guard = $request->string('guard')->toString() ?: 'all';

        $roles = $guard === 'all'
            ? Role::allForEveryGuard()
            : Role::allForGuard(Guard::from($guard)->value);
        $selectedRole = $roles->firstWhere('id', $request->integer('role')) ?? $roles->first();

        $actions = ['view', 'create', 'update', 'delete'];
        $permissions=Permission::all();

        $permissionModules = $permissions
            ->filter(fn ($permission) => in_array(str($permission->name)->after('.')->toString(), $actions, true))
            ->groupBy(fn ($permission) => str($permission->name)->before('.')->toString());

        return view('admin.roles.index', [
            'roles' => $roles,
            'permissionModules' => $permissionModules,
            'actions' => $actions,
            'permissions' => $permissions,
            'selectedRole' => $selectedRole,
            'query' => $query,
            'guard' => $guard,
        ]);
    }
}
