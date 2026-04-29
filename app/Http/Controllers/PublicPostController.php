<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Support\BlogSettings;
use Illuminate\View\View;

class PublicPostController extends Controller
{
    public function show(string $slug): View
    {
        $post = Post::query()
            ->published()
            ->withPublicRelations()
            ->where('slug', $slug)
            ->firstOrFail();

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

        return view('public.post', [
            'title' => $post->meta_title ?: $post->title,
            'post' => $post,
            'metaDescription' => $post->meta_description ?: $post->excerpt,
            'siteName' => BlogSettings::get('site_name', 'Ngopi Dulur'),
            'defaultMetaDescription' => BlogSettings::get('default_meta_description', 'Personal blog CMS dengan nuansa hangat dan fondasi modern.'),
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
