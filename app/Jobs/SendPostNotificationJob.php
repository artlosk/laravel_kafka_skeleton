<?php

namespace App\Jobs;

use App\Mail\NewPostNotification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPostNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];
    public $timeout = 120;

    protected $postId;
    protected $userId;

    public function __construct(int $postId, int $userId)
    {
        $this->postId = $postId;
        $this->userId = $userId;
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $post = Post::with('user')->find($this->postId);
        $user = User::find($this->userId);

        if (!$post) {
            Log::warning("Post {$this->postId} not found for notification");
            return;
        }

        if (!$user) {
            Log::warning("User {$this->userId} not found for notification");
            return;
        }

        try {
            Mail::to($user->email)->send(new NewPostNotification($post, $user));

            Log::info('Post notification sent successfully', [
                'post_id' => $post->id,
                'post_title' => $post->title,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'attempt' => $this->attempts(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send post notification', [
                'post_id' => $post->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff[$this->attempts() - 1] ?? 60);
            }

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Post notification job failed permanently', [
            'post_id' => $this->postId,
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }

    public function tags(): array
    {
        return ['post-notification', 'post:' . $this->postId, 'user:' . $this->userId];
    }
}

