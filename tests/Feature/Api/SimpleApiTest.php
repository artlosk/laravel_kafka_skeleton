<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SimpleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_works()
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok'
            ]);
    }

    public function test_requires_authentication_for_protected_routes()
    {
        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_can_authenticate_with_sanctum()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        // Создаем разрешения
        $permissions = [
            'read-posts',
            'create-posts',
            'edit-posts',
            'delete-posts'
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }

        $user->givePermissionTo($permissions);

        $response = $this->getJson('/api/v1/posts');

        // Должен вернуть 200, так как у пользователя есть разрешения
        $response->assertStatus(200);
    }
}
