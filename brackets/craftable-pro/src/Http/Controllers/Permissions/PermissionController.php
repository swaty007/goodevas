<?php

namespace Brackets\CraftablePro\Http\Controllers\Permissions;

use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Http\Requests\Permissions\IndexPermissionRequest;
use Brackets\CraftablePro\Http\Requests\Permissions\UpdatePermissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * @return Response
     */
    public function index(IndexPermissionRequest $request)
    {
        // column with permissions names
        $allPermissions = Permission::all()->map->name;
        $permissionsTree = [];

        collect($allPermissions)->each(function ($permission) use (&$permissionsTree) {
            Arr::set($permissionsTree, $permission, $permission);
        });

        // column for roles
        $rolesPermissions = Role::with('permissions')->get();

        $roleTree = $rolesPermissions->map(function ($role) {
            return ['id' => $role['id'], 'name' => $role['name'], 'permissions' => $role->permissions->map->name];
        });

        return Inertia::render('Permissions/Index', [
            'roles' => $roleTree,
            'permissions' => $permissionsTree,
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function update(UpdatePermissionRequest $request)
    {
        $validated = $request->validated();

        collect($validated['roles'])->each(function ($role) {
            $currentRole = Role::find($role['id']);

            $currentRole->syncPermissions($role['permissions']);
        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Permissions have been successfully updated')]);
    }
}
