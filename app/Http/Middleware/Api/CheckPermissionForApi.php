<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionForApi
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Пользователь не аутентифицирован',
                'status_code' => 401
            ], 401);
        }

        $hasPermission = $user->hasPermissionTo($permission, 'web');

        if (!$hasPermission) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => "У вас нет прав для выполнения действия: {$permission}",
                'status_code' => 403
            ], 403);
        }

        return $next($request);
    }
}
