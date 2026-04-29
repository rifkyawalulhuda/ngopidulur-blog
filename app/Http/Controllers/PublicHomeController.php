<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Support\BlogSettings;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PublicHomeController extends Controller
{
    public function index(): View
    {
        if (! Schema::hasTable('posts') || ! Schema::hasTable('categories')) {
            return view('public.home', [
                'title' => 'Ngopi Dulur',
                'siteName' => 'Ngopi Dulur',
                'siteTagline' => 'Warm Coffee Meets Modern Tech',
                'siteDescription' => 'Blog pribadi hangat untuk catatan, ide, dan tulisan santai.',
                'heroBadge' => 'Ngopi Dulur',
                'heroHeading' => 'Cerita, catatan, dan pikiran ringan yang enak dibaca sambil ngopi.',
                'heroSubheading' => 'Seduh bacaan terbaru dari ruang tulis pribadi yang modern dan hangat.',
                'footerNote' => 'Dibuat dengan Laravel, Vue, dan secangkir kopi yang pelan-pelan habis.',
                'featuredPost' => null,
                'latestPosts' => $this->emptyPaginator(),
                'categories' => collect(),
                'searchTerm' => trim((string) request()->input('q', '')),
            ]);
        }

        $featuredPost = $this->featuredPost();

        $latestPostsQuery = Post::query()
            ->published()
            ->withPublicRelations()
            ->when($featuredPost, fn ($query) => $query->whereKeyNot($featuredPost->id))
            ->latest('published_at')
            ->latest('id');

        $latestPosts = $latestPostsQuery
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('posts', fn ($query) => $query->published())
            ->withCount([
                'posts as published_posts_count' => fn ($query) => $query->published(),
            ])
            ->orderByDesc('published_posts_count')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('public.home', [
            'title' => BlogSettings::get('site_name', 'Ngopi Dulur'),
            'siteName' => BlogSettings::get('site_name', 'Ngopi Dulur'),
            'siteTagline' => BlogSettings::get('site_tagline', 'Warm Coffee Meets Modern Tech'),
            'siteDescription' => BlogSettings::get('site_description', 'Blog pribadi hangat untuk catatan, ide, dan tulisan santai.'),
            'heroBadge' => BlogSettings::get('hero_badge', 'Ngopi Dulur'),
            'heroHeading' => BlogSettings::get('hero_heading', 'Cerita, catatan, dan pikiran ringan yang enak dibaca sambil ngopi.'),
            'heroSubheading' => BlogSettings::get('hero_subheading', 'Seduh bacaan terbaru dari ruang tulis pribadi yang modern dan hangat.'),
            'footerNote' => BlogSettings::get('footer_note', 'Dibuat dengan Laravel, Vue, dan secangkir kopi yang pelan-pelan habis.'),
            'featuredPost' => $featuredPost,
            'latestPosts' => $latestPosts,
            'categories' => $categories,
            'searchTerm' => trim((string) request()->input('q', '')),
        ]);
    }

    private function featuredPost(): ?Post
    {
        $query = Post::query()
            ->published()
            ->withPublicRelations()
            ->latest('published_at')
            ->latest('id');

        return (clone $query)->where('is_featured', true)->first()
            ?? $query->first();
    }

    private function emptyPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            [],
            0,
            9,
            1,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }
}
