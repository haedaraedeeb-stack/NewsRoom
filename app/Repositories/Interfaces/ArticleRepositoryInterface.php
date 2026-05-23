<?php

namespace App\Repositories\Interfaces;

use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Collection;

interface ArticleRepositoryInterface
{
    public function getAll(?User $user = null): Collection;

    public function createArticle(array $data): Article;

    public function updateArticle(int $id, array $data): Article;

    public function deleteArticle(int $id): bool;

    public function getArticleById(int $id): Article;
}
