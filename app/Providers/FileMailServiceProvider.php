<?php

namespace App\Providers;

use App\Mail\Transport\FileTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class FileMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Mail::extend('file', function () {
            return new FileTransport();
        });
    }
}

