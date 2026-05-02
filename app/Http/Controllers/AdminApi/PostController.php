<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\PostPublishingService;
use App\Support\PublicAssetUrl;
use App\Support\UniqueSlug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Post::query()
            ->with([
                'author:id,name',
                'category:id,name,slug',
                'tags:id,name,slug',
            ]);

        if ($search = trim((string) $request->string('search'))) {
            $query->where('title', 'like', '%'.$search.'%');
        }

        if ($status = trim((string) $request->string('status'))) {
            $query->where('status', $status);
        }

        if ($category = trim((string) $request->string('category'))) {
            $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $category));
        }

        $sort = $request->string('sort')->toString() ?: 'updated_at';

        if ($sort === 'published_at') {
            $query->orderByRaw('published_at IS NULL')
                ->orderByDesc('published_at')
                ->orderByDesc('updated_at');
        } else {
            $query->orderByDesc('updated_at');
        }

        $items = $query->paginate(10)->through(fn (Post $post) => $this->transformList($post));

        return response()->json([
            'items' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
            'filters' => [
                'search' => $search ?? '',
                'status' => $status ?? '',
                'category' => $category ?? '',
                'sort' => $sort,
            ],
            'categories' => Category::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ]),
            'tags' => Tag::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
                ->map(fn (Tag $tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ]),
        ]);
    }

    public function store(PostRequest $request, UniqueSlug $slugger, PostPublishingService $service): JsonResponse
    {
        try {
            $post = DB::transaction(function () use ($request, $slugger, $service) {
                return $this->persistPost(new Post(), $request, $slugger, $service);
            });
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Artikel berhasil disimpan.',
            'item' => $this->transformDetail($post->fresh(['author', 'category', 'tags'])),
        ], 201);
    }

    public function show(Post $post): JsonResponse
    {
        return response()->json([
            'item' => $this->transformDetail($post->load(['author', 'category', 'tags'])),
        ]);
    }

    public function update(PostRequest $request, Post $post, UniqueSlug $slugger, PostPublishingService $service): JsonResponse
    {
        try {
            $post = DB::transaction(function () use ($request, $post, $slugger, $service) {
                return $this->persistPost($post, $request, $slugger, $service);
            });
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Artikel berhasil diperbarui.',
            'item' => $this->transformDetail($post->fresh(['author', 'category', 'tags'])),
        ]);
    }

    public function destroy(Post $post): JsonResponse
    {
        DB::transaction(function () use ($post) {
            $post->slug = $this->trashedSlug($post->slug, $post->id);
            $post->saveQuietly();
            $post->delete();
        });

        return response()->json([
            'message' => 'Artikel berhasil dihapus.',
        ]);
    }

    public function publish(Post $post): JsonResponse
    {
        if ($post->featured_image === null) {
            return response()->json([
                'message' => 'Gambar unggulan wajib diunggah saat artikel diterbitkan.',
            ], 422);
        }

        $post->status = Post::STATUS_PUBLISHED;
        $post->published_at = $post->published_at ?? now();
        $post->featured_image_alt = $post->featured_image_alt ?: $post->title;
        $post->save();

        return response()->json([
            'message' => 'Artikel berhasil diterbitkan.',
            'item' => $this->transformDetail($post->fresh(['author', 'category', 'tags'])),
        ]);
    }

    public function archive(Post $post): JsonResponse
    {
        $post->status = Post::STATUS_ARCHIVED;
        $post->save();

        return response()->json([
            'message' => 'Artikel berhasil diarsipkan.',
            'item' => $this->transformDetail($post->fresh(['author', 'category', 'tags'])),
        ]);
    }

    public function preview(Post $post, PostPublishingService $service): JsonResponse
    {
        $rendered = $service->renderContent($post->content_format, $post->content);

        return response()->json([
            'item' => $this->transformDetail($post->load(['author', 'category', 'tags'])),
            'preview_html' => $rendered,
        ])->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function uploadEditorImage(Request $request, PostPublishingService $service): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'image.required' => 'Gambar editor wajib dipilih.',
            'image.image' => 'File editor harus berupa gambar.',
            'image.mimes' => 'Format gambar editor harus JPG, JPEG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar editor maksimal 2 MB.',
        ]);

        try {
            $path = $service->storeContentImage($validated['image']);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Gambar editor berhasil diunggah.',
            'location' => PublicAssetUrl::fromPublicDisk($path),
            'path' => $path,
        ]);
    }

    private function persistPost(Post $post, PostRequest $request, UniqueSlug $slugger, PostPublishingService $service): Post
    {
        $data = $request->validated();
        $currentImage = $post->featured_image;
        $title = $data['title'];
        $slug = $this->resolveSlug($post, $data, $slugger);
        $renderedContent = $service->renderContent($data['content_format'], $data['content']);
        $readingTime = $service->estimateReadingTime($renderedContent);
        $uploadedImagePath = null;

        if ($request->hasFile('featured_image')) {
            $uploadedImagePath = $service->storeFeaturedImage($request->file('featured_image'));

            if ($currentImage) {
                $service->deleteFeaturedImage($currentImage);
            }
        }

        $featuredImage = $uploadedImagePath ?? $currentImage;
        $featuredImageAlt = $data['featured_image_alt'] ?? null;

        if ($featuredImage !== null && blank($featuredImageAlt)) {
            $featuredImageAlt = $title;
        }

        $publishedAt = $this->resolvePublishedAt($post, $data['status'], $data['published_at'] ?? null);

        $post->fill([
            'user_id' => $request->user()->id,
            'category_id' => $data['category_id'],
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $data['excerpt'] ?? null,
            'content_format' => $data['content_format'],
            'content' => $data['content'],
            'rendered_content' => $renderedContent,
            'reading_time_minutes' => $readingTime,
            'featured_image' => $featuredImage,
            'featured_image_alt' => $featuredImageAlt,
            'is_featured' => $request->boolean('is_featured', false),
            'status' => $data['status'],
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'published_at' => $publishedAt,
        ]);
        $post->save();

        $post->tags()->sync($data['tags'] ?? []);

        return $post;
    }

    private function resolveSlug(Post $post, array $data, UniqueSlug $slugger): string
    {
        $slug = trim((string) ($data['slug'] ?? ''));

        if ($slug !== '') {
            return $slug;
        }

        if ($post->exists) {
            return $post->slug;
        }

        return $slugger->for(Post::class, $data['title']);
    }

    private function resolvePublishedAt(Post $post, string $status, mixed $publishedAt): ?Carbon
    {
        if ($publishedAt !== null && $publishedAt !== '') {
            return Carbon::parse($publishedAt);
        }

        if ($status === Post::STATUS_PUBLISHED) {
            return $post->published_at ?? now();
        }

        return $post->published_at;
    }

    private function trashedSlug(string $slug, int $postId): string
    {
        $suffix = '--trashed-'.$postId;
        $maxLength = 255 - strlen($suffix);
        $base = Str::limit($slug, $maxLength, '');

        return $base.$suffix;
    }

    private function transformList(Post $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'category_name' => $post->category?->name,
            'category_slug' => $post->category?->slug,
            'status' => $post->status,
            'published_at' => $post->published_at?->toDateTimeString(),
            'updated_at' => $post->updated_at?->toDateTimeString(),
            'author_name' => $post->author?->name,
            'tags_count' => $post->tags->count(),
            'featured_image' => $post->featured_image,
            'featured_image_url' => $post->featured_image_url,
        ];
    }

    private function transformDetail(Post $post): array
    {
        return [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'category_id' => $post->category_id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'content_format' => $post->content_format,
            'content' => $post->content,
            'rendered_content' => $post->rendered_content,
            'reading_time_minutes' => $post->reading_time_minutes,
            'featured_image' => $post->featured_image,
            'featured_image_url' => $post->featured_image_url,
            'featured_image_alt' => $post->featured_image_alt,
            'is_featured' => (bool) $post->is_featured,
            'status' => $post->status,
            'meta_title' => $post->meta_title,
            'meta_description' => $post->meta_description,
            'published_at' => $post->published_at?->toDateTimeString(),
            'created_at' => $post->created_at?->toDateTimeString(),
            'updated_at' => $post->updated_at?->toDateTimeString(),
            'author' => $post->author ? [
                'id' => $post->author->id,
                'name' => $post->author->name,
            ] : null,
            'category' => $post->category ? [
                'id' => $post->category->id,
                'name' => $post->category->name,
                'slug' => $post->category->slug,
            ] : null,
            'tags' => $post->tags->map(fn (Tag $tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ])->values(),
            'tag_ids' => $post->tags->pluck('id')->values(),
        ];
    }
}
