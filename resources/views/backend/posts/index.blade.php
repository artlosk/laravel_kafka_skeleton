@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-newspaper mr-2"></i>
            Посты
        </h1>
        <div class="d-flex">
            @can('create-posts')
                <a href="{{ route('backend.posts.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Создать пост
                </a>
            @endcan
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list mr-2"></i>
                Список постов
            </h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                    <tr>
                        <th><i class="fas fa-heading mr-1"></i> Заголовок</th>
                        <th><i class="fas fa-user mr-1"></i> Автор</th>
                        <th><i class="fas fa-calendar mr-1"></i> Создан</th>
                        <th><i class="fas fa-cogs mr-1"></i> Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td>
                                @can('read-posts')
                                    <a href="{{ route('backend.posts.show', $post) }}" class="text-decoration-none">
                                        {{ $post->title }}
                                    </a>
                                @else
                                    {{ $post->title }}
                                @endcan
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $post->user->name }}</span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $post->created_at->format('d.m.Y H:i') }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @can('read-posts')
                                        <a href="{{ route('backend.posts.show', $post) }}"
                                           class="btn btn-outline-info btn-sm"
                                           title="Просмотр">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan
                                    @can('edit-posts')
                                        <a href="{{ route('backend.posts.edit', $post) }}"
                                           class="btn btn-outline-warning btn-sm"
                                           title="Редактировать">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('delete-posts')
                                        <form action="{{ route('backend.posts.delete', $post) }}" method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Вы уверены, что хотите удалить этот пост?')"
                                                    title="Удалить">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <br>
                                Посты не найдены
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
