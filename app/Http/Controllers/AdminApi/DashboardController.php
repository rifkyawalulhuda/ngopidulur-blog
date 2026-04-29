<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::published()->count(),
            'draft_posts' => Post::draft()->count(),
            'archived_posts' => Post::archived()->count(),
            'total_categories' => Category::count(),
            'total_tags' => Tag::count(),
        ];

        $latestPosts = Post::query()
            ->with(['author:id,name', 'category:id,name,slug'])
            ->latest('published_at')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'status' => $post->status,
                'published_at' => $post->published_at?->toDateTimeString(),
                'updated_at' => $post->updated_at?->toDateTimeString(),
                'author_name' => $post->author?->name,
                'category_name' => $post->category?->name,
                'excerpt' => $post->excerpt,
            ]);

        return response()->json([
            'stats' => $stats,
            'latest_posts' => $latestPosts,
        ]);
    }
}
