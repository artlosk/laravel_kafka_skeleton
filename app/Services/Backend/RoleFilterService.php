<?php

namespace App\Services\Backend;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RoleFilterService
{
    public function applyRoleFilters(Builder $query, Request $request): Builder
    {
        if ($request->has('search') && trim($request->search) !== '') {
            $search = trim($request->search);
            $query->where('name', 'like', "%{$search}%");
        }

        return $query;
    }

    public function applyRoleSorting(Builder $query, Request $request): Builder
    {
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['id', 'name', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'name';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        return $query->orderBy($sort, $direction);
    }

    public function getFilteredRoles(Request $request, int $perPage = 10)
    {
        $query = Role::query();

        $query = $this->applyRoleFilters($query, $request);
        $query = $this->applyRoleSorting($query, $request);

        return $query->paginate($perPage)->withQueryString();
    }

    public function applyPermissionFilters(Builder $query, Request $request): Builder
    {
        if ($request->has('search') && trim($request->search) !== '') {
            $search = trim($request->search);
            $query->where('name', 'like', "%{$search}%");
        }

        return $query;
    }

    public function applyPermissionSorting(Builder $query, Request $request): Builder
    {
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['id', 'name', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'name';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        return $query->orderBy($sort, $direction);
    }

    public function getFilteredPermissions(Request $request, int $perPage = 10)
    {
        $query = Permission::query();

        $query = $this->applyPermissionFilters($query, $request);
        $query = $this->applyPermissionSorting($query, $request);

        return $query->paginate($perPage)->withQueryString();
    }
}

