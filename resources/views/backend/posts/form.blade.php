@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-{{ isset($post) ? 'edit' : 'plus' }} mr-2"></i>
            {{ isset($post) ? 'Редактирование поста' : 'Создание поста' }}
        </h1>
        <div class="d-flex">
            <a href="{{ route('backend.posts.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Назад к списку
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-newspaper mr-2"></i>
                {{ isset($post) ? 'Редактирование поста' : 'Новый пост' }}
            </h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Ошибки валидации:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form id="postForm"
                  action="{{ isset($post) ? route('backend.posts.update', $post) : route('backend.posts.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($post))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="title" class="form-label" form-label class="form-label">
                        <i class="fas fa-heading mr-1"></i> Заголовок поста
                    </label>
                    <input type="text" name="title" id="title"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $post->title ?? '') }}"
                           placeholder="Введите заголовок поста" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label" form-label class="form-label">
                        <i class="fas fa-align-left mr-1"></i> Содержимое поста
                    </label>
                    <textarea name="content" id="content"
                              class="form-control @error('content') is-invalid @enderror"
                              rows="8"
                              placeholder="Введите содержимое поста"
                              required>{{ old('content', $post->content ?? '') }}</textarea>
                    @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-images mr-1"></i> Медиафайлы
                    </label>
                    <x-backend.media-gallery name="media" :initialMedia="$post->relatedMedia ?? []"/>
                    @error('media.*')
                    <div class="text-danger mt-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save"></i> {{ isset($post) ? 'Сохранить изменения' : 'Создать пост' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
