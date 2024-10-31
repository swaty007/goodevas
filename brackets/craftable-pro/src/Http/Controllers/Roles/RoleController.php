<?php

namespace Brackets\CraftablePro\Http\Controllers\Roles;

use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Http\Requests\Roles\IndexRoleRequest;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RoleController extends Controller
{
    /**
     * @param IndexRoleRequest $request
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(IndexRoleRequest $request)
    {
        $roles = QueryBuilder::for(Role::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id',
                    'name',
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts(['id', 'name'])
            ->with('users')
            ->select(['id', 'name'])
            ->paginate(request()->get('per_page'))->withQueryString();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
        ]);
    }

    /**
     * @param Role $role
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(Role $role)
    {
        $this->authorize('craftable-pro.role.edit');

        $allPermissions = Permission::all()->map->name;
        $assignedPermissions = $role->permissions->map->name;

        $permissionsTree = [];

        collect($allPermissions)->each(function ($permission) use (&$permissionsTree, $assignedPermissions) {
            $isAssigned = collect($assignedPermissions)->contains($permission);
            Arr::set($permissionsTree, $permission, $isAssigned);
        });

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissionsTree' => $permissionsTree,
        ]);
    }

    /**
     * @param Role $role
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Role $role, Request $request)
    {
        $this->authorize('craftable-pro.role.edit');

        $role->syncPermissions(collect(Arr::dot($request->input('permissionsTree')))->filter()->keys());

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Role has been successfully updated')]);
    }
}
