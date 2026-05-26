<?php

namespace App\Services;

use App\Jobs\SendArticlePublishedNotificationJob;
use App\Models\Article;
use App\Models\User;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ArticleService
{
    public function __construct(private readonly ArticleRepositoryInterface $articleRepository
    , private readonly AttachmentService  $attachmentService) {}

    public function create(array $data, UploadedFile $file = null): Article
    {
        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);
        $data['user_id'] = Auth::id();
        $data['status'] = $data['status'] ?? 'draft';
        $article =  $this->articleRepository->createArticle($data);
        if ($file)
        {
            $files = $this->attachmentService->store($article, $file);
        }
        if (!empty($tagIds)) {
            $article->tags()->sync($tagIds);
        }
        return $article->load(['tags', 'attachments']);
    }

    public function update(int $id, array $data): Article
    {
        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);
        $data['user_id'] = Auth::id();
        $article = $this->articleRepository->updateArticle($id, $data);
        if (!empty($tagIds)) {
            $article->tags()->sync($tagIds);
        }
        return $article->load(['tags', 'attachments']);
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
        SendArticlePublishedNotificationJob::dispatch($article)
            ->onQueue('notifications');
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
