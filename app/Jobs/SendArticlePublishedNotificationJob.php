<?php

namespace App\Jobs;

use App\Contracts\NotificationServiceInterface;
use App\Models\Article;
use App\Models\User;
use App\Notifications\ArticlePublishedNotification;
use App\Services\DatabaseNotificationService;
use App\Services\EmailNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendArticlePublishedNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    /**
     * Create a new job instance.
     */
    public function __construct(private Article $article)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            if ($user->hasRole('admin')) {
                app(DatabaseNotificationService::class)->send($user, $this->article);
            } else {
                app(EmailNotificationService::class)->send($user, $this->article);
            }
        }
    }
}
