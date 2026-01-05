<?php

namespace App\Mail;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPostNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    public $recipient;

    public function __construct(Post $post, User $recipient)
    {
        $this->post = $post;
        $this->recipient = $recipient;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новый пост: ' . $this->post->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-post-notification',
            with: [
                'post' => $this->post,
                'recipient' => $this->recipient,
                'postUrl' => route('backend.posts.show', $this->post->id),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

