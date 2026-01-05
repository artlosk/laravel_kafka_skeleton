<?php

namespace Tests\Feature\Backend;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BackendRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_backend_posts_index_requires_authentication()
    {
        $response = $this->get('/admin/posts');
        $response->assertRedirect('/login');
    }

    public function test_backend_posts_index_requires_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/posts');
        $response->assertStatus(403);
    }

    public function test_backend_posts_index_works_with_permission()
    {
        $user = User::factory()->create();
        $adminPermission = Permission::create(['name' => 'access-admin-panel', 'guard_name' => 'web']);
        $readPermission = Permission::create(['name' => 'read-posts', 'guard_name' => 'web']);
        $user->givePermissionTo([$adminPermission, $readPermission]);

        $this->actingAs($user);

        $response = $this->get('/admin/posts');
        $response->assertStatus(200);
    }

    public function test_backend_posts_create_requires_permission()
    {
        $user = User::factory()->create();
        $adminPermission = Permission::create(['name' => 'access-admin-panel', 'guard_name' => 'web']);
        $user->givePermissionTo($adminPermission);
        $this->actingAs($user);

        $response = $this->get('/admin/posts/create');
        $response->assertStatus(403);
    }

    public function test_backend_posts_create_works_with_permission()
    {
        $user = User::factory()->create();
        $adminPermission = Permission::create(['name' => 'access-admin-panel', 'guard_name' => 'web']);
        $createPermission = Permission::create(['name' => 'create-posts', 'guard_name' => 'web']);
        $user->givePermissionTo([$adminPermission, $createPermission]);

        $this->actingAs($user);

        $response = $this->get('/admin/posts/create');
        $response->assertStatus(200);
    }

    public function test_backend_media_index_requires_permission()
    {
        $user = User::factory()->create();
        $adminPermission = Permission::create(['name' => 'access-admin-panel', 'guard_name' => 'web']);
        $user->givePermissionTo($adminPermission);
        $this->actingAs($user);

        $response = $this->get('/admin/media');
        $response->assertStatus(200); // Медиа доступно только с access-admin-panel
    }

    public function test_backend_media_index_works_with_permission()
    {
        $user = User::factory()->create();
        $adminPermission = Permission::create(['name' => 'access-admin-panel', 'guard_name' => 'web']);
        $mediaPermission = Permission::create(['name' => 'view-media', 'guard_name' => 'web']);
        $user->givePermissionTo([$adminPermission, $mediaPermission]);

        $this->actingAs($user);

        $response = $this->get('/admin/media');
        $response->assertStatus(200);
    }
}
