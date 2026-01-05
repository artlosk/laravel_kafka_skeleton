@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-shield-alt mr-2"></i>
            Создание разрешения
        </h1>
        <div class="d-flex">
            <a href="{{ route('backend.permissions.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Назад к списку
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-shield-alt mr-2"></i>
                Новое разрешение
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

            <form action="{{ route('backend.permissions.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label" form-label class="form-label">
                        <i class="fas fa-tag mr-1"></i> Название разрешения
                    </label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           placeholder="Например: create-posts, edit-users" required>
                    <div class="form-text">
                        <i class="fas fa-info-circle mr-1"></i>
                        Используйте kebab-case формат (например: create-posts, edit-users)
                    </div>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Создать разрешение
                    </button>
                    <a href="{{ route('backend.permissions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
