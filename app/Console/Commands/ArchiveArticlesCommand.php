<?php

namespace App\Console\Commands;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('articles:archive {days=30 : Number of days} {--dry-run : Show without archiving}')]
#[Description('Archive articles that have not been published')]
class ArchiveArticlesCommand extends Command
{
    public function handle(): int
    {
        $days    = $this->argument('days');
        $isDryRun = (bool)$this->option('dry-run');
        $this->info("Checking articles older than {$days} days...");
        $articles = Article::where('status', ArticleStatus::Draft)
            ->where('created_at', '<', now()->subDays($days))
            ->get();

        if ($articles->isEmpty()) {
            $this->info('No articles to archive.');
            return Command::SUCCESS;
        }
        $this->info("Found {$articles->count()} articles.");

        if ($isDryRun) {
            $this->warn('Dry run — no changes made.');
            return Command::SUCCESS;
        }
        foreach ($articles as $article) {
            $article->update(['status' => ArticleStatus::Archived]);
        }
        $this->info("Archived {$articles->count()} articles successfully.");
        return Command::SUCCESS;
    }
}
