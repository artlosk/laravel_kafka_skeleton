<?php

namespace App\Policies\Api;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('read-posts');
    }

    public function view(User $user, Post $post): bool
    {
        return $user->can('read-posts');
    }

    public function create(User $user): bool
    {
        return $user->can('create-posts');
    }

    public function update(User $user, Post $post): bool
    {
        return $user->can('edit-posts');
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->can('delete-posts');
    }

    public function restore(User $user, Post $post): bool
    {
        return false;
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return false;
    }
}
