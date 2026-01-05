# Kafka Guide

## Kafka UI

**URL**: http://localhost:8080

Веб-интерфейс для управления Kafka. После запуска контейнеров доступен автоматически.

**Возможности:**
- Просмотр всех топиков и их конфигурации
- Просмотр сообщений в топиках
- Мониторинг групп потребителей (consumer groups)
- Управление топиками (создание, удаление, изменение конфигурации)
- Просмотр метрик и статистики брокеров
- Отправка тестовых сообщений
- Просмотр схем данных (если используются)

**Навигация:**
- **Brokers** - информация о брокерах Kafka
- **Topics** - список всех топиков
- **Consumers** - группы потребителей
- **Messages** - просмотр и отправка сообщений

---

## Быстрый старт

### 1. Установка пакета

Для работы с Kafka в Laravel необходимо установить один из пакетов:

```bash
# Вариант 1: enqueue/rdkafka (рекомендуется)
./vendor/bin/sail composer require enqueue/rdkafka

# Вариант 2: nmred/kafka-php
./vendor/bin/sail composer require nmred/kafka-php
```

### 2. Настройка .env

Добавьте в `.env`:

```env
QUEUE_CONNECTION=kafka

KAFKA_BROKERS=kafka:29092
KAFKA_QUEUE=default
KAFKA_TOPIC=notifications
KAFKA_CONSUMER_GROUP=laravel-consumer

# Порты для Docker (опционально)
FORWARD_KAFKA_PORT=9092
FORWARD_ZOOKEEPER_PORT=2181
FORWARD_KAFKA_UI_PORT=8080
```

### 3. Запуск воркера

```bash
./vendor/bin/sail artisan queue:work kafka
```

---

## Основные команды

### Работа с очередями

```bash
# Запустить воркер
./vendor/bin/sail artisan queue:work kafka

# Запустить воркер для конкретной очереди
./vendor/bin/sail artisan queue:work kafka --queue=notifications

# Запустить воркер с ограничением времени
./vendor/bin/sail artisan queue:work kafka --timeout=60

# Запустить воркер с ограничением попыток
./vendor/bin/sail artisan queue:work kafka --tries=3

# Обработать только одно задание
./vendor/bin/sail artisan queue:work kafka --once

# Остановить воркеры после текущего задания
./vendor/bin/sail artisan queue:restart
```

### Управление заданиями

```bash
# Список неудачных заданий
./vendor/bin/sail artisan queue:failed

# Повторить неудачное задание
./vendor/bin/sail artisan queue:retry {id}

# Повторить все неудачные задания
./vendor/bin/sail artisan queue:retry all

# Удалить неудачное задание
./vendor/bin/sail artisan queue:forget {id}

# Очистить все неудачные задания
./vendor/bin/sail artisan queue:flush

# Очистить очередь
./vendor/bin/sail artisan queue:clear kafka
```

### Просмотр логов

```bash
# Laravel Pail
./vendor/bin/sail artisan pail

# Логи контейнера
./vendor/bin/sail logs -f

# Логи Kafka
docker logs -f kafka-kafka

# Логи Zookeeper
docker logs -f kafka-zookeeper
```

---

## Создание заданий (Jobs)

### Создать Job

```bash
./vendor/bin/sail artisan make:job ProcessOrder
```

### Пример Job

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        Log::info("Processing order: {$this->orderId}");
        
        // Ваша логика обработки заказа
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to process order {$this->orderId}: {$exception->getMessage()}");
    }
}
```

### Отправка заданий

```php
use App\Jobs\ProcessOrder;

// Простая отправка
ProcessOrder::dispatch($orderId);

// С задержкой
ProcessOrder::dispatch($orderId)->delay(now()->addMinutes(10));

// В конкретную очередь
ProcessOrder::dispatch($orderId)->onQueue('orders');

// С приоритетом
ProcessOrder::dispatch($orderId)->onQueue('high-priority');
```

---

## Работа с топиками Kafka

### Создание топика

```bash
# Создать топик через Kafka CLI
docker exec -it kafka-kafka kafka-topics --create \
  --bootstrap-server localhost:9092 \
  --replication-factor 1 \
  --partitions 3 \
  --topic notifications

# Список всех топиков
docker exec -it kafka-kafka kafka-topics --list \
  --bootstrap-server localhost:9092

