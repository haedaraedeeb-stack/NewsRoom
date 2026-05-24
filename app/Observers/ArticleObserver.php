<?php

namespace App\Observers;

use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class ArticleObserver
{
    private function clearCache(): void
    {
        Cache::forget('dashboard:stats');
        Cache::forget('tags:popular');
    }
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        $this->clearCache();
    }

    public function archived(Article $article): void
    {
        $this->cl
    }
}
