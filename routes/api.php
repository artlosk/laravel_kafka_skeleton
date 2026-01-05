<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/health', function () {
        return response()->json(['status' => 'ok', 'timestamp' => now()]);
    });

    // API Authentication Routes (без аутентификации)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
        Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
        Route::post('/social-login', [App\Http\Controllers\Api\AuthController::class, 'socialLogin']);
        Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])
            ->middleware('auth:sanctum');
        Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me'])
            ->middleware('auth:sanctum');
        Route::post('/refresh', [App\Http\Controllers\Api\AuthController::class, 'refresh'])
            ->middleware('auth:sanctum');
    });

    // API роуты с Basic Auth (логин/пароль) - используем кастомный middleware для JSON ответов
    Route::middleware(['auth.api.basic', 'throttle:api'])->group(function () {

        // Posts API с Basic Auth
        Route::prefix('posts')->group(function () {
            Route::get('/', [PostController::class, 'index'])
                ->middleware('check.permission:read-posts');
            Route::get('/{post}', [PostController::class, 'show'])
                ->middleware('check.permission:read-posts');
            Route::post('/', [PostController::class, 'store'])
                ->middleware('check.permission:create-posts');
            Route::put('/{post}', [PostController::class, 'update'])
                ->middleware('check.permission:edit-posts');
            Route::delete('/{post}', [PostController::class, 'destroy'])
                ->middleware('check.permission:delete-posts');
            Route::get('/user/{user}', [PostController::class, 'getUserPosts'])
                ->middleware('check.permission:read-posts')
                ->name('posts.user');
        });
    });
});
