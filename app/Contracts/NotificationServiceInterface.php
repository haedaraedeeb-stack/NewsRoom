<?php

namespace App\Contracts;

use App\Models\Article;
use App\Models\User;

interface NotificationServiceInterface
{
    public function send(User $user, Article $article): void;
}
