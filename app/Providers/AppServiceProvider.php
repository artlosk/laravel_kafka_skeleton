<?php

namespace App\Providers;

use App\Queue\Connectors\KafkaConnector;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;

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

        // Регистрация коннектора Kafka
        Queue::extend('kafka', function () {
            return new KafkaConnector();
        });
    }
}
