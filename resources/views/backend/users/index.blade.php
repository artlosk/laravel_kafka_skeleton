@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-users mr-2"></i>
            Пользователи
        </h1>
        <div class="d-flex">
            @can('create-users')
                <a href="{{ route('backend.users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus"></i> Создать пользователя
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
                Список пользователей
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

            {{-- Фильтры и поиск --}}
            <div class="mb-3">
                <form method="GET" action="{{ route('backend.users.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Поиск</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Имя или Email">
                    </div>
                    <div class="col-md-3">
                        <label for="role" class="form-label">Роль</label>
                        <select class="form-control" id="role" name="role">
                            <option value="">Все роли</option>
                            @foreach($roles as $role)
                                <option
                                    value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Применить
                        </button>
                        @if(request('search') || request('role'))
                            <a href="{{ route('backend.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Сбросить
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                    <tr>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') === 'asc' && request('sort') === 'id' ? 'desc' : 'asc']) }}"
                               class="text-decoration-none text-dark">
                                <i class="fas fa-hashtag mr-1"></i> ID
                                @if(request('sort') === 'id')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort text-muted"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') === 'asc' && request('sort') === 'name' ? 'desc' : 'asc']) }}"
                               class="text-decoration-none text-dark">
                                <i class="fas fa-user mr-1"></i> Имя
                                @if(request('sort') === 'name')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort text-muted"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => request('direction') === 'asc' && request('sort') === 'email' ? 'desc' : 'asc']) }}"
                               class="text-decoration-none text-dark">
                                <i class="fas fa-envelope mr-1"></i> Email
                                @if(request('sort') === 'email')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort text-muted"></i>
                                @endif
                            </a>
                        </th>
                        <th><i class="fas fa-user-tag mr-1"></i> Роли</th>
                        <th><i class="fas fa-key mr-1"></i> API Токен</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' && request('sort') === 'created_at' ? 'desc' : 'asc']) }}"
                               class="text-decoration-none text-dark">
                                <i class="fas fa-calendar mr-1"></i> Создан
                                @if(request('sort') === 'created_at' || !request('sort'))
                                    <i class="fas fa-sort-{{ (request('direction') === 'asc' && request('sort') === 'created_at') ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort text-muted"></i>
                                @endif
                            </a>
                        </th>
                        <th><i class="fas fa-cogs mr-1"></i> Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $user->id }}</span>
                            </td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td>
                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                    {{ $user->email }}
                                </a>
                            </td>
                            <td>
                                @forelse($user->roles as $role)
                                    <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">Нет ролей</span>
                                @endforelse
                            </td>
                            <td>
                                @if($user->hasApiToken())
                                    @php $tokenInfo = $user->getApiTokenInfo(); @endphp

                                    @if($tokenInfo['is_expired'])
                                        <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Просрочен
                                            </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ $tokenInfo['created_at']->format('d.m.Y') }}
                                        </small>
                                    @elseif($tokenInfo['expires_at'])
                                        @php $daysLeft = $tokenInfo['days_until_expiry']; @endphp
                                        @if($daysLeft <= 7 && $daysLeft > 0)
                                            <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i> Истекает скоро
                                                </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $daysLeft }} {{ $daysLeft == 1 ? 'день' : ($daysLeft < 5 ? 'дня' : 'дней') }}
                                            </small>
                                        @else
                                            <span class="badge bg-success">
                                                    <i class="fas fa-key"></i> Активен
                                                </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $tokenInfo['created_at']->format('d.m.Y') }}
                                            </small>
                                        @endif
                                    @else
                                        <span class="badge bg-info">
                                                <i class="fas fa-infinity"></i> Бессрочный
                                            </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ $tokenInfo['created_at']->format('d.m.Y') }}
                                        </small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">
                                            <i class="fas fa-ban"></i> Нет токена
                                        </span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $user->created_at->format('d.m.Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @can('edit-users')
                                        <a href="{{ route('backend.users.edit', $user) }}"
                                           class="btn btn-outline-warning btn-sm"
                                           title="Редактировать">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('delete-users')
                                        <form action="{{ route('backend.users.destroy', $user) }}" method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?')"
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
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <br>
                                Пользователи не найдены
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="mt-3">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
