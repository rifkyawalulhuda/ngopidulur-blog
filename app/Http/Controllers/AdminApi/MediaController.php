<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class MediaController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Post::query()
            ->with(['category', 'author'])
            ->whereNotNull('featured_image')
            ->where('featured_image', '!=', '')
            ->latest('updated_at')
            ->latest('id')
            ->get()
            ->map(fn (Post $post) => [
                'id' => $post->id,
                'post_id' => $post->id,
                'post_title' => $post->title,
                'post_slug' => $post->slug,
                'thumbnail_url' => $post->featured_image_url,
                'featured_image_url' => $post->featured_image_url,
                'featured_image' => $post->featured_image,
                'featured_image_alt' => $post->featured_image_alt,
                'category_name' => $post->category?->name,
                'author_name' => $post->author?->name,
                'status' => $post->status,
                'updated_at' => optional($post->updated_at)->translatedFormat('d M Y H:i'),
                'published_at' => optional($post->published_at)->translatedFormat('d M Y H:i'),
            ])
            ->values();

        return response()->json([
            'items' => $items,
        ]);
    }
}
