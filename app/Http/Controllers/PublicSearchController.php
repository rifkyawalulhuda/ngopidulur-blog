<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Support\BlogSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicSearchController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $queryTerm = trim((string) $request->input('q', $request->input('search', '')));
        $posts = $this->searchPosts($queryTerm);
        $isHeroContext = $request->string('context')->toString() === 'hero';

        if ($request->boolean('ajax') || $request->expectsJson()) {
            $suggestions = $isHeroContext
                ? $posts->getCollection()->take(5)->values()
                : collect();

            return response()->json([
                'title' => $this->pageTitle($queryTerm),
                'results_html' => view('public.partials.search-results', [
                    'posts' => $posts,
                    'searchTerm' => $queryTerm,
                ])->render(),
                'suggestions_html' => $isHeroContext
                    ? view('public.partials.search-suggestions', [
                        'posts' => $suggestions,
                        'searchTerm' => $queryTerm,
                        'total' => $posts->total(),
                    ])->render()
                    : null,
                'count_label' => $queryTerm !== ''
                    ? $posts->total().' artikel ditemukan'
                    : 'Mulai ketik untuk melihat artikel terkait secara realtime.',
                'query' => $queryTerm,
            ]);
        }

        return view('public.search', [
            'title' => $this->pageTitle($queryTerm),
            'metaTitle' => $queryTerm !== ''
                ? 'Pencarian: '.$queryTerm.' | '.BlogSettings::get('site_name', 'Ngopi Dulur')
                : 'Cari Artikel | '.BlogSettings::get('site_name', 'Ngopi Dulur'),
            'metaDescription' => 'Hasil pencarian artikel Ngopi Dulur',
            'canonicalUrl' => route('search'),
            'metaRobots' => 'noindex,follow',
            'posts' => $posts,
            'searchTerm' => $queryTerm,
        ]);
    }

    private function searchPosts(string $queryTerm): LengthAwarePaginator
    {
        return Post::query()
            ->published()
            ->withPublicRelations()
            ->when($queryTerm !== '', function (Builder $query) use ($queryTerm) {
                $normalizedTerm = preg_replace('/\s+/u', ' ', $queryTerm) ?? $queryTerm;
                $isMysql = in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
                $tokens = preg_split('/\s+/u', $normalizedTerm, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $hasShortToken = collect($tokens)->contains(fn (string $token) => mb_strlen($token) < 3);

                $query->where(function (Builder $searchQuery) use ($normalizedTerm, $isMysql, $hasShortToken) {
                    if ($isMysql && ! $hasShortToken && mb_strlen($normalizedTerm) >= 3) {
                        $searchQuery->whereFullText(['title', 'excerpt', 'content'], $normalizedTerm)
                            ->orWhere('title', 'like', '%'.$normalizedTerm.'%')
                            ->orWhere('excerpt', 'like', '%'.$normalizedTerm.'%')
                            ->orWhere('content', 'like', '%'.$normalizedTerm.'%');

                        return;
                    }

                    $searchQuery->where('title', 'like', '%'.$normalizedTerm.'%')
                        ->orWhere('excerpt', 'like', '%'.$normalizedTerm.'%')
                        ->orWhere('content', 'like', '%'.$normalizedTerm.'%');
                });
            })
            ->latest('published_at')
            ->latest('id')
            ->paginate(9)
            ->withQueryString();
    }

    private function pageTitle(string $queryTerm): string
    {
        return $queryTerm !== '' ? 'Hasil pencarian untuk "'.$queryTerm.'"' : 'Cari artikel';
    }
}
