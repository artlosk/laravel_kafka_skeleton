@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-user-tag mr-2"></i>
            Редактирование роли: {{ $role->name }}
        </h1>
        <div class="d-flex">
            <a href="{{ route('backend.roles.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Назад к списку
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-tag mr-2"></i>
                Редактирование роли
            </h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Ошибки валидации:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('backend.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label" form-label class="form-label">
                        <i class="fas fa-tag mr-1"></i> Название роли
                    </label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $role->name) }}"
                           placeholder="Введите название роли" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-shield-alt mr-1"></i> Разрешения
                    </label>
                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-md-4 col-lg-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="permissions[]"
                                           value="{{ $permission->name }}"
                                           id="permission_{{ $permission->id }}"
                                        {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_{{ $permission->id }}"
                                           class="form-label">
                                        <span class="badge bg-secondary">{{ $permission->name }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('permissions')
                    <div class="text-danger mt-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save"></i> Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
