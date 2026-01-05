@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-lock mr-2"></i>
            Смена пароля
        </h1>
        <div class="d-flex">
            <a href="{{ route('backend.profile') }}" class="btn btn-outline-secondary btn-sm">
                Назад к профилю
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-lock mr-2"></i>
                Обновление пароля
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

            <form action="{{ route('backend.password') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="current_password" class="form-label" form-label class="form-label">
                                <i class="fas fa-lock mr-1"></i> Текущий пароль
                            </label>
                            <input type="password" name="current_password" id="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Введите текущий пароль" required>
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label" form-label class="form-label">
                                <i class="fas fa-lock mr-1"></i> Новый пароль
                            </label>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Минимум 8 символов" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label" form-label class="form-label">
                                <i class="fas fa-lock mr-1"></i> Подтверждение нового пароля
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control"
                                   placeholder="Повторите новый пароль" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save"></i> Обновить пароль
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
