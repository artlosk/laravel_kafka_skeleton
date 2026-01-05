<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AdminMenuServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('adminlte.menu', function () {
            return $this->buildSidebarMenu();
        });
    }

    public function boot()
    {
    }

    protected function buildSidebarMenu()
    {
        return [
            // Главная
            [
                'text' => 'Главная',
                'route' => 'backend.dashboard',
                'icon' => 'fas fa-home',
                'permissions' => ['access-admin-panel'],
            ],

            // Управление контентом
            [
                'text' => 'Посты',
                'route' => 'backend.posts.index',
                'icon' => 'fas fa-file-alt',
                'label' => \App\Models\Post::count(),
                'label_color' => 'info',
                'permissions' => ['view-posts'],
            ],

            // Управление медиа
            [
                'text' => 'Медиа',
                'route' => 'backend.media.index',
                'icon' => 'fas fa-images',
                'permissions' => ['view-media'],
            ],

            // Администрирование (только для админов)
            [
                'text' => 'Администрирование',
                'icon' => 'fas fa-cogs',
                'admin_only' => true,
                'submenu' => [
                    [
                        'text' => 'Роли',
                        'route' => 'backend.roles.index',
                        'icon' => 'fas fa-user-shield',
                        'label' => \Spatie\Permission\Models\Role::count(),
                        'label_color' => 'warning',
                        'permissions' => ['view-roles'],
                    ],
                    [
                        'text' => 'Права доступа',
                        'route' => 'backend.permissions.index',
                        'icon' => 'fas fa-key',
                        'label' => \Spatie\Permission\Models\Permission::count(),
                        'label_color' => 'danger',
                        'permissions' => ['view-permissions'],
                    ],
                    // Управление пользователями
                    [
                        'text' => 'Пользователи',
                        'route' => 'backend.users.index',
                        'icon' => 'fas fa-users',
                        'label' => \App\Models\User::count(),
                        'label_color' => 'success',
                        'permissions' => ['view-users'],
                    ],
                ],
            ],

            // Профиль пользователя
            [
                'text' => 'Профиль',
                'icon' => 'fas fa-user',
                'submenu' => [
                    [
                        'text' => 'Мой профиль',
                        'route' => 'backend.profile',
                        'icon' => 'fas fa-user',
                    ],
                    [
                        'text' => 'Смена пароля',
                        'route' => 'backend.password',
                        'icon' => 'fas fa-lock',
                    ],
                ],
            ],
        ];
    }
}
