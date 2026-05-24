<?php

namespace App\Services;

use App\Contracts\NotificationServiceInterface;
use App\Models\Article;
use App\Models\User;
use App\Notifications\ArticlePublishedDatabaseNotification;

class DatabaseNotificationService implements NotificationServiceInterface
{
    public function send (User $user, Article $article): void
    {
        $user->notify(new ArticlePublishedDatabaseNotification(($article)));
    }
}
