@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-eye mr-2"></i>
            Просмотр поста: {{ $post->title }}
        </h1>
        <div class="d-flex">
            @can('edit-posts')
                <a href="{{ route('backend.posts.edit', $post) }}" class="btn btn-outline-warning btn-sm me-2">
                    <i class="fas fa-edit"></i> Редактировать
                </a>
            @endcan
            <a href="{{ route('backend.posts.index') }}" class="btn btn-outline-secondary btn-sm">
                Назад к списку
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-newspaper mr-2"></i>
                {{ $post->title }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-user text-primary mr-2"></i>
                        <strong>Автор:</strong>
                        <span class="badge bg-info ms-2">{{ $post->user->name }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-calendar text-primary mr-2"></i>
                        <strong>Создан:</strong>
                        <span class="ms-2">{{ $post->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-align-left mr-2"></i>
                    Содержимое поста
                </h5>
                <div class="border rounded p-3 bg-light">
                    <p class="mb-0">{{ $post->content }}</p>
                </div>
            </div>

            @if($post->relatedMedia->count() > 0)
                <div class="mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-images mr-2"></i>
                        Медиафайлы ({{ $post->relatedMedia->count() }})
                    </h5>
                    <div class="row">
                        @foreach($post->relatedMedia as $media)
                            <div class="col-md-4 col-lg-3 mb-3">
                                <div class="card">
                                    <div class="card-body p-2 text-center">
                                        @if($media->mime_type && str_starts_with($media->mime_type, 'image/'))
                                            <img
                                                src="{{ $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl() }}"
                                                class="img-fluid rounded"
                                                alt="{{ $media->name }}"
                                                style="max-height: 150px; object-fit: cover;">
                                        @else
                                            <i class="fas fa-file fa-3x text-muted"></i>
                                        @endif
                                    </div>
                                    <div class="card-footer p-2">
                                        <small class="text-muted d-block text-truncate" title="{{ $media->name }}">
                                            {{ $media->name }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock text-primary mr-2"></i>
                        <strong>Обновлен:</strong>
                        <span class="ms-2">{{ $post->updated_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-images text-primary mr-2"></i>
                        <strong>Медиафайлов:</strong>
                        <span class="badge bg-secondary ms-2">{{ $post->relatedMedia->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                @can('edit-posts')
                    <a href="{{ route('backend.posts.edit', $post) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Редактировать
                    </a>
                @endcan
                @can('delete-posts')
                    <form action="{{ route('backend.posts.delete', $post) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Вы уверены, что хотите удалить этот пост?')">
                            <i class="fas fa-trash"></i> Удалить
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
@endsection
