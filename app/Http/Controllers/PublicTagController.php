<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Support\BlogSettings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicTagController extends Controller
{
    public function show(Request $request, Tag $tag): View
    {
        $posts = Post::query()
            ->published()
            ->withPublicRelations()
            ->whereHas('tags', fn ($query) => $query->whereKey($tag->id))
            ->latest('published_at')
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        return view('public.tag', [
            'title' => $tag->name,
            'metaTitle' => $tag->name.' | '.BlogSettings::get('site_name', 'Ngopi Dulur'),
            'metaDescription' => 'Kumpulan artikel dengan tag '.$tag->name.'.',
            'canonicalUrl' => route('tag.show', $tag->slug),
            'tag' => $tag,
            'posts' => $posts,
            'searchTerm' => trim((string) $request->input('q', '')),
        ]);
    }
}
