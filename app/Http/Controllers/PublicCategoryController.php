<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Support\BlogSettings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('posts', fn ($query) => $query->published())
            ->withCount([
                'posts as published_posts_count' => fn ($query) => $query->published(),
            ])
            ->orderByDesc('published_posts_count')
            ->orderBy('name')
            ->paginate(12);

        return view('public.categories', [
            'title' => 'Semua Kategori',
            'metaTitle' => 'Semua Kategori | '.BlogSettings::get('site_name', 'Ngopi Dulur'),
            'metaDescription' => 'Jelajahi semua kategori tulisan yang tersedia di Ngopi Dulur.',
            'canonicalUrl' => route('category.index'),
            'categories' => $categories,
        ]);
    }

    public function show(Request $request, Category $category): View
    {
        abort_unless($category->is_active, 404);

        $posts = Post::query()
            ->published()
            ->withPublicRelations()
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        return view('public.category', [
            'title' => $category->name,
            'metaTitle' => $category->name.' | '.BlogSettings::get('site_name', 'Ngopi Dulur'),
            'metaDescription' => $category->description ?: 'Kumpulan artikel pada kategori '.$category->name.'.',
            'canonicalUrl' => route('category.show', $category->slug),
            'category' => $category,
            'posts' => $posts,
            'searchTerm' => trim((string) $request->input('q', '')),
        ]);
    }
}
