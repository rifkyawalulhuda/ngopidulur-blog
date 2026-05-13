<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = trim((string) $request->string("q"));

        if (strlen($query) < 2) {
            return response()->json([
                "query" => $query,
                "results" => [
                    "posts" => [],
                    "categories" => [],
                    "tags" => [],
                ],
                "total" => 0,
            ]);
        }

        $posts = Post::query()
            ->with(["category:id,name,slug", "author:id,name"])
            ->where(function ($q) use ($query) {
                $q->where("title", "like", "%" . $query . "%")
                    ->orWhere("excerpt", "like", "%" . $query . "%");
            })
            ->orderByDesc("updated_at")
            ->limit(5)
            ->get()
            ->map(fn (Post $post) => [
                "id" => $post->id,
                "title" => $post->title,
                "slug" => $post->slug,
                "status" => $post->status,
                "category_name" => $post->category?->name,
                "author_name" => $post->author?->name,
                "updated_at" => $post->updated_at?->toDateTimeString(),
                "url" => url("/admin/posts/" . $post->id . "/edit"),
                "type" => "post",
            ]);

        $categories = Category::query()
            ->where("name", "like", "%" . $query . "%")
            ->orderBy("name")
            ->limit(5)
            ->get()
            ->map(fn (Category $category) => [
                "id" => $category->id,
                "name" => $category->name,
                "slug" => $category->slug,
                "url" => url("/admin/categories"),
                "type" => "category",
            ]);

        $tags = Tag::query()
            ->where("name", "like", "%" . $query . "%")
            ->orderBy("name")
            ->limit(5)
            ->get()
            ->map(fn (Tag $tag) => [
                "id" => $tag->id,
                "name" => $tag->name,
                "slug" => $tag->slug,
                "url" => url("/admin/tags"),
                "type" => "tag",
            ]);

        $total = $posts->count() + $categories->count() + $tags->count();

        return response()->json([
            "query" => $query,
            "results" => [
                "posts" => $posts,
                "categories" => $categories,
                "tags" => $tags,
            ],
            "total" => $total,
        ]);
    }
}