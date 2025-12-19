<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewPostNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $post) {}

    public function via($notifiable)
    {
        return ['database']; // or ['mail', 'database']
    }

    public function toDatabase($notifiable)
    {
        return [
            'post_id' => $this->post->id,
            'title' => $this->post->title,
            'message' => 'A new post has been published',
        ];
    }

    // OPTIONAL (email)
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Post')
            ->line('A new post was published')
            ->action('View Post', url("/posts/{$this->post->id}"));
    }
}
