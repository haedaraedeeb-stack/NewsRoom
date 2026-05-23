<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use Illuminate\Support\Collection;

class CommentRepository implements CommentRepositoryInterface
{
    public function getByModel(string $type, int $id): Collection
    {
        return Comment::where('commentable_type', $type)
            ->where('commentable_id', $id)
            ->latest()
            ->get();
    }

    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    public function update(int $id, array $data): Comment
    {
        $comment = Comment::findOrFail($id);
        $comment->update($data);
        return $comment;
    }

    public function delete(int $id): bool
    {
        $comment = Comment::findOrFail($id);
        return $comment->delete();
    }

    public function findById(int $id): Comment
    {
        return Comment::findOrFail($id);
    }
}
