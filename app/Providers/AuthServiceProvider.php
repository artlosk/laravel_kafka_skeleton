<?php

namespace App\Providers;

use App\Models\Post;
use App\Policies\Api\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
