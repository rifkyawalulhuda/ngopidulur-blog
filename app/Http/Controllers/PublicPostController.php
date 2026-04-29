<?php

namespace App\Http\Controllers;

use App\Models\Post;
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

        return view('public.post', [
            'title' => $post->meta_title ?: $post->title,
            'post' => $post,
            'metaDescription' => $post->meta_description ?: $post->excerpt,
        ]);
    }
}