# Описание топика
docker exec -it kafka-kafka kafka-topics --describe \
  --bootstrap-server localhost:9092 \
  --topic notifications
```

### Удаление топика

```bash
docker exec -it kafka-kafka kafka-topics --delete \
  --bootstrap-server localhost:9092 \
  --topic notifications
```

### Просмотр сообщений в топике

```bash
# Просмотр сообщений с начала
docker exec -it kafka-kafka kafka-console-consumer \
  --bootstrap-server localhost:9092 \
  --topic notifications \
  --from-beginning

# Просмотр только новых сообщений
docker exec -it kafka-kafka kafka-console-consumer \
  --bootstrap-server localhost:9092 \
  --topic notifications
```

### Отправка тестового сообщения

```bash
docker exec -it kafka-kafka kafka-console-producer \
  --bootstrap-server localhost:9092 \
  --topic notifications
```

---

## Docker команды

### Управление контейнерами

```bash
# Проверить статус
docker ps | grep kafka

# Перезапустить
./vendor/bin/sail restart kafka
./vendor/bin/sail restart zookeeper

# Просмотр логов
docker logs -f kafka-kafka
docker logs -f kafka-zookeeper

# Войти в контейнер
docker exec -it kafka-kafka bash
docker exec -it kafka-zookeeper bash
```

### Kafka CLI команды

```bash
# Статус брокера
docker exec -it kafka-kafka kafka-broker-api-versions \
  --bootstrap-server localhost:9092

# Список групп потребителей
docker exec -it kafka-kafka kafka-consumer-groups \
  --bootstrap-server localhost:9092 \
  --list

# Описание группы потребителей
docker exec -it kafka-kafka kafka-consumer-groups \
  --bootstrap-server localhost:9092 \
  --describe \
  --group laravel-consumer

# Сброс offset группы
docker exec -it kafka-kafka kafka-consumer-groups \
  --bootstrap-server localhost:9092 \
  --group laravel-consumer \
  --topic notifications \
  --reset-offsets \
  --to-earliest \
  --execute
```

### Очистка очереди Kafka

В Kafka нет прямой команды для очистки очереди, как в RabbitMQ. Есть несколько способов:

#### Способ 1: Удалить и пересоздать топик (полная очистка)

```bash
# Удалить топик
docker exec -it kafka-kafka kafka-topics --delete \
  --bootstrap-server localhost:9092 \
  --topic notifications

# Топик будет автоматически создан при следующей отправке сообщения
# Или создайте вручную:
docker exec -it kafka-kafka kafka-topics --create \
  --bootstrap-server localhost:9092 \
  --replication-factor 1 \
  --partitions 1 \
  --topic notifications
```

#### Способ 2: Сбросить offset consumer group (для воркера)

Это заставит воркер начать читать сообщения с начала топика (или пропустить все текущие):

```bash
# Остановите воркер перед выполнением команды!

# Сбросить offset на начало (прочитает все сообщения заново)
docker exec -it kafka-kafka kafka-consumer-groups \
  --bootstrap-server localhost:9092 \
  --group laravel-consumer \
  --topic notifications \
  --reset-offsets \
  --to-earliest \
  --execute

# Сбросить offset на конец (пропустит все текущие сообщения)
docker exec -it kafka-kafka kafka-consumer-groups \
  --bootstrap-server localhost:9092 \
  --group laravel-consumer \
  --topic notifications \
  --reset-offsets \
  --to-latest \
  --execute

# Удалить consumer group (создастся заново при следующем запуске воркера)
docker exec -it kafka-kafka kafka-consumer-groups \
  --bootstrap-server localhost:9092 \
  --group laravel-consumer \
  --delete
```

#### Способ 3: Изменить consumer group ID (временное решение)

Измените `KAFKA_CONSUMER_GROUP` в `.env` на новое значение:

```env
KAFKA_CONSUMER_GROUP=laravel-consumer-new
```

Это создаст новую группу, которая начнет читать с начала топика.

#### Способ 4: Удалить все сообщения через изменение retention (для разработки)

```bash
# Установить retention на 1 секунду (удалит все сообщения)
docker exec -it kafka-kafka kafka-configs \
  --bootstrap-server localhost:9092 \
  --alter \
  --entity-type topics \
  --entity-name notifications \
  --add-config retention.ms=1000

