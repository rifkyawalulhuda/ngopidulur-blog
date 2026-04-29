<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Models\Post;
use App\Support\UniqueSlug;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'items' => Category::query()
                ->withCount('posts')
                ->orderBy('name')
                ->get()
                ->map(fn (Category $category) => $this->transform($category)),
        ]);
    }

    public function store(CategoryRequest $request, UniqueSlug $slugger): JsonResponse
    {
        $data = $request->validated();

        $category = Category::create([
            'name' => $data['name'],
            'slug' => $slugger->for(Category::class, $data['slug'] ?? $data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'message' => 'Kategori berhasil ditambahkan.',
            'item' => $this->transform($category->loadCount('posts')),
        ], 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'item' => $this->transform($category->loadCount('posts')),
        ]);
    }

    public function update(CategoryRequest $request, Category $category, UniqueSlug $slugger): JsonResponse
    {
        $data = $request->validated();

        $category->fill([
            'name' => $data['name'],
            'slug' => $slugger->for(Category::class, $data['slug'] ?? $data['name'], $category->getKey()),
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);
        $category->save();

        return response()->json([
            'message' => 'Kategori berhasil diperbarui.',
            'item' => $this->transform($category->loadCount('posts')),
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $inUse = Post::withTrashed()->where('category_id', $category->id)->exists();

        if ($inUse) {
            return response()->json([
                'message' => 'Kategori tidak bisa dihapus karena masih dipakai oleh artikel.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }

    private function transform(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'is_active' => (bool) $category->is_active,
            'posts_count' => $category->posts_count ?? $category->posts()->count(),
            'created_at' => $category->created_at?->toDateTimeString(),
            'updated_at' => $category->updated_at?->toDateTimeString(),
        ];
    }
}
