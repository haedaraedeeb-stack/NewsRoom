<?php
namespace App\Services;
use App\Contracts\NotificationServiceInterface;
use App\Models\Article;
use App\Models\User;
use App\Notifications\ArticlePublishedEmailNotification;

class EmailNotificationService implements NotificationServiceInterface
{
    public function send(User $user, Article $article): void
    {
        $user->notify(new ArticlePublishedEmailNotification($article));
    }
}
