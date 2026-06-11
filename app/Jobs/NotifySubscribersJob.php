<?php

namespace App\Jobs;

use App\Mail\ArticlePublishedMail;
use App\Models\Article;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class NotifySubscribersJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Article $article)
    {
        $this->article->loadMissing('user','comments', 'comments.user', 'attachments', 'tags');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->article->user->email)
            ->send(new ArticlePublishedMail($this->article));
    }
}
