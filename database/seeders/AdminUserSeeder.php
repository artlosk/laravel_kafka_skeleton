<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $permissions = [
            'access-admin-panel',
            'manage-users',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'read-posts',
            'view-posts',
            'create-posts',
            'edit-posts',
            'delete-posts',
            'view-media',
            'upload-media',
            'delete-media',
            'manage-roles',
            'manage-permissions',
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $adminRole->givePermissionTo(Permission::where('guard_name', 'web')->get());

        $admin->assignRole($adminRole);

        $adminToken = $admin->createApiToken('Admin Token');
        $this->command->info('Admin API token: ' . $adminToken->plainTextToken);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: admin123');
    }
}
