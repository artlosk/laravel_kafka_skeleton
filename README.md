# Laravel Skeleton

Базовый скелетон Laravel-приложения с готовой инфраструктурой для разработки веб-приложений. Включает систему управления правами доступа (RBAC), работу с медиафайлами и административную панель.

## Технологический стек

- PHP 8.2+
- Laravel 12.0
- MySQL 8.0
- Redis
- Apache Kafka
- Docker (Laravel Sail)

## Основные компоненты

### Система прав доступа (RBAC)

Реализована на базе пакета `spatie/laravel-permission`. Поддерживает гибкое управление ролями и правами пользователей.

**Предустановленные права:**
- Управление пользователями (view, create, edit, delete)
- Управление постами (read, view, create, edit, delete)
- Управление медиафайлами (view, upload, delete)
- Управление ролями и правами (view, create, edit, delete)
- Доступ к административной панели

### Медиабиблиотека

Интеграция `spatie/laravel-medialibrary` для работы с изображениями и файлами:
- Загрузка и хранение файлов
- Автоматическое создание миниатюр
- Связь медиафайлов с моделями через полиморфные отношения
- Поддержка сортировки медиафайлов
- Интеграция с FilePond для загрузки файлов

### Административная панель

Используется `jeroennoten/laravel-adminlte` для административного интерфейса:
- Готовый дизайн панели администратора
- Управление пользователями и их ролями
- CRUD для постов с поддержкой медиагалереи
- Профиль пользователя с аватарами
- Генерация и управление API токенами

### API

REST API с аутентификацией через Laravel Sanctum:
- Token-based аутентификация
- Управление токенами через административную панель
- Готовые endpoints для работы с постами
- Policy-based авторизация

### Система уведомлений

Автоматические email-уведомления о новых постах через Kafka:

**Возможности:**
- Мультиселект ролей и пользователей (можно выбрать несколько за раз)
- Асинхронная отправка через очередь Kafka
- Управление через административную панель
- Сохранение писем в файлы (`storage/app/private/emails/`) для разработки
- Поддержка реальной отправки email для production

**Быстрый старт:**

1. Настройте `.env`:
```env
QUEUE_CONNECTION=kafka
MAIL_MAILER=file  # для dev, или smtp для production
```

2. Запустите воркер:
```bash
./vendor/bin/sail artisan queue:work kafka --queue=notifications --tries=3 --timeout=60
```

3. В админ-панели перейдите в **"Уведомления о постах"** и настройте получателей

4. При создании поста уведомления автоматически отправятся в очередь

5. Проверьте письма:
```bash
./vendor/bin/sail exec kafka-app ls -lah storage/app/private/emails/
# Просмотреть письмо:
./vendor/bin/sail exec kafka-app cat storage/app/private/emails/имя_файла.html
```

## Установка

### Требования

- Docker и Docker Compose
- Git

**Примечание:** Локальная установка PHP и Composer не требуется. Все команды выполняются внутри Docker контейнеров.

### Шаги установки

1. Клонировать репозиторий:
```bash
git clone <repository-url>
cd laravel_sceleton
```

2. Скопировать файл окружения:
```bash
cp .env.example .env
```

3. Очистить служебные файлы macOS (если работаете на Mac с внешним диском):
```bash
find . -name "._*" -type f -delete
dot_clean -m .
```

4. Установить зависимости и запустить контейнеры:
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

./vendor/bin/sail up -d
```

5. Сгенерировать ключ приложения:
```bash
./vendor/bin/sail artisan key:generate
```

6. Выполнить миграции и сидеры:
```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed --class=AdminUserSeeder
```

7. Установить npm зависимости и собрать фронтенд:
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

8. Создать символическую ссылку для хранилища:
```bash
./vendor/bin/sail artisan storage:link
```

## Доступ к приложению

После установки приложение будет доступно по адресу: `http://localhost`

### Учетные данные администратора

- Email: `admin@example.com`
- Пароль: `admin123`

При первом запуске сидера в консоли будет выведен API токен для администратора.

### Docker контейнеры

После запуска будут доступны следующие контейнеры:
- `kafka-app` - Laravel приложение (порт 80)
- `kafka-mysql` - MySQL база данных (порт 3306)
- `kafka-redis` - Redis (порт 6379)
- `kafka-kafka` - Apache Kafka (порт 9092)
- `kafka-zookeeper` - Zookeeper для Kafka (порт 2181)
- `kafka-ui` - Kafka UI веб-интерфейс (порт 8080)

### Kafka UI

Веб-интерфейс для управления Kafka: `http://localhost:8080`

**Возможности:**
- Просмотр топиков и сообщений
- Мониторинг групп потребителей
- Управление топиками
- Просмотр метрик и статистики
- Отправка тестовых сообщений

**Подробная документация**: [KAFKA_GUIDE.md](KAFKA_GUIDE.md)

## Структура проекта

### Модели

**User** - пользователи системы
- Роли и права через Spatie Permission
- API токены через Laravel Sanctum
- Связь с постами

**Post** - публикации
- Связь с пользователем
- Интеграция с медиабиблиотекой
- Поддержка галереи изображений

### Контроллеры

- `Backend/` - контроллеры административной панели
- `Api/` - REST API endpoints
- Авторизация через Policy классы

