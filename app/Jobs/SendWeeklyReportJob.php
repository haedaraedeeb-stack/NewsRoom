<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\User;
use App\Notifications\WeeklyReportNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWeeklyReportJob implements ShouldQueue
{
    public int $tries = 3;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $articles = Article::whereBetween('published_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->get();
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new WeeklyReportNotification($articles));
        }
    }
}
