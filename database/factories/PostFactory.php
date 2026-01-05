<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'content' => fake()->paragraphs(3, true),
            'user_id' => User::inRandomOrder()->first()?->id ?? 1,
        ];
    }

    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    public function recent(): static
    {
        return $this->state(fn(array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
