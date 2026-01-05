<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->get();
        return view('frontend.posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        return view('frontend.posts.show', compact('post'));
    }
}
