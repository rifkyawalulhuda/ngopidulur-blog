<?php

namespace App\Http\Controllers;

use App\Models\PostView;
use App\Models\Post;
use App\Support\BlogSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicPostController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $post = Post::query()
            ->published()
            ->withPublicRelations()
            ->where('slug', $slug)
            ->firstOrFail();

        $this->trackView($request, $post);

        $relatedPosts = Post::query()
            ->published()
            ->withPublicRelations()
            ->whereKeyNot($post->id)
            ->where(function ($query) use ($post) {
                $query->where('category_id', $post->category_id);

                if ($post->tags->isNotEmpty()) {
                    $query->orWhereHas('tags', fn ($tagQuery) => $tagQuery->whereIn('tags.id', $post->tags->pluck('id')));
                }
            })
            ->latest('published_at')
            ->latest('id')
            ->limit(3)
            ->get();

        if ($relatedPosts->isEmpty()) {
            $relatedPosts = Post::query()
                ->published()
                ->withPublicRelations()
                ->whereKeyNot($post->id)
                ->latest('published_at')
                ->latest('id')
                ->limit(3)
                ->get();
        }

        return view('public.post', [
            'title' => $post->meta_title ?: $post->title,
            'metaTitle' => $post->meta_title ?: $post->title,
            'post' => $post,
            'metaDescription' => $post->meta_description ?: $post->excerpt,
            'canonicalUrl' => route('posts.show', $post->slug),
            'ogImage' => $post->featured_image_url ?: BlogSettings::assetUrl('default_og_image'),
            'ogType' => 'article',
            'siteName' => BlogSettings::get('site_name', 'Ngopi Dulur'),
            'defaultMetaDescription' => BlogSettings::get('default_meta_description', 'Personal blog CMS dengan nuansa hangat dan fondasi modern.'),
            'relatedPosts' => $relatedPosts,
        ]);
    }

    private function trackView(Request $request, Post $post): void
    {
        $session = $request->session();
        $session->put('ngopi_dulur_last_seen_at', now()->timestamp);

        $sessionId = $session->getId();
        $visitorKey = hash('sha256', implode('|', [
            $sessionId,
            (string) $request->ip(),
            (string) $request->userAgent(),
        ]));

        PostView::query()->updateOrCreate(
            [
                'post_id' => $post->id,
                'visitor_key' => $visitorKey,
                'viewed_on' => now()->toDateString(),
            ],
            [
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
                'viewed_at' => now(),
            ],
        );
    }
}
