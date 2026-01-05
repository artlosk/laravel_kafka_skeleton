<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostRequest;
use App\Http\Resources\Api\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $posts = Post::with(['user:id,name', 'relatedMedia'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => PostResource::collection($posts->items()),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'from' => $posts->firstItem(),
                'to' => $posts->lastItem(),
            ],
            'links' => [
                'first' => $posts->url(1),
                'last' => $posts->url($posts->lastPage()),
                'prev' => $posts->previousPageUrl(),
                'next' => $posts->nextPageUrl(),
            ]
        ]);
    }

    public function show(Post $post): JsonResponse
    {
        $this->authorize('read-posts', 'web');

        $post->load(['user:id,name', 'relatedMedia']);

        return response()->json([
            'data' => new PostResource($post)
        ]);
    }

    public function store(PostRequest $request): JsonResponse
    {
        $this->authorize('create-posts', 'web');

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => auth('sanctum')->id(),
        ]);

        $post->load(['user:id,name']);

        return response()->json([
            'data' => new PostResource($post),
            'message' => 'Пост успешно создан'
        ], Response::HTTP_CREATED);
    }

    public function update(PostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('edit-posts', 'web');

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        $post->load(['user:id,name']);

        return response()->json([
            'data' => new PostResource($post),
            'message' => 'Пост успешно обновлен'
        ]);
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete-posts', 'web');

        $post->delete();

        return response()->json([
            'message' => 'Пост успешно удален'
        ], Response::HTTP_NO_CONTENT);
    }

    public function getUserPosts(User $user, Request $request): JsonResponse
    {
        $this->authorize('read-posts', 'web');

        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $posts = Post::where('user_id', $user->id)
            ->with(['user:id,name', 'relatedMedia'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => PostResource::collection($posts->items()),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'from' => $posts->firstItem(),
                'to' => $posts->lastItem(),
            ],
            'links' => [
                'first' => $posts->url(1),
                'last' => $posts->url($posts->lastPage()),
                'prev' => $posts->previousPageUrl(),
                'next' => $posts->nextPageUrl(),
            ]
        ]);
    }
}
