@extends('layouts.app')

@section('content')
    <div class="py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">Редактирование профиля</h3>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('frontend.profile') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label"><i class="bi bi-person me-2"></i>Имя</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                       name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label"><i class="bi bi-envelope me-2"></i>Email
                                    адрес</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                       name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- API Token Section -->
                            <div class="mb-4">
                                <hr class="my-4">
                                <h5 class="mb-3">
                                    <i class="fas fa-key me-2"></i>API Token
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
                                        <form action="{{ route('frontend.profile.generate-token') }}" method="POST"
                                              class="d-inline me-2">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning btn-sm"
                                                    onclick="return confirm('Это создаст новый токен и удалит старый. Продолжить?')">
                                                <i class="fas fa-sync-alt"></i> Обновить токен
                                            </button>
                                        </form>
                                        <form action="{{ route('frontend.profile.revoke-token') }}" method="POST"
                                              class="d-inline me-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Это отзовет ваш API токен. Продолжить?')">
                                                <i class="fas fa-trash-alt"></i> Удалить токен
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('frontend.profile.generate-token') }}" method="POST"
                                              class="d-inline me-2">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-plus-circle"></i> Создать токен
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-save me-2"></i>Обновить профиль
                                </button>
                                <a href="{{ route('frontend.dashboard') }}" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-x-circle me-2"></i>Отмена
                                </a>
                                <a href="{{ route('frontend.password') }}" class="btn btn-link">Изменить пароль</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>

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
