content = '''<?php

namespace App\\Http\\Controllers\\AdminApi;

use App\\Http\\Controllers\\Controller;
use App\\Models\\Category;
use App\\Models\\Post;
use App\\Models\\Tag;
use Illuminate\\Http\\JsonResponse;
use Illuminate\\Http\\Request;

class SearchController extends Controller
{
    public function index(Request \): JsonResponse
    {
        \ = trim((string) \->string('q'));

        if (strlen(\) < 2) {
            return response()->json([
                'query' => \,
                'results' => [
                    'posts' => [],
                    'categories' => [],
                    'tags' => [],
                ],
                'total' => 0,
            ]);
        }

        \ = Post::query()
            ->with(['category:id,name,slug', 'author:id,name'])
            ->where(function (\) use (\) {
                \->where('title', 'like', '%' . \ . '%')
                    ->orWhere('excerpt', 'like', '%' . \ . '%');
            })
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(fn (Post \) => [
                'id' => \->id,
                'title' => \->title,
                'slug' => \->slug,
                'status' => \->status,
                'category_name' => \->category?->name,
                'author_name' => \->author?->name,
                'updated_at' => \->updated_at?->toDateTimeString(),
                'url' => url('/admin/posts/' . \->id . '/edit'),
                'type' => 'post',
            ]);

        \ = Category::query()
            ->where('name', 'like', '%' . \ . '%')
            ->orderBy('name')
            ->limit(5)
            ->get()
            ->map(fn (Category \) => [
                'id' => \->id,
                'name' => \->name,
                'slug' => \->slug,
                'url' => url('/admin/categories'),
                'type' => 'category',
            ]);

        \ = Tag::query()
            ->where('name', 'like', '%' . \ . '%')
            ->orderBy('name')
            ->limit(5)
            ->get()
            ->map(fn (Tag \) => [
                'id' => \->id,
                'name' => \->name,
                'slug' => \->slug,
                'url' => url('/admin/tags'),
                'type' => 'tag',
            ]);

        \ = \->count() + \->count() + \->count();

        return response()->json([
            'query' => \,
            'results' => [
                'posts' => \,
                'categories' => \,
                'tags' => \,
            ],
            'total' => \,
        ]);
    }
}
'''

with open(r'D:\Github\ngopidulur-blog\app\Http\Controllers\AdminApi\SearchController.php', 'w', encoding='utf-8', newline='\n') as f:
    f.write(content)

print('Done')
