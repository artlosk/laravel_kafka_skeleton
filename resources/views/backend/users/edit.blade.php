@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-user-edit mr-2"></i>
            Редактирование пользователя: {{ $user->name }}
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
                <i class="fas fa-user-edit mr-2"></i>
                Редактирование учетной записи
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('backend.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label" form-label class="form-label">
                                <i class="fas fa-user mr-1"></i> Имя пользователя
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $user->name) }}"
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
                                   id="email" name="email" value="{{ old('email', $user->email) }}"
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
                                <i class="fas fa-lock mr-1"></i> Новый пароль
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password"
                                   placeholder="Оставьте пустым, чтобы не менять">
                            <div class="form-text">
                                <i class="fas fa-info-circle mr-1"></i>
                                Оставьте поле пустым, чтобы сохранить текущий пароль
                            </div>
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
                                   placeholder="Повторите новый пароль">
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
                                           @if(in_array($role->name, $userRoles) ||
                                              (old('roles') && in_array($role->name, old('roles')))) checked @endif>
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

                <!-- API Token Section -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-key mr-1"></i> API Токен
                    </label>
                    <div class="input-group">
                        @if($user->hasApiToken())
                            @php
                                $tokenInfo = $user->getApiTokenInfo();
                                $lastToken = $user->getLastApiToken();
                            @endphp
                            <input type="text" class="form-control {{ $tokenInfo['is_expired'] ? 'is-invalid' : '' }}"
                                   value="{{ $lastToken->token }}"
                                   readonly
                                   id="api-token">
                            <button class="btn btn-outline-primary btn-sm" type="button"
                                    onclick="copyToClipboard('api-token')">
                                <i class="fas fa-copy"></i> Копировать
                            </button>
                        @else
                            <input type="text" class="form-control"
                                   value="No API token"
                                   readonly>
                        @endif
                    </div>

                    @if($user->hasApiToken())
                        @php $tokenInfo = $user->getApiTokenInfo(); @endphp

                        @if($tokenInfo['is_expired'])
                            <div class="alert alert-danger mt-2">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Токен просрочен!</strong>
                                Создан: {{ $tokenInfo['created_at']->format('d.m.Y H:i') }}
                            </div>
                        @elseif($tokenInfo['expires_at'])
                            @php $daysLeft = $tokenInfo['days_until_expiry']; @endphp
                            @if($daysLeft <= 7 && $daysLeft > 0)
                                <div class="alert alert-warning mt-2">
                                    <i class="fas fa-clock"></i>
                                    <strong>Токен скоро истечет!</strong>
                                    Осталось дней: {{ $daysLeft }}
                                </div>
                            @endif

                            <small class="form-text text-muted">
                                <i class="fas fa-calendar"></i>
                                Создан: {{ $tokenInfo['created_at']->format('d.m.Y H:i') }} |
                                Истекает: {{ $tokenInfo['expires_at']->format('d.m.Y H:i') }}
                                @if($daysLeft > 0)
                                    (через {{ $daysLeft }} {{ $daysLeft == 1 ? 'день' : ($daysLeft < 5 ? 'дня' : 'дней') }}
                                    )
                                @endif
                            </small>
                        @else
                            <small class="form-text text-muted">
                                <i class="fas fa-infinity"></i>
                                Создан: {{ $tokenInfo['created_at']->format('d.m.Y H:i') }} |
                                <strong>Бессрочный токен</strong>
                            </small>
                        @endif
                    @else
                        <small class="form-text text-muted">
                            <i class="fas fa-ban"></i>
                            У пользователя нет API токена
                        </small>
                    @endif
                </div>

                <div class="mb-3">
                    <div class="d-flex gap-2 flex-wrap">
                        @if($user->hasApiToken())
                            <form action="{{ route('backend.users.generate-api-token', $user) }}" method="POST"
                                  class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning btn-sm"
                                        onclick="return confirm('Это создаст новый токен и удалит старый. Продолжить?')">
                                    <i class="fas fa-sync-alt"></i> Обновить токен
                                </button>
                            </form>
                            <form action="{{ route('backend.users.revoke-api-token', $user) }}" method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Это отзовет все API токены пользователя. Продолжить?')">
                                    <i class="fas fa-trash-alt"></i> Удалить токен
                                </button>
                            </form>
                        @else
                            <form action="{{ route('backend.users.generate-api-token', $user) }}" method="POST"
                                  class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-plus-circle"></i> Создать токен
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Сохранить изменения
                    </button>
                    <a href="{{ route('backend.users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- JavaScript вынесен в отдельный файл resources/js/backend/users.js --}}
@endsection
