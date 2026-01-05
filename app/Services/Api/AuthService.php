<?php

namespace App\Services\Api;

use App\Models\User;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\SocialLoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AuthService
{

    public function login(string $email, string $password): ?array
    {
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return null;
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $defaultRole = Role::where('name', 'user')->first();
        if ($defaultRole) {
            $user->assignRole($defaultRole);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    public function socialLogin(array $data): array
    {
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'] ?? explode('@', $data['email'])[0],
                'password' => Hash::make(Str::random(32)),
            ]
        );

        if ($user->wasRecentlyCreated && !$user->hasAnyRole()) {
            $defaultRole = Role::where('name', 'user')->first();
            if ($defaultRole) {
                $user->assignRole($defaultRole);
            }
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    public function logout(User $user): bool
    {
        return $user->currentAccessToken()->delete();
    }

    public function refreshToken(User $user): string
    {
        $user->currentAccessToken()->delete();
        return $user->createToken('api-token')->plainTextToken;
    }
}

