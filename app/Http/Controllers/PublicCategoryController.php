<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCategoryController extends Controller
{
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
            'category' => $category,
            'posts' => $posts,
            'searchTerm' => trim((string) $request->input('q', '')),
        ]);
    }
}
