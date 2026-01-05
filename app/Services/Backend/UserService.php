<?php

namespace App\Services\Backend;

use App\Models\User;
use App\Http\Requests\Backend\StoreUserRequest;
use App\Http\Requests\Backend\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    public function createUser(array $data, ?array $roles = null): User
    {
        return DB::transaction(function () use ($data, $roles) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            if ($roles) {
                $user->syncRoles($roles);
            }

            return $user;
        });
    }

    public function updateUser(User $user, array $data, ?array $roles = null): User
    {
        return DB::transaction(function () use ($user, $data, $roles) {
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (isset($data['password']) && !empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            if ($roles !== null) {
                $user->syncRoles($roles);
            }

            return $user->fresh();
        });
    }

    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            $user->tokens()->delete();

            return $user->delete();
        });
    }

    public function createApiToken(User $user, string $name = 'Admin Generated Token')
    {
        return $user->createApiToken($name);
    }

    public function revokeApiTokens(User $user): int
    {
        return $user->tokens()->delete();
    }
}

