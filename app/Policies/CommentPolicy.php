<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->hasRole('admin');
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->hasRole('admin');
    }
}
