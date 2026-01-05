@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-user-plus mr-2"></i>
            Создание пользователя
        </h1>
        <div class="d-flex">
            <a href="{{ route('backend.users.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Назад к списку
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-plus mr-2"></i>
                Новая учетная запись
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('backend.users.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label" form-label class="form-label">
                                <i class="fas fa-user mr-1"></i> Имя пользователя
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="Введите имя пользователя" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label" form-label class="form-label">
                                <i class="fas fa-envelope mr-1"></i> Email адрес
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}"
                                   placeholder="user@example.com" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label" form-label class="form-label">
                                <i class="fas fa-lock mr-1"></i> Пароль
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password"
                                   placeholder="Минимум 8 символов" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label" form-label class="form-label">
                                <i class="fas fa-lock mr-1"></i> Подтверждение пароля
                            </label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation"
                                   placeholder="Повторите пароль" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-user-tag mr-1"></i> Роли пользователя
                    </label>
                    <div class="row">
                        @foreach($roles as $role)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="role_{{ $role->id }}" name="roles[]"
                                           value="{{ $role->name }}"
                                           @if(old('roles') && in_array($role->name, old('roles'))) checked @endif>
                                    <label class="form-check-label" for="role_{{ $role->id }}" class="form-label">
                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('roles')
                    <div class="text-danger mt-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Создать пользователя
                    </button>
                    <a href="{{ route('backend.users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
