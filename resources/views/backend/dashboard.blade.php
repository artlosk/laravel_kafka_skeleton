@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-tachometer-alt mr-2"></i>
            Dashboard
        </h1>
        <div class="d-flex">
            <button class="btn btn-primary btn-sm" onclick="refreshStats()">
                <i class="fas fa-sync"></i> Обновить
            </button>
        </div>
    </div>
@stop

@section('admin_content')

    @php
        // Проверяем просроченные и скоро истекающие токены
        $expiredTokens = \App\Models\User::whereHas('tokens', function($query) {
            $query->where('expires_at', '<', now());
        })->count();

        $expiringSoonTokens = \App\Models\User::whereHas('tokens', function($query) {
            $query->where('expires_at', '>', now())
                  ->where('expires_at', '<=', now()->addDays(7));
        })->count();
    @endphp

    @if($expiredTokens > 0 || $expiringSoonTokens > 0)
        <div class="row mb-4">
            <div class="col-12">
                @if($expiredTokens > 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Внимание!</strong>
                        У {{ $expiredTokens }} {{ $expiredTokens == 1 ? 'пользователя' : 'пользователей' }} просрочены
                        API токены.
                        <a href="{{ route('backend.users.index') }}" class="alert-link">Управление пользователями</a>
                    </div>
                @endif

                @if($expiringSoonTokens > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-clock"></i>
                        <strong>Предупреждение!</strong>
                        У {{ $expiringSoonTokens }} {{ $expiringSoonTokens == 1 ? 'пользователя' : 'пользователей' }}
                        API токены истекают в течение недели.
                        <a href="{{ route('backend.users.index') }}" class="alert-link">Управление пользователями</a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Статистические карточки -->
        <div class="col-lg-3 col-6">
            <div class="stats-card bg-primary" onclick="window.location.href='{{ route('backend.users.index') }}'">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-value">{{ \App\Models\User::count() }}</div>
                        <div class="stats-title">Пользователи</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="stats-card bg-success" onclick="window.location.href='{{ route('backend.posts.index') }}'">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-value">{{ \App\Models\Post::count() }}</div>
                        <div class="stats-title">Посты</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="stats-card bg-info" onclick="window.location.href='{{ route('backend.roles.index') }}'">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-value">{{ \Spatie\Permission\Models\Role::count() }}</div>
                        <div class="stats-title">Роли</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="stats-card bg-warning"
                 onclick="window.location.href='{{ route('backend.permissions.index') }}'">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-value">{{ \Spatie\Permission\Models\Permission::count() }}</div>
                        <div class="stats-title">Права</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-key"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Последние посты -->
        <div class="col-lg-8">
            <div class="card fade-in-up">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-2"></i>
                        Последние посты
                    </h3>
                </div>
                <div class="card-body">
                    @if(isset($recentPosts) && $recentPosts && $recentPosts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Заголовок</th>
                                    <th>Автор</th>
                                    <th>Дата создания</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(isset($recentPosts) ? $recentPosts : [] as $post)
                                    <tr>
                                        <td>
                                            <strong>{{ Str::limit($post->title, 50) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $post->user->name }}</span>
                                        </td>
                                        <td>
                                            <small
                                                class="text-muted">{{ $post->created_at->format('d.m.Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('backend.posts.show', $post) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('backend.posts.edit', $post) }}"
                                               class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Пока нет постов</p>
                            <a href="{{ route('backend.posts.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Создать первый пост
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Быстрые действия -->
        <div class="col-lg-4">
            <div class="card fade-in-up">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-2"></i>
                        Быстрые действия
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('backend.posts.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Создать пост
                        </a>
                        <a href="{{ route('backend.users.create') }}" class="btn btn-success">
                            <i class="fas fa-user-plus mr-2"></i>Добавить пользователя
                        </a>
                        <a href="{{ route('backend.roles.create') }}" class="btn btn-info">
                            <i class="fas fa-user-shield mr-2"></i>Создать роль
                        </a>
                        <a href="{{ route('backend.permissions.create') }}" class="btn btn-warning">
                            <i class="fas fa-key mr-2"></i>Добавить право
                        </a>
                    </div>
                </div>
            </div>

            <!-- Системная информация -->
            <div class="card fade-in-up mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        Системная информация
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h4 mb-0 text-primary">{{ PHP_VERSION }}</div>
                                <small class="text-muted">PHP Version</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 text-success">{{ app()->version() }}</div>
                            <small class="text-muted">Laravel Version</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        // Функция обновления статистики
        function refreshStats() {
            location.reload();
        }

        // Анимация появления карточек
        document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('.fade-in-up');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
@endpush
