<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    public function index()
    {
        $this->authorize('access-admin-panel');

        $recentPosts = Post::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'users_count' => User::count(),
            'posts_count' => Post::count(),
            'roles_count' => Role::count(),
            'permissions_count' => Permission::count(),
        ];

        return view('backend.dashboard', compact('recentPosts', 'stats'));
    }
}
