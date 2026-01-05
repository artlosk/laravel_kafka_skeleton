<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $userPermissions = [
            'read-posts',
            'view-posts',
        ];

        foreach ($userPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->givePermissionTo(Permission::where('guard_name', 'web')->whereIn('name', $userPermissions)->get());

        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $testUser->assignRole($userRole);

        $testToken = $testUser->createApiToken('Test User Token');
        $this->command->info('Test user API token: ' . $testToken->plainTextToken);

        $this->call(AdminUserSeeder::class);

        $this->command->info('Test user created successfully!');
        $this->command->info('Email: test@example.com');
        $this->command->info('Password: password');
        $this->command->info('Role: user (can read and view posts)');
    }
}