### Middleware

- Проверка прав доступа
- Rate limiting для API
- Локализация

## Разработка

### Запуск в режиме разработки

Для одновременного запуска всех необходимых сервисов:

```bash
./vendor/bin/sail composer dev
```

Эта команда запустит:
- PHP сервер разработки
- Queue worker
- Log viewer (Laravel Pail)
- Vite dev server

### Работа с Artisan командами

Все команды Laravel выполняются через Sail:

```bash
# Примеры команд
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan make:controller ExampleController
./vendor/bin/sail artisan queue:work
./vendor/bin/sail artisan cache:clear
```

### Работа с Composer

```bash
# Установка пакета
./vendor/bin/sail composer require package/name

# Обновление зависимостей
./vendor/bin/sail composer update

# Автозагрузка
./vendor/bin/sail composer dump-autoload
```

### Тестирование

Запуск тестов:

```bash
./vendor/bin/sail composer test
```

### Линтинг кода

```bash
./vendor/bin/sail composer pint
```

### Доступ к контейнеру

Для выполнения команд внутри контейнера:

```bash
./vendor/bin/sail shell
```

Или напрямую через Docker:

```bash
docker exec -it kafka-app bash
```

## Конфигурация

### База данных

Настройки подключения к БД находятся в `.env`:
- `DB_CONNECTION=mysql`
- `DB_HOST=mysql`
- `DB_PORT=3306`
- `DB_DATABASE=laravel`
- `DB_USERNAME=sail`
- `DB_PASSWORD=password`

### Хранилище файлов

По умолчанию используется локальное хранилище в `storage/app/public`. Для использования других драйверов (S3, etc.) настройте `config/filesystems.php`.

### Права доступа

Конфигурация системы прав находится в `config/permission.php`. По умолчанию используется guard `web` без поддержки команд.

### Kafka и очереди

Конфигурация очередей находится в `config/queue.php`. Для использования Kafka:

1. Установите пакет:
```bash
./vendor/bin/sail composer require enqueue/rdkafka
# или
./vendor/bin/sail composer require nmred/kafka-php
```

2. Настройте `.env`:
```env
QUEUE_CONNECTION=kafka
KAFKA_BROKERS=kafka:29092
KAFKA_QUEUE=default
KAFKA_TOPIC=notifications
KAFKA_CONSUMER_GROUP=laravel-consumer
```

3. Запустите воркер:
```bash
./vendor/bin/sail artisan queue:work kafka --queue=notifications --tries=3 --timeout=60
```

4. Просмотр писем (для разработки):
```bash
# Список писем
./vendor/bin/sail exec kafka-app ls -lah storage/app/private/emails/

# Просмотр письма
./vendor/bin/sail exec kafka-app cat storage/app/private/emails/имя_файла.html
```

Подробная документация: [KAFKA_GUIDE.md](KAFKA_GUIDE.md)

## API

### Аутентификация

Для работы с API необходим токен, который можно получить:
1. Через административную панель в профиле пользователя
2. При создании пользователя через AdminUserSeeder

Токен передается в заголовке:
```
Authorization: Bearer {token}
```

### Endpoints

**Posts API:**
- `GET /api/posts` - список постов
- `GET /api/posts/{id}` - получить пост
- `POST /api/posts` - создать пост
- `PUT /api/posts/{id}` - обновить пост
- `DELETE /api/posts/{id}` - удалить пост

Все endpoints защищены авторизацией и проверкой прав доступа.

## Расширение функционала

### Добавление новой модели с медиабиблиотекой

1. Реализовать интерфейс `HasMediaSync`
2. Использовать трейт `InteractsWithMedia`
3. Определить метод `relatedMedia()` для связи с медиафайлами
4. Реализовать метод `syncMedia()` для синхронизации

Пример реализации см. в модели `Post`.

### Добавление новых прав

1. Создать права в сидере или через код:
```php
Permission::create(['name' => 'new-permission']);
```

2. Назначить права роли:
```php
$role->givePermissionTo('new-permission');
```

3. Проверить права в контроллере:
```php
$this->authorize('new-permission');
```

## Полезные команды

### Управление контейнерами

```bash
# Запустить все контейнеры
./vendor/bin/sail up -d

# Остановить все контейнеры
./vendor/bin/sail down

# Перезапустить контейнеры
./vendor/bin/sail restart

# Просмотр логов
./vendor/bin/sail logs

# Просмотр логов конкретного сервиса
./vendor/bin/sail logs mysql
```

### Работа с базой данных

```bash
# Подключение к MySQL
./vendor/bin/sail mysql

# Выполнение миграций
./vendor/bin/sail artisan migrate

# Откат миграций
./vendor/bin/sail artisan migrate:rollback

# Пересоздание БД с сидерами
./vendor/bin/sail artisan migrate:fresh --seed
```

### Очистка кеша

```bash
# Очистить весь кеш
./vendor/bin/sail artisan optimize:clear

# Или по отдельности
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear
```

### Алиас для упрощения команд

Для удобства можно создать алиас в `~/.zshrc` или `~/.bashrc`:

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

После этого команды можно выполнять короче:

```bash
sail up -d
sail artisan migrate
sail composer install
```

## Лицензия

MIT

