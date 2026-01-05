@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-user-tag mr-2"></i>
            Роли
        </h1>
        <div class="d-flex">
            @can('create-roles')
                <a href="{{ route('backend.roles.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Создать роль
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
                Список ролей
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
                        <th><i class="fas fa-tag mr-1"></i> Название</th>
                        <th><i class="fas fa-users mr-1"></i> Пользователей</th>
                        <th><i class="fas fa-shield-alt mr-1"></i> Разрешений</th>
                        <th><i class="fas fa-cogs mr-1"></i> Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>
                                <span class="badge bg-primary fs-6">{{ $role->name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $role->users_count ?? $role->users->count() }}</span>
                            </td>
                            <td>
                                <span
                                    class="badge bg-success">{{ $role->permissions_count ?? $role->permissions->count() }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @can('view-roles')
                                        <a href="{{ route('backend.roles.show', $role) }}"
                                           class="btn btn-outline-info btn-sm"
                                           title="Просмотр">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan
                                    @can('edit-roles')
                                        <a href="{{ route('backend.roles.edit', $role) }}"
                                           class="btn btn-outline-warning btn-sm"
                                           title="Редактировать">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('delete-roles')
                                        <form action="{{ route('backend.roles.destroy', $role) }}" method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Вы уверены, что хотите удалить эту роль?')"
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
                                <i class="fas fa-user-tag fa-2x mb-2"></i>
                                <br>
                                Роли не найдены
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($roles->hasPages())
                <div class="mt-3">
                    {{ $roles->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
