<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreRoleRequest;
use App\Http\Requests\Backend\UpdateRoleRequest;
use App\Http\Requests\Backend\StorePermissionRequest;
use App\Http\Requests\Backend\UpdatePermissionRequest;
use App\Http\Requests\Backend\UpdateUserRolesPermissionsRequest;
use App\Services\Backend\RoleFilterService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        protected RoleFilterService $filterService
    )
    {
        $this->middleware(['auth']);
    }

    public function indexRoles(\Illuminate\Http\Request $request): View
    {
        $this->authorize('manage-roles');

        $roles = $this->filterService->getFilteredRoles($request, self::PAGINATION_LIMIT);
        return view('backend.roles.index', compact('roles'));
    }

    public function createRole(): View
    {
        $this->authorize('manage-roles');
        $permissions = Permission::all();
        return view('backend.roles.create', compact('permissions'));
    }

    public function storeRole(StoreRoleRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $role = Role::create(['name' => $request->input('name')]);
                if ($request->input('permissions')) {
                    $role->syncPermissions($request->input('permissions'));
                }
            });
            return redirect()->route('backend.roles.index')->with('success', __('messages.role_created'));
        } catch (\Exception $e) {
            return redirect()->route('backend.roles.create')->with('error', __('messages.role_creation_failed'));
        }
    }

    public function showRole(Role $role): View
    {
        $this->authorize('view-roles');
        $role->load(['permissions', 'users']);
        return view('backend.roles.show', compact('role'));
    }

    public function editRole(Role $role): View
    {
        $this->authorize('manage-roles');
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('backend.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function updateRole(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $role) {
                $role->update(['name' => $request->input('name')]);
                $role->syncPermissions($request->input('permissions', []));
            });
            return redirect()->route('backend.roles.index')->with('success', __('messages.role_updated'));
        } catch (\Exception $e) {
            return redirect()->route('backend.roles.edit', $role)->with('error', __('messages.role_update_failed'));
        }
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        $this->authorize('manage-roles');
        if ($role->users()->count() > 0) {
            return redirect()->route('backend.roles.index')->with('error', __('messages.role_in_use'));
        }
        try {
            $role->delete();
            return redirect()->route('backend.roles.index')->with('success', __('messages.role_deleted'));
        } catch (\Exception $e) {
            return redirect()->route('backend.roles.index')->with('error', __('messages.role_deletion_failed'));
        }
    }

    public function indexPermissions(\Illuminate\Http\Request $request): View
    {
        $this->authorize('manage-permissions');

        $permissions = $this->filterService->getFilteredPermissions($request, self::PAGINATION_LIMIT);
        return view('backend.permissions.index', compact('permissions'));
    }

    public function createPermission(): View
    {
        $this->authorize('manage-permissions');
        return view('backend.permissions.create');
    }

    public function storePermission(StorePermissionRequest $request): RedirectResponse
    {
        try {
            Permission::create(['name' => $request->input('name')]);
            return redirect()->route('backend.permissions.index')->with('success', __('messages.permission_created'));
        } catch (\Exception $e) {
            return redirect()->route('backend.permissions.create')->with('error', __('messages.permission_creation_failed'));
        }
    }

    public function showPermission(Permission $permission): View
    {
        $this->authorize('view-permissions');
        $permission->load(['roles', 'users']);
        return view('backend.permissions.show', compact('permission'));
    }

    public function editPermission(Permission $permission): View
    {
        $this->authorize('manage-permissions');
        return view('backend.permissions.edit', compact('permission'));
    }

    public function updatePermission(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        try {
            $permission->update(['name' => $request->input('name')]);
            return redirect()->route('backend.permissions.index')->with('success', __('messages.permission_updated'));
        } catch (\Exception $e) {
            return redirect()->route('backend.permissions.edit', $permission)->with('error', __('messages.permission_update_failed'));
        }
    }

    public function destroyPermission(Permission $permission): RedirectResponse
    {
        $this->authorize('manage-permissions');
        if ($permission->roles()->count() > 0 || $permission->users()->count() > 0) {
            return redirect()->route('backend.permissions.index')->with('error', __('messages.permission_in_use'));
        }
        try {
            $permission->delete();
            return redirect()->route('backend.permissions.index')->with('success', __('messages.permission_deleted'));
        } catch (\Exception $e) {
            return redirect()->route('backend.roles.index')->with('error', __('messages.permission_deletion_failed'));
        }
    }

    public function manageUserRolesPermissions(): View
    {
        $this->authorize('manage-roles');
        $this->authorize('manage-permissions');
        $users = User::paginate(self::PAGINATION_LIMIT);
        $roles = Role::all();
        $permissions = Permission::all();
        return view('backend.roles.manage', compact('users', 'roles', 'permissions'));
    }

    public function updateUserRolesPermissions(UpdateUserRolesPermissionsRequest $request): RedirectResponse
    {
        try {
            $user = User::findOrFail($request->input('user_id'));
            DB::transaction(function () use ($request, $user) {
                $user->syncRoles($request->input('roles', []));
                $user->syncPermissions($request->input('permissions', []));
            });
            return redirect()->route('backend.roles.manage')->with('success', __('messages.user_roles_permissions_updated'));
        } catch (\Exception $e) {
            return redirect()->route('backend.roles.manage')->with('error', __('messages.user_roles_permissions_update_failed'));
        }
    }

    public function getUserRolesPermissions(User $user): JsonResponse
    {
        $this->authorize('manage-roles');
        $this->authorize('manage-permissions');
        return response()->json([
            'roles' => $user->roles->pluck('name')->toArray(),
            'permissions' => $user->permissions->pluck('name')->toArray(),
        ]);
    }
}
