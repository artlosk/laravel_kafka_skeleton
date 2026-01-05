<?php

namespace App\Http\Middleware\Backend;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $roles = explode('|', $role);
        if (!Auth::check() || !Auth::user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
