@extends('layouts.app')

@section('content')
    <div class="py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">{{ $post->title }}</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Posted by {{ $post->user->name }}
                            on {{ $post->created_at->format('M d, Y') }}</p>
                        <div class="content">
                            {!! nl2br(e($post->content)) !!}
                        </div>
                        <a href="{{ route('frontend.posts.index') }}" class="btn btn-outline-primary mt-3">Back to
                            Posts</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