# Подождать несколько секунд, затем вернуть нормальное значение
docker exec -it kafka-kafka kafka-configs \
  --bootstrap-server localhost:9092 \
  --alter \
  --entity-type topics \
  --entity-name notifications \
  --add-config retention.ms=604800000
```

**Рекомендация для разработки:** Используйте Способ 1 (удаление и пересоздание топика) - это самый простой и надежный способ.

---

## Мониторинг

### Через Kafka CLI

```bash
# Статистика топиков
docker exec -it kafka-kafka kafka-topics --describe \
  --bootstrap-server localhost:9092

# Статистика групп потребителей
docker exec -it kafka-kafka kafka-consumer-groups \
  --bootstrap-server localhost:9092 \
  --describe \
  --all-groups

# Использование диска
docker exec -it kafka-kafka du -sh /var/lib/kafka/data
```

### Через логи

```bash
# Логи Kafka
docker logs -f kafka-kafka | grep ERROR

# Логи Zookeeper
docker logs -f kafka-zookeeper | grep ERROR
```

---

## Производительность

1. **Запустите несколько воркеров**:
```bash
# В разных терминалах или через Supervisor
./vendor/bin/sail artisan queue:work kafka --queue=notifications &
./vendor/bin/sail artisan queue:work kafka --queue=notifications &
./vendor/bin/sail artisan queue:work kafka --queue=notifications &
```

2. **Используйте Supervisor** для автозапуска воркеров:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work kafka --sleep=3 --tries=3
autostart=true
autorestart=true
user=sail
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
```

3. **Настройте партиции** для параллельной обработки:
```bash
docker exec -it kafka-kafka kafka-topics --alter \
  --bootstrap-server localhost:9092 \
  --topic notifications \
  --partitions 6
```

---

## Система уведомлений о постах

### Настройка получателей

1. Перейдите в админ-панель: **Уведомления о постах**
2. Создайте настройку:
   - **Тип уведомления**: Роль или Пользователь
   - **Получатели**: Выберите одну или несколько ролей/пользователей
   - **Активность**: Включите/выключите уведомления

**Важно:** Может существовать только одна настройка для ролей и одна для пользователей.

### Работа с письмами (Development)

```bash
# Убедитесь, что в .env установлено:
MAIL_MAILER=file

# Список всех писем
./vendor/bin/sail exec kafka-app ls -lah storage/app/private/emails/

# Просмотр письма
./vendor/bin/sail exec kafka-app cat storage/app/private/emails/2026-01-05_07-00-38_695b6196a1722.html

# Найти последнее письмо
./vendor/bin/sail exec kafka-app ls -t storage/app/private/emails/*.html | head -1

# Просмотр последнего письма
./vendor/bin/sail exec kafka-app cat $(ls -t storage/app/private/emails/*.html | head -1)

# Удалить все письма
./vendor/bin/sail exec kafka-app rm -rf storage/app/private/emails/*.html

# Количество писем
./vendor/bin/sail exec kafka-app ls -1 storage/app/private/emails/*.html | wc -l
```

### Проверка работы системы

1. **Запустите воркер**:
```bash
./vendor/bin/sail artisan queue:work kafka --queue=notifications --tries=3 --timeout=60
```

2. **Создайте пост** через админ-панель

3. **Проверьте логи**:
```bash
./vendor/bin/sail artisan pail
# или
./vendor/bin/sail exec kafka-app tail -f storage/logs/laravel.log | grep "Post notification"
```

4. **Проверьте Kafka топики**:
```bash
# Список сообщений в топике
docker exec -it kafka-kafka kafka-console-consumer \
  --bootstrap-server localhost:9092 \
  --topic notifications \
  --from-beginning

# Статистика группы потребителей
docker exec -it kafka-kafka kafka-consumer-groups \
  --bootstrap-server localhost:9092 \
  --describe \
  --group laravel-consumer
```

5. **Проверьте письма**:
```bash
./vendor/bin/sail exec kafka-app ls -lah storage/app/private/emails/
```

### Переключение на реальную отправку (Production)

1. Настройте SMTP в `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

2. Перезапустите контейнеры:
```bash
./vendor/bin/sail restart
```

3. Перезапустите воркер:
```bash
./vendor/bin/sail artisan queue:restart
./vendor/bin/sail artisan queue:work kafka --queue=notifications --tries=3 --timeout=60
```
