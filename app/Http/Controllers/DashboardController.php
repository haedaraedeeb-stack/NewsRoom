<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::get('dashboard:stats');
        $popularTags = Cache::get('tags:popular');

        if (!$stats || !$popularTags) {
            $lock = Cache::lock('dashboard:lock', 10);

            if ($lock->get()) {
                try {
                    $stats = [
                        'users_count'    => User::count(),
                        'articles_count' => Article::count(),
                        'comments_count' => Comment::where('commentable_type', Article::class)->count(),
                        'top_writer'     => Article::select('user_id', DB::raw('COUNT(*) as articles_count'))
                            ->with('user:id,name')
                            ->groupBy('user_id')
                            ->orderByDesc('articles_count')
                            ->first(),
                    ];

                    $popularTags = Tag::withCount('articles')
                        ->orderByDesc('articles_count')
                        ->limit(10)
                        ->get();

                    Cache::put('dashboard:stats', $stats, now()->addMinutes(10));
                    Cache::put('tags:popular', $popularTags, now()->addMinutes(10));

                } finally {
                    $lock->release();
                }
            } else {
                sleep(1);
                $stats      = Cache::get('dashboard:stats', []);
                $popularTags = Cache::get('tags:popular', []);
            }
        }

        return $this->successResponse(
            data: [
                'stats'       => $stats,
                'popular_tags' => $popularTags,
            ],
            message: 'Dashboard retrieved successfully.',
        );
    }
}
