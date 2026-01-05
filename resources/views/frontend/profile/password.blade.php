@extends('layouts.app')

@section('content')
    <div class="py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-danger text-white text-center">
                        <h3 class="mb-0">Смена пароля</h3>
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

                        <form method="POST" action="{{ route('frontend.password') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="current_password" class="form-label"><i class="bi bi-lock me-2"></i>Текущий
                                    пароль</label>
                                <input type="password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       id="current_password" name="current_password" required>
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label"><i class="bi bi-lock-fill me-2"></i>Новый
                                    пароль</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label"><i
                                        class="bi bi-lock-fill me-2"></i>Подтверждение нового пароля</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                       name="password_confirmation" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-danger me-2">
                                    <i class="bi bi-save me-2"></i>Обновить пароль
                                </button>
                                <a href="{{ route('frontend.profile') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Отмена
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
    </style>
@endsection
