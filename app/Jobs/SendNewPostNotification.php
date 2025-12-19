<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NewPostNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNewPostNotification  implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public $post) {}

    public function handle(): void
    {
        User::where('id', '!=', $this->post->user_id)
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    $user->notify(new NewPostNotification($this->post));
                }
            });
    }
}
