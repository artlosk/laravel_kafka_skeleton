<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\SocialLoginRequest;
use App\Services\Api\AuthService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    )
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->email,
            $request->password
        );

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Неверные учетные данные.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Успешная аутентификация.',
            'data' => $result
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Пользователь успешно зарегистрирован.',
            'data' => $result
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Успешный выход из системы.'
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ]
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $token = $this->authService->refreshToken($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Токен успешно обновлен.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    public function socialLogin(SocialLoginRequest $request): JsonResponse
    {
        $result = $this->authService->socialLogin($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Успешная аутентификация через провайдера.',
            'data' => $result
        ]);
    }
}
