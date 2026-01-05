<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('Authorization')) {
            return response()->json([
                'success' => false,
                'message' => 'Требуется аутентификация. Отправьте заголовок Authorization с Basic Auth.',
                'error' => 'Unauthorized'
            ], 401, [
                'WWW-Authenticate' => 'Basic realm="API"'
            ]);
        }

        $authHeader = $request->header('Authorization');

        if (!str_starts_with($authHeader, 'Basic ')) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный формат аутентификации. Используйте Basic Auth.',
                'error' => 'Unauthorized'
            ], 401, [
                'WWW-Authenticate' => 'Basic realm="API"'
            ]);
        }

        $credentials = base64_decode(substr($authHeader, 6));
        if (!$credentials || !str_contains($credentials, ':')) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный формат Basic Auth.',
                'error' => 'Unauthorized'
            ], 401, [
                'WWW-Authenticate' => 'Basic realm="API"'
            ]);
        }

        [$email, $password] = explode(':', $credentials, 2);

        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return response()->json([
                'success' => false,
                'message' => 'Неверные учетные данные.',
                'error' => 'Unauthorized'
            ], 401, [
                'WWW-Authenticate' => 'Basic realm="API"'
            ]);
        }

        return $next($request);
    }
}
