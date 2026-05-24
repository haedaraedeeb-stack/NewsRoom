<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function update(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || $user->hasRole('admin');
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || $user->hasRole('admin');
    }

    public function publish(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || $user->hasRole('admin');
    }

    public function archive(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || $user->hasRole('admin');
    }
}
