<?php

namespace App\Services\Backend;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserFilterService
{
    public function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->has('search') && trim($request->search) !== '') {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && trim($request->role) !== '') {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', trim($request->role));
            });
        }

        return $query;
    }

    public function applySorting(Builder $query, Request $request): Builder
    {
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['id', 'name', 'email', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        return $query->orderBy($sort, $direction);
    }

    public function getFilteredUsers(Request $request, int $perPage = 10)
    {
        $query = User::with('roles', 'permissions');

        $query = $this->applyFilters($query, $request);
        $query = $this->applySorting($query, $request);

        return $query->paginate($perPage)->withQueryString();
    }
}

