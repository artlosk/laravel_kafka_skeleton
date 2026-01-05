@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-plus mr-2"></i>
            Добавить настройку уведомления
        </h1>
        <div class="d-flex">
            <a href="{{ route('backend.post-notification-settings.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Назад к списку
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-bell mr-2"></i>
                Новая настройка уведомления
            </h3>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Ошибки валидации:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('backend.post-notification-settings.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="notify_type">Тип уведомления <span class="text-danger">*</span></label>
                    <select name="notify_type" id="notify_type" class="form-control @error('notify_type') is-invalid @enderror" required>
                        <option value="">Выберите тип</option>
                        <option value="role" {{ old('notify_type') === 'role' ? 'selected' : '' }}>По роли</option>
                        <option value="user" {{ old('notify_type') === 'user' ? 'selected' : '' }}>Конкретному пользователю</option>
                    </select>
                    @error('notify_type')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">
                        Выберите, кому будут отправляться уведомления: всем пользователям с определенной ролью или конкретному пользователю
                    </small>
                </div>

                <div class="form-group" id="role_field" style="display: none;">
                    <label for="role_names">Роли <span class="text-danger">*</span></label>
                    <select name="role_names[]" id="role_names" class="form-control select2 @error('role_names') is-invalid @enderror" multiple>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ in_array($role->name, old('role_names', [])) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_names')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">
                        Все пользователи с выбранными ролями будут получать уведомления о новых постах. Можно выбрать несколько ролей.
                    </small>
                </div>

                <div class="form-group" id="user_field" style="display: none;">
                    <label for="user_ids">Пользователи <span class="text-danger">*</span></label>
                    <select name="user_ids[]" id="user_ids" class="form-control select2 @error('user_ids') is-invalid @enderror" multiple>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ in_array($user->id, old('user_ids', [])) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_ids')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">
                        Выбранные пользователи будут получать уведомления о новых постах. Можно выбрать несколько пользователей.
                    </small>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Активна</label>
                    </div>
                    <small class="form-text text-muted">
                        Неактивные настройки не будут использоваться при отправке уведомлений
                    </small>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Сохранить
                    </button>
                    <a href="{{ route('backend.post-notification-settings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/backend/post-notification-settings.js') }}"></script>
@endpush

