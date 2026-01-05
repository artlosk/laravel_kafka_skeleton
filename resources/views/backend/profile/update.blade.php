@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-user-edit mr-2"></i>
            Настройки профиля
        </h1>
        <div class="d-flex">
            <a href="{{ route('backend.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                Назад к панели
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-edit mr-2"></i>
                Редактирование профиля
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

            <form action="{{ route('backend.profile') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label" form-label class="form-label">
                                <i class="fas fa-user mr-1"></i> Имя пользователя
                            </label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="Введите ваше имя" required>
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
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="your@email.com" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- API Token Section -->
                <div class="mb-4">
                    <hr class="my-4">
                    <h5 class="mb-3">
                        <i class="fas fa-key mr-2"></i>API Token
                    </h5>

                    <div class="mb-3">
                        <label class="form-label">Ваш API токен</label>
                        <div class="input-group">
                            @if($user->hasApiToken())
                                @php
                                    $tokenInfo = $user->getApiTokenInfo();
                                    $lastToken = $user->getLastApiToken();
                                @endphp
                                <input type="text"
                                       class="form-control {{ $tokenInfo['is_expired'] ? 'is-invalid' : '' }}"
                                       value="{{ $lastToken->token }}"
                                       readonly
                                       id="api-token">
                                <button class="btn btn-outline-primary" type="button"
                                        onclick="copyToClipboard('api-token')">
                                    <i class="fas fa-copy"></i> Копировать
                                </button>
                            @else
                                <input type="text" class="form-control"
                                       value="У вас нет API токена"
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
                                У вас нет API токена
                            </small>
                        @endif
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        @if($user->hasApiToken())
                            <form action="{{ route('backend.profile.generate-token') }}" method="POST"
                                  class="d-inline me-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning btn-sm"
                                        onclick="return confirm('Это создаст новый токен и удалит старый. Продолжить?')">
                                    <i class="fas fa-sync-alt"></i> Обновить токен
                                </button>
                            </form>
                            <form action="{{ route('backend.profile.revoke-token') }}" method="POST"
                                  class="d-inline me-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Это отзовет ваш API токен. Продолжить?')">
                                    <i class="fas fa-trash-alt"></i> Удалить токен
                                </button>
                            </form>
                        @else
                            <form action="{{ route('backend.profile.generate-token') }}" method="POST"
                                  class="d-inline me-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-plus-circle"></i> Создать токен
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save"></i> Сохранить изменения
                    </button>
                    <a href="{{ route('backend.password') }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-lock"></i> Изменить пароль
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            element.select();
            element.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand('copy');

            // Show feedback
            const button = element.nextElementSibling;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Скопировано!';
            button.classList.add('btn-success');
            button.classList.remove('btn-outline-primary');

            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-primary');
            }, 2000);
        }
    </script>
@endsection
