<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ArticleService
{
    public function __construct(private readonly ArticleRepositoryInterface $articleRepository) {}

    public function create(array $data): Article
    {
        $data['user_id'] = Auth::id();
        $data['status'] = $data['status'] ?? 'draft';
        return $this->articleRepository->createArticle($data);
    }

    public function update(int $id, array $data): Article
    {
        $data['user_id'] = Auth::id();
        return $this->articleRepository->updateArticle($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->articleRepository->deleteArticle($id);
    }

    public function getAllArticles(?User $user = null): Collection
    {
        return $this->articleRepository->getAll($user);
    }

    public function getArticleById(int $id): Article
    {
        return $this->articleRepository->getArticleById($id);
    }

    public function publish(int $id): Article
    {
        $article = $this->articleRepository->getArticleById($id);
        $article->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return $article;
    }

    public function archive(int $id): Article
    {
        $article = $this->articleRepository->getArticleById($id);
        $article->update(['status' => 'archived',
            'archived_at' => now(),
            ]);
        return $article;
    }
}
