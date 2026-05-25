<?php

namespace App\Console\Commands;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('articles:report {--dry-run : Show without archiving}')]
#[Description('Report published articles per writer this month')]
class ArticlesReportCommand extends Command
{
    public function handle(): int
    {
        $isDryRun = (bool)$this->option('dry-run');
        $this->info('Generating articles report for ' . now()->format('F Y') . '...');
        $articles = Article::where('status', ArticleStatus::Published)
            ->whereMonth('published_at', now()->month)
            ->whereYear('published_at', now()->year)
            ->with('user:id,name')
            ->get()
            ->groupBy('user_id');

        if ($articles->isEmpty()) {
            $this->info('No published articles this month.');
            return Command::SUCCESS;
        }
        if ($isDryRun) {
            $this->warn('Dry run — no changes made.');
            return Command::SUCCESS;
        }
        $rows = [];
        foreach ($articles as $userId => $userArticles) {
            $rows[] = [
                $userArticles->first()->user->name,
                $userArticles->count(),
            ];
        }

        $this->table(['Writer', 'Articles Count'], $rows);

        $logMessage = now()->format('Y-m-d H:i:s') . " - Monthly Report:\n";
        foreach ($rows as $row) {
            $logMessage .= "  {$row[0]}: {$row[1]} articles\n";
        }
       Storage::append(
            'logs/articles-report.log',
            $logMessage
        );

        $this->info('Report saved to storage/logs/articles-report.log');
        return Command::SUCCESS;
    }
}
