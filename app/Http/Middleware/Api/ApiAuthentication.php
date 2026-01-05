<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->bearerToken()) {
            return $this->unauthorizedResponse('Токен доступа не предоставлен');
        }

        if (!auth('sanctum')->check()) {
            return $this->unauthorizedResponse('Недействительный токен доступа');
        }

        return $next($request);
    }

    private function unauthorizedResponse(string $message): JsonResponse
    {
        return response()->json([
            'error' => 'Unauthorized',
            'message' => $message,
            'status_code' => 401
        ], 401);
    }
}
