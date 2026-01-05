<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        $this->loadViewComponentsAs('', [resource_path('views/backend/components')]);

        \Illuminate\Pagination\Paginator::defaultView('pagination::bootstrap-5');
        \Illuminate\Pagination\Paginator::defaultSimpleView('pagination::simple-bootstrap-5');
        \Illuminate\Support\Facades\App::setLocale('ru');
    }
}
