<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\PostCreateRequest;
use App\Http\Requests\Backend\PostUpdateRequest;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function index()
    {
        $this->authorize('read-posts');
        $posts = Post::all();
        return view('backend.posts.index', compact('posts'));
    }

    public function create()
    {
        $this->authorize('create-posts');
        return view('backend.posts.form');
    }

    public function store(PostCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $post = new Post();
            $post->savePost($request->validated());
            DB::commit();

            return redirect()->route('backend.posts.show', $post)
                ->with('success', 'Пост успешно создан.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Ошибка при создании поста: ' . $e->getMessage()]);
        }
    }

    public function show(Post $post)
    {
        $this->authorize('read-posts');
        $post->load(['relatedMedia' => function ($query) {
            $query->orderBy('media_relation_entity.order_column');
        }]);
        return view('backend.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $this->authorize('edit-posts');
        $post->load(['relatedMedia' => function ($query) {
            $query->orderBy('media_relation_entity.order_column');
        }]);
        return view('backend.posts.form', compact('post'));
    }

    public function update(PostUpdateRequest $request, Post $post)
    {
        $this->authorize('edit-posts');

        DB::beginTransaction();
        try {
            $post->savePost($request->validated());
            DB::commit();
            return redirect()->route('backend.posts.show', $post)->with('success', 'Пост успешно обновлен.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Ошибка при обновлении поста: ' . $e->getMessage()]);
        }
    }

    public function delete(Post $post)
    {
        $this->authorize('delete-posts');
        $post->delete();
        return redirect()->route('backend.posts.index')->with('success', 'Пост успешно удален.');
    }
}
