<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Article;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use Illuminate\Support\Collection;

class CommentService
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository
    ) {}

    public function getComments(string $type, int $id): Collection
    {
        return $this->commentRepository->getByModel($type, $id);
    }

    public function create(Article $article, int $userId, array $data): Comment
    {
        return $this->commentRepository->create([
            'body'             => $data['body'],
            'user_id'          => $userId,
            'commentable_type' => Article::class,
            'commentable_id'   => $article->id,
        ]);
    }

    public function update(int $id, array $data): Comment
    {
        return $this->commentRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->commentRepository->delete($id);
    }

    public function findById(int $id): Comment
    {
        return $this->commentRepository->findById($id);
    }
}
