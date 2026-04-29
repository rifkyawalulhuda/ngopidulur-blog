<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TagRequest;
use App\Models\Tag;
use App\Support\UniqueSlug;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'items' => Tag::query()
                ->withCount('posts')
                ->orderBy('name')
                ->get()
                ->map(fn (Tag $tag) => $this->transform($tag)),
        ]);
    }

    public function store(TagRequest $request, UniqueSlug $slugger): JsonResponse
    {
        $data = $request->validated();

        $tag = Tag::create([
            'name' => $data['name'],
            'slug' => $slugger->for(Tag::class, $data['slug'] ?? $data['name']),
        ]);

        return response()->json([
            'message' => 'Tag berhasil ditambahkan.',
            'item' => $this->transform($tag->loadCount('posts')),
        ], 201);
    }

    public function show(Tag $tag): JsonResponse
    {
        return response()->json([
            'item' => $this->transform($tag->loadCount('posts')),
        ]);
    }

    public function update(TagRequest $request, Tag $tag, UniqueSlug $slugger): JsonResponse
    {
        $data = $request->validated();

        $tag->fill([
            'name' => $data['name'],
            'slug' => $slugger->for(Tag::class, $data['slug'] ?? $data['name'], $tag->getKey()),
        ]);
        $tag->save();

        return response()->json([
            'message' => 'Tag berhasil diperbarui.',
            'item' => $this->transform($tag->loadCount('posts')),
        ]);
    }

    public function destroy(Tag $tag): JsonResponse
    {
        $tag->posts()->detach();
        $tag->delete();

        return response()->json([
            'message' => 'Tag berhasil dihapus.',
        ]);
    }

    private function transform(Tag $tag): array
    {
        return [
            'id' => $tag->id,
            'name' => $tag->name,
            'slug' => $tag->slug,
            'posts_count' => $tag->posts_count ?? $tag->posts()->count(),
            'created_at' => $tag->created_at?->toDateTimeString(),
            'updated_at' => $tag->updated_at?->toDateTimeString(),
        ];
    }
}
