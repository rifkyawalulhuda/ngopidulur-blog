<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use App\Models\Tag;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $today = CarbonImmutable::today();
        $hasPostViewsTable = Schema::hasTable('post_views');
        $currentWeekStart = $today->subDays(6)->startOfDay();
        $previousWeekStart = $currentWeekStart->subDays(7);
        $previousWeekEnd = $currentWeekStart->subSecond();
        $currentMonthStart = $today->startOfMonth();
        $previousMonthStart = $currentMonthStart->subMonth()->startOfMonth();
        $previousMonthEnd = $currentMonthStart->subSecond();

        $totalPosts = Post::count();
        $draftPosts = Post::draft()->count();
        $currentWeekPosts = Post::query()->whereBetween('created_at', [$currentWeekStart, $today->endOfDay()])->count();
        $previousWeekPosts = Post::query()->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])->count();

        $totalViews = $hasPostViewsTable ? PostView::count() : 0;
        $currentWeekViews = $hasPostViewsTable
            ? PostView::query()->whereBetween('viewed_at', [$currentWeekStart, $today->endOfDay()])->count()
            : 0;
        $previousWeekViews = $hasPostViewsTable
            ? PostView::query()->whereBetween('viewed_at', [$previousWeekStart, $previousWeekEnd])->count()
            : 0;

        $monthlyVisitors = $hasPostViewsTable
            ? PostView::query()
                ->whereBetween('viewed_at', [$currentMonthStart, $today->endOfDay()])
                ->distinct('visitor_key')
                ->count('visitor_key')
            : 0;

        $previousMonthVisitors = $hasPostViewsTable
            ? PostView::query()
                ->whereBetween('viewed_at', [$previousMonthStart, $previousMonthEnd])
                ->distinct('visitor_key')
                ->count('visitor_key')
            : 0;

        $publishedThisMonth = Post::published()
            ->whereBetween('published_at', [$currentMonthStart, $today->endOfDay()])
            ->count();

        $monthlyTarget = 8;

        $stats = [
            'total_posts' => [
                'label' => 'Total Tulisan',
                'value' => $totalPosts,
                'growth' => $this->calculateGrowth($currentWeekPosts, $previousWeekPosts),
                'caption' => $currentWeekPosts . ' tulisan dibuat 7 hari terakhir',
            ],
            'total_views' => [
                'label' => 'Total Views',
                'value' => $totalViews,
                'growth' => $this->calculateGrowth($currentWeekViews, $previousWeekViews),
                'caption' => $currentWeekViews . ' views dalam 7 hari terakhir',
            ],
            'monthly_visitors' => [
                'label' => 'Pengunjung Bulan Ini',
                'value' => $monthlyVisitors,
                'growth' => $this->calculateGrowth($monthlyVisitors, $previousMonthVisitors),
                'caption' => 'Unique visitors bulan berjalan',
            ],
            'draft_posts' => [
                'label' => 'Draft Menunggu',
                'value' => $draftPosts,
                'growth' => null,
                'caption' => 'Siap dirapikan sebelum diterbitkan',
            ],
        ];

        $recentPostsQuery = Post::query()
            ->with(['author:id,name', 'category:id,name,slug'])
            ->latest('published_at')
            ->latest('id');

        if ($hasPostViewsTable) {
            $recentPostsQuery->withCount('views');
        }

        $recentPosts = $recentPostsQuery
            ->limit(6)
            ->get()
            ->map(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'status' => $post->status,
                'views' => (int) ($post->views_count ?? 0),
                'published_at' => $post->published_at?->toDateTimeString(),
                'updated_at' => $post->updated_at?->toDateTimeString(),
                'author_name' => $post->author?->name,
                'category_name' => $post->category?->name,
                'edit_url' => url('/admin/posts/' . $post->id . '/edit'),
                'preview_api_url' => route('admin.api.posts.preview', $post),
            ]);

        $topPostsQuery = Post::query()
            ->published()
            ->with(['category:id,name']);

        if ($hasPostViewsTable) {
            $topPostsQuery->withCount('views')->orderByDesc('views_count');
        }

        $topPosts = $topPostsQuery
            ->orderByDesc('published_at')
            ->limit(5)
            ->get()
            ->map(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'views' => (int) ($post->views_count ?? 0),
                'category_name' => $post->category?->name,
                'published_at' => $post->published_at?->toDateTimeString(),
                'edit_url' => url('/admin/posts/' . $post->id . '/edit'),
            ]);

        $activities = Post::query()
            ->with(['author:id,name'])
            ->latest('updated_at')
            ->limit(6)
            ->get()
            ->map(function (Post $post) {
                $action = 'Menyimpan draft';
                $description = 'Tulisan masih dalam proses penulisan.';

                if ($post->status === Post::STATUS_PUBLISHED) {
                    $action = 'Menerbitkan tulisan';
                    $description = 'Tulisan sudah tayang untuk pembaca publik.';
                } elseif ($post->status === Post::STATUS_ARCHIVED) {
                    $action = 'Mengarsipkan tulisan';
                    $description = 'Tulisan disimpan dan tidak tampil di publik.';
                } elseif ($post->updated_at && $post->created_at && $post->updated_at->gt($post->created_at)) {
                    $action = 'Memperbarui tulisan';
                    $description = 'Konten, metadata, atau status baru saja diperbarui.';
                }

                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'action' => $action,
                    'description' => $description,
                    'author_name' => $post->author?->name ?? 'Admin',
                    'time' => $post->updated_at?->toDateTimeString(),
                ];
            });

        $trafficLabels = collect(range(29, 0))
            ->map(fn (int $daysAgo) => $today->subDays($daysAgo))
            ->values();

        $trafficGrouped = $hasPostViewsTable
            ? PostView::query()
                ->selectRaw('DATE(viewed_on) as day, COUNT(*) as total')
                ->whereBetween('viewed_on', [$today->subDays(29)->toDateString(), $today->toDateString()])
                ->groupBy('day')
                ->pluck('total', 'day')
            : collect();

        $categoryGrouped = Category::query()
            ->select('categories.id', 'categories.name')
            ->selectRaw('COUNT(posts.id) as total_posts')
            ->leftJoin('posts', 'posts.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_posts')
            ->limit(6)
            ->get();

        return response()->json([
            'stats' => $stats,
            'monthly_target' => [
                'target' => $monthlyTarget,
                'completed' => $publishedThisMonth,
                'remaining' => max($monthlyTarget - $publishedThisMonth, 0),
                'progress_percentage' => min((int) round(($publishedThisMonth / max($monthlyTarget, 1)) * 100), 100),
                'message' => sprintf(
                    'Kamu sudah publish %d tulisan bulan ini. Terus semangat ngopi & nulis, Dulur! ☕',
                    $publishedThisMonth
                ),
            ],
            'charts' => [
                'traffic' => [
                    'labels' => $trafficLabels->map(fn (CarbonImmutable $date) => $date->format('d M'))->all(),
                    'series' => $trafficLabels->map(fn (CarbonImmutable $date) => (int) ($trafficGrouped[$date->toDateString()] ?? 0))->all(),
                ],
                'categories' => [
                    'labels' => $categoryGrouped->pluck('name')->all(),
                    'series' => $categoryGrouped->pluck('total_posts')->map(fn ($value) => (int) $value)->all(),
                ],
            ],
            'recent_posts' => $recentPosts,
            'top_posts' => $topPosts,
            'activities' => $activities,
        ]);
    }

    private function calculateGrowth(int $current, int $previous): ?array
    {
        if ($current === 0 && $previous === 0) {
            return [
                'value' => 0,
                'direction' => 'neutral',
                'label' => '0%',
            ];
        }

        if ($previous === 0) {
            return [
                'value' => 100,
                'direction' => 'up',
                'label' => '+100%',
            ];
        }

        $growth = (int) round((($current - $previous) / $previous) * 100);

        return [
            'value' => $growth,
            'direction' => $growth > 0 ? 'up' : ($growth < 0 ? 'down' : 'neutral'),
            'label' => ($growth > 0 ? '+' : '') . $growth . '%',
        ];
    }
}
