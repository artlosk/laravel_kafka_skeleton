<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('notify_type', ['role', 'user'])->unique()->comment('Тип уведомления: по роли или конкретному пользователю');
            $table->json('role_names')->nullable()->comment('Массив названий ролей (если notify_type = role)');
            $table->json('user_ids')->nullable()->comment('Массив ID пользователей (если notify_type = user)');
            $table->boolean('is_active')->default(true)->comment('Активна ли настройка');
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_notification_settings');
    }
};

