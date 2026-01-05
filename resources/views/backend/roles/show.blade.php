@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-user-tag mr-2"></i>
            Просмотр роли: {{ $role->name }}
        </h1>
        <div class="d-flex">
            @can('edit-roles')
                <a href="{{ route('backend.roles.edit', $role) }}" class="btn btn-outline-warning btn-sm me-2">
                    <i class="fas fa-edit"></i> Редактировать
                </a>
            @endcan
            <a href="{{ route('backend.roles.index') }}" class="btn btn-outline-secondary btn-sm">
                Назад к списку
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-tag mr-2"></i>
                {{ $role->name }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-tag text-primary mr-2"></i>
                        <strong>Название роли:</strong>
                        <span class="badge bg-primary fs-6 ms-2">{{ $role->name }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-users text-primary mr-2"></i>
                        <strong>Пользователей:</strong>
                        <span class="badge bg-info ms-2">{{ $role->users->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Разрешения ({{ $role->permissions->count() }})
                </h5>
                @if($role->permissions->count() > 0)
                    <div class="row">
                        @foreach($role->permissions as $permission)
                            <div class="col-md-4 col-lg-3 mb-2">
                                <span class="badge bg-secondary fs-6">{{ $permission->name }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        У этой роли нет назначенных разрешений
                    </div>
                @endif
            </div>

            @if($role->users->count() > 0)
                <div class="mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-users mr-2"></i>
                        Пользователи с этой ролью ({{ $role->users->count() }})
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-user mr-1"></i> Имя</th>
                                <th><i class="fas fa-envelope mr-1"></i> Email</th>
                                <th><i class="fas fa-calendar mr-1"></i> Создан</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($role->users as $user)
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
                    Нет пользователей с этой ролью
                </div>
            @endif

            <div class="d-flex gap-2">
                @can('edit-roles')
                    <a href="{{ route('backend.roles.edit', $role) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Редактировать
                    </a>
                @endcan
                @can('delete-roles')
                    <form action="{{ route('backend.roles.destroy', $role) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Вы уверены, что хотите удалить эту роль?')">
                            <i class="fas fa-trash"></i> Удалить
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
@endsection
