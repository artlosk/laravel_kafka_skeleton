@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-bell mr-2"></i>
            Настройки уведомлений о постах
        </h1>
        <div class="d-flex">
            <a href="{{ route('backend.post-notification-settings.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Добавить настройку
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list mr-2"></i>
                Список настроек
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
                        <th style="width: 10px">#</th>
                        <th><i class="fas fa-tag mr-1"></i> Тип</th>
                        <th><i class="fas fa-users mr-1"></i> Получатели</th>
                        <th style="width: 120px"><i class="fas fa-toggle-on mr-1"></i> Статус</th>
                        <th style="width: 150px"><i class="fas fa-calendar mr-1"></i> Создано</th>
                        <th style="width: 150px" class="text-center"><i class="fas fa-cogs mr-1"></i> Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($settings as $setting)
                        <tr>
                            <td>{{ $setting->id }}</td>
                            <td>
                                @if($setting->notify_type === 'role')
                                    <span class="badge bg-info">
                                        <i class="fas fa-users"></i> Роли
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        <i class="fas fa-user"></i> Пользователи
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($setting->notify_type === 'role')
                                    @if(!empty($setting->role_names))
                                        @foreach($setting->role_names as $roleName)
                                            <span class="badge bg-info">{{ $roleName }}</span>
                                        @endforeach
                                        <br>
                                        <small class="text-muted">
                                            {{ count($setting->role_names) }} 
                                            {{ trans_choice('роль|роли|ролей', count($setting->role_names)) }}
                                        </small>
                                    @else
                                        <span class="text-muted">Не указано</span>
                                    @endif
                                @else
                                    @if(!empty($setting->user_ids))
                                        @php
                                            $users = \App\Models\User::whereIn('id', $setting->user_ids)->get();
                                        @endphp
                                        @foreach($users->take(3) as $user)
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                <small class="text-muted">({{ $user->email }})</small>
                                            </div>
                                        @endforeach
                                        @if($users->count() > 3)
                                            <small class="text-muted">и еще {{ $users->count() - 3 }}...</small>
                                        @endif
                                        <br>
                                        <small class="text-muted">
                                            Всего: {{ count($setting->user_ids) }} 
                                            {{ trans_choice('пользователь|пользователя|пользователей', count($setting->user_ids)) }}
                                        </small>
                                    @else
                                        <span class="text-muted">Не указано</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('backend.post-notification-settings.toggle-active', $setting) }}"
                                      method="POST"
                                      style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="btn btn-sm btn-{{ $setting->is_active ? 'success' : 'secondary' }}"
                                            title="{{ $setting->is_active ? 'Деактивировать' : 'Активировать' }}">
                                        <i class="fas fa-{{ $setting->is_active ? 'check' : 'times' }}"></i>
                                        {{ $setting->is_active ? 'Активна' : 'Неактивна' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $setting->created_at->format('d.m.Y H:i') }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('backend.post-notification-settings.edit', $setting) }}"
                                       class="btn btn-outline-warning btn-sm"
                                       title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('backend.post-notification-settings.destroy', $setting) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Вы уверены, что хотите удалить эту настройку?')"
                                                title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <br>
                                Настройки уведомлений пока не добавлены
                                <br><br>
                                <a href="{{ route('backend.post-notification-settings.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Добавить первую настройку
                                </a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($settings->hasPages())
                <div class="mt-3">
                    {{ $settings->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="card card-info mt-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle mr-2"></i>
                Информация
            </h3>
        </div>
        <div class="card-body">
            <p><strong>Как это работает:</strong></p>
            <ul class="mb-0">
                <li>При создании нового поста система автоматически отправит email-уведомления всем настроенным получателям</li>
                <li><strong>Тип "Роли"</strong> - уведомления получат все пользователи с выбранными ролями (можно выбрать несколько)</li>
                <li><strong>Тип "Пользователи"</strong> - уведомления получат выбранные пользователи (можно выбрать несколько)</li>
                <li>Уведомления отправляются через очередь Kafka для оптимальной производительности</li>
                <li>Неактивные настройки не будут использоваться при отправке уведомлений</li>
                <li><strong>Важно:</strong> Может быть только одна настройка для ролей и одна для пользователей. При создании новой настройки того же типа, существующая будет обновлена</li>
            </ul>
        </div>
    </div>
@endsection

