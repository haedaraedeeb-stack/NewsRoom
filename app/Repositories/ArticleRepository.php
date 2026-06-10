<?php

namespace App\Repositories;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function getAll(?User $user = null, int $perPage = 10): LengthAwarePaginator
    {
        if (!$user)
        {
            return  Article::where('status', ArticleStatus::Published)->latest()->get();
        }
        if ($user->hasRole('admin')) {
            return Article::latest()->get();
        }

        return Article::where(function ($query) use ($user) {
            $query->where('status', ArticleStatus::Published)
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->whereIn('status', [ArticleStatus::Draft, ArticleStatus::Archived]);
                });
        })->latest()->paginate($perPage);
    }

    public function createArticle(array $data): Article
    {
        return Article::create($data);
    }

    public function updateArticle(int $id, array $data): Article
    {
        $article = Article::findOrFail($id);
        $article->update($data);
        return $article;
    }

    public function deleteArticle(int $id): bool
    {
        $article = Article::findOrFail($id);
        return $article->delete();
    }

    public function getArticleById(int $id): Article
    {
        return Article::findOrFail($id);
    }
}
