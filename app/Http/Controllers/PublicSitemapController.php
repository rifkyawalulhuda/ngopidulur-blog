<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PublicSitemapController extends Controller
{
    public function show(): Response|View
    {
        if (! Schema::hasTable('posts') || ! Schema::hasTable('categories') || ! Schema::hasTable('tags')) {
            return response()->view('public.sitemap', [
                'urls' => collect([
                    [
                        'loc' => route('home'),
                        'lastmod' => now()->toAtomString(),
                    ],
                ]),
            ], 200, [
                'Content-Type' => 'application/xml; charset=UTF-8',
            ]);
        }

        $homeEntry = collect([
            [
                'loc' => route('home'),
                'lastmod' => optional(
                    Post::query()->published()->latest('updated_at')->value('updated_at')
                )?->toAtomString() ?? now()->toAtomString(),
            ],
        ]);

        $postEntries = Post::query()
            ->published()
            ->latest('published_at')
            ->latest('id')
            ->get()
            ->map(fn (Post $post) => [
                'loc' => route('posts.show', $post->slug),
                'lastmod' => optional($post->updated_at ?? $post->published_at)?->toAtomString(),
            ]);

        $categoryEntries = Category::query()
            ->where('is_active', true)
            ->whereHas('posts', fn ($query) => $query->published())
            ->withMax([
                'posts as last_published_post_updated_at' => fn ($query) => $query->published(),
            ], 'updated_at')
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => [
                'loc' => route('category.show', $category->slug),
                'lastmod' => optional($category->last_published_post_updated_at)?->toAtomString(),
            ]);

        $tagEntries = Tag::query()
            ->whereHas('posts', fn ($query) => $query->published())
            ->withMax([
                'posts as last_published_post_updated_at' => fn ($query) => $query->published(),
            ], 'updated_at')
            ->orderBy('name')
            ->get()
            ->map(fn (Tag $tag) => [
                'loc' => route('tag.show', $tag->slug),
                'lastmod' => optional($tag->last_published_post_updated_at)?->toAtomString(),
            ]);

        return response()->view('public.sitemap', [
            'urls' => $homeEntry
                ->concat($postEntries)
                ->concat($categoryEntries)
                ->concat($tagEntries),
        ], 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
