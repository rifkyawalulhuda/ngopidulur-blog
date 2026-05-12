<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\PublicAssetUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class MediaController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Post::query()
            ->with(['category:id,name', 'author:id,name'])
            ->where(function ($query) {
                $query->whereNotNull('featured_image')
                    ->orWhereNotNull('rendered_content')
                    ->orWhereNotNull('content');
            })
            ->latest('updated_at')
            ->latest('id')
            ->get()
            ->flatMap(fn (Post $post) => $this->imagesForPost($post))
            ->unique('id')
            ->values();

        return response()->json([
            'items' => $items,
        ]);
    }

    private function imagesForPost(Post $post): Collection
    {
        $items = collect();

        if (filled($post->featured_image)) {
            $items->push($this->makeImageItem(
                post: $post,
                sourceKey: 'featured',
                sourceLabel: 'Gambar unggulan',
                imageUrl: $post->featured_image_url,
                imagePath: $post->featured_image,
                alt: $post->featured_image_alt
            ));
        }

        foreach ($this->extractContentImages((string) ($post->rendered_content ?: $post->content)) as $index => $image) {
            $items->push($this->makeImageItem(
                post: $post,
                sourceKey: 'content-'.$index,
                sourceLabel: 'Gambar konten',
                imageUrl: $image['url'],
                imagePath: $image['path'],
                alt: $image['alt']
            ));
        }

        return $items->filter(fn (array $item) => filled($item['image_url']));
    }

    private function makeImageItem(Post $post, string $sourceKey, string $sourceLabel, ?string $imageUrl, ?string $imagePath, ?string $alt): array
    {
        $imageKey = $imagePath ?: $imageUrl ?: $sourceKey;

        return [
            'id' => $post->id.'-'.md5($imageKey),
            'post_id' => $post->id,
            'post_title' => $post->title,
            'post_slug' => $post->slug,
            'related_title' => $post->title,
            'image_url' => $imageUrl,
            'thumbnail_url' => $imageUrl,
            'image_alt' => $alt ?: $post->title,
            'image_path' => $imagePath,
            'featured_image_url' => $sourceKey === 'featured' ? $imageUrl : null,
            'featured_image' => $sourceKey === 'featured' ? $imagePath : null,
            'featured_image_alt' => $sourceKey === 'featured' ? $alt : null,
            'source_label' => $sourceLabel,
            'category_name' => $post->category?->name,
            'author_name' => $post->author?->name,
        ];
    }

    private function extractContentImages(string $html): array
    {
        if (trim($html) === '') {
            return [];
        }

        $previous = libxml_use_internal_errors(true);
        $document = new \DOMDocument();
        $document->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = [];

        foreach ($document->getElementsByTagName('img') as $image) {
            $normalized = $this->normalizeImageSource($image->getAttribute('src'));

            if ($normalized === null) {
                continue;
            }

            $images[] = [
                'url' => $normalized['url'],
                'path' => $normalized['path'],
                'alt' => trim($image->getAttribute('alt')) ?: null,
            ];
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $images;
    }

    private function normalizeImageSource(string $source): ?array
    {
        $source = trim($source);

        if ($source === '' || preg_match('/^(data|blob|javascript):/i', $source)) {
            return null;
        }

        if (str_starts_with($source, '/storage/')) {
            $path = ltrim(substr($source, strlen('/storage/')), '/');

            return [
                'url' => PublicAssetUrl::fromPublicDisk($path),
                'path' => $path,
            ];
        }

        if (str_starts_with($source, 'storage/')) {
            $path = ltrim(substr($source, strlen('storage/')), '/');

            return [
                'url' => PublicAssetUrl::fromPublicDisk($path),
                'path' => $path,
            ];
        }

        if (str_starts_with($source, 'posts/')) {
            return [
                'url' => PublicAssetUrl::fromPublicDisk($source),
                'path' => $source,
            ];
        }

        return [
            'url' => $source,
            'path' => null,
        ];
    }
}
