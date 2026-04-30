<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Support\BlogSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicSearchController extends Controller
{
    public function index(Request $request): View
    {
        $queryTerm = trim((string) $request->input('q', $request->input('search', '')));

        $posts = Post::query()
            ->published()
            ->withPublicRelations()
            ->when($queryTerm !== '', function (Builder $query) use ($queryTerm) {
                if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
                    $query->whereFullText(['title', 'excerpt', 'content'], $queryTerm);

                    return;
                }

                $query->where(function (Builder $searchQuery) use ($queryTerm) {
                    $searchQuery->where('title', 'like', '%'.$queryTerm.'%')
                        ->orWhere('excerpt', 'like', '%'.$queryTerm.'%')
                        ->orWhere('content', 'like', '%'.$queryTerm.'%');
                });
                $query->latest('published_at')
                    ->latest('id');
            }, function (Builder $query) {
                $query->latest('published_at')
                    ->latest('id');
            })
            ->paginate(9)
            ->withQueryString();

        return view('public.search', [
            'title' => $queryTerm !== '' ? 'Hasil pencarian untuk "'.$queryTerm.'"' : 'Cari Artikel',
            'metaTitle' => $queryTerm !== '' ? 'Pencarian: '.$queryTerm.' | '.BlogSettings::get('site_name', 'Ngopi Dulur') : 'Cari Artikel | '.BlogSettings::get('site_name', 'Ngopi Dulur'),
            'metaDescription' => 'Hasil pencarian artikel Ngopi Dulur',
            'canonicalUrl' => route('search'),
            'metaRobots' => 'noindex,follow',
            'posts' => $posts,
            'searchTerm' => $queryTerm,
        ]);
    }
}
