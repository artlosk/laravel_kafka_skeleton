@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-shield-alt mr-2"></i>
            Просмотр разрешения: {{ $permission->name }}
        </h1>
        <div class="d-flex">
            @can('edit-permissions')
                <a href="{{ route('backend.permissions.edit', $permission) }}"
                   class="btn btn-outline-warning btn-sm me-2">
                    <i class="fas fa-edit"></i> Редактировать
                </a>
            @endcan
            <a href="{{ route('backend.permissions.index') }}" class="btn btn-outline-secondary btn-sm">
                Назад к списку
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-shield-alt mr-2"></i>
                {{ $permission->name }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-tag text-primary mr-2"></i>
                        <strong>Название разрешения:</strong>
                        <span class="badge bg-secondary fs-6 ms-2">{{ $permission->name }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-user-tag text-primary mr-2"></i>
                        <strong>Ролей:</strong>
                        <span class="badge bg-primary ms-2">{{ $permission->roles->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-users text-primary mr-2"></i>
                        <strong>Пользователей:</strong>
                        <span class="badge bg-info ms-2">{{ $permission->users->count() }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-calendar text-primary mr-2"></i>
                        <strong>Создано:</strong>
                        <span class="ms-2">{{ $permission->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>

            @if($permission->roles->count() > 0)
                <div class="mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-user-tag mr-2"></i>
                        Роли с этим разрешением ({{ $permission->roles->count() }})
                    </h5>
                    <div class="row">
                        @foreach($permission->roles as $role)
                            <div class="col-md-4 col-lg-3 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary fs-6 me-2">{{ $role->name }}</span>
                                    <small class="text-muted">({{ $role->users->count() }} пользователей)</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    Это разрешение не назначено ни одной роли
                </div>
            @endif

            @if($permission->users->count() > 0)
                <div class="mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-users mr-2"></i>
                        Пользователи с этим разрешением ({{ $permission->users->count() }})
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-user mr-1"></i> Имя</th>
                                <th><i class="fas fa-envelope mr-1"></i> Email</th>
                                <th><i class="fas fa-user-tag mr-1"></i> Роли</th>
                                <th><i class="fas fa-calendar mr-1"></i> Создан</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($permission->users as $user)
                                <tr>
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                            {{ $user->email }}
                                        </a>
                                    </td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $user->created_at->format('d.m.Y H:i') }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    Нет пользователей с этим разрешением
                </div>
            @endif

            <div class="d-flex gap-2">
                @can('edit-permissions')
                    <a href="{{ route('backend.permissions.edit', $permission) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Редактировать
                    </a>
                @endcan
                @can('delete-permissions')
                    <form action="{{ route('backend.permissions.destroy', $permission) }}" method="POST"
                          class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Вы уверены, что хотите удалить это разрешение?')">
                            <i class="fas fa-trash"></i> Удалить
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
@endsection
