<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новый пост</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .post-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #007bff;
        }
        .post-meta {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .post-content {
            margin-bottom: 25px;
            padding: 15px;
            background-color: white;
            border-left: 4px solid #007bff;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📝 Новый пост опубликован!</h1>
    </div>

    <div class="content">
        <p>Здравствуйте, <strong>{{ $recipient->name }}</strong>!</p>

        <p>Пользователь <strong>{{ $post->user->name }}</strong> опубликовал новый пост:</p>

        <div class="post-title">
            {{ $post->title }}
        </div>

        <div class="post-meta">
            📅 Опубликовано: {{ $post->created_at->format('d.m.Y H:i') }}<br>
            ✍️ Автор: {{ $post->user->name }}
        </div>

        @if($post->content)
        <div class="post-content">
            {{ Str::limit(strip_tags($post->content), 300) }}
        </div>
        @endif

        <p style="text-align: center; margin-top: 30px;">
            <a href="{{ $postUrl }}" class="button">Читать полностью</a>
        </p>
    </div>

    <div class="footer">
        <p>Вы получили это письмо, потому что подписаны на уведомления о новых постах.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. Все права защищены.</p>
    </div>
</body>
</html>

