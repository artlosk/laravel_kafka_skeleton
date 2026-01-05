@extends('layouts.app')

@section('content')
    <div class="py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-dark">All Posts</h1>
            <p class="lead text-muted">Explore the latest posts from our community.</p>
        </div>

        @if ($posts->isEmpty())
            <div class="alert alert-info text-center">
                No posts available yet.
            </div>
        @else
            <div class="row g-4">
                @foreach ($posts as $post)
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">{{ $post->title }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($post->content, 100) }}</p>
                                <a href="{{ route('frontend.posts.show', $post) }}" class="btn btn-primary">Read
                                    More</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
