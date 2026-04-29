@extends('layouts.public')

@push('head')
    <meta name="description" content="{{ $metaDescription ?? $post->excerpt ?? $defaultMetaDescription ?? $post->title }}">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="{{ route('posts.show', $post->slug) }}">

    <meta property="og:title" content="{{ $title ?? $post->title }}">
    <meta property="og:description" content="{{ $metaDescription ?? $post->excerpt ?? $defaultMetaDescription ?? $post->title }}">
    <meta property="og:type" content="article">
    @if ($post->featured_image_url)
        <meta property="og:image" content="{{ $post->featured_image_url }}">
    @endif
@endpush

@section('content')
    <article class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <div class="space-y-6">
            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.22em]">
                <a href="{{ route('category.show', $post->category) }}" class="rounded-full bg-coffee-100 px-3 py-1 text-coffee-700 dark:bg-coffee-500/20 dark:text-coffee-100">
                    {{ $post->category?->name ?? 'Artikel' }}
                </a>
                <span class="text-neutralwarm-500 dark:text-neutralwarm-100/70">
                    {{ $post->published_at?->translatedFormat('d F Y') }}
                </span>
                <span class="text-neutralwarm-500 dark:text-neutralwarm-100/70">
                    {{ $post->author?->name ?? 'Admin' }}
                </span>
                @if ($post->reading_time_minutes)
                    <span class="text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ $post->reading_time_minutes }} menit baca</span>
                @endif
            </div>

            <div class="space-y-4">
                <h1 class="font-lora text-4xl font-semibold leading-tight text-coffee-900 dark:text-neutralwarm-50 sm:text-5xl">
                    {{ $post->title }}
                </h1>
                @if ($post->excerpt)
                    <p class="max-w-3xl text-lg leading-8 text-neutralwarm-500 dark:text-neutralwarm-100/75">
                        {{ $post->excerpt }}
                    </p>
                @endif
            </div>
        </div>

        @if ($post->featured_image_url)
            <div class="mt-8 overflow-hidden rounded-[2rem] border border-coffee-100 bg-white shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?? $post->title }}" class="h-auto w-full object-cover">
            </div>
        @endif

        <div class="mt-10 grid gap-10 lg:grid-cols-[minmax(0,1fr)_280px]">
            <div>
                <div class="post-content">
                    {!! $post->rendered_content !!}
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-[1.6rem] border border-coffee-100 bg-white p-5 shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Tags</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse ($post->tags as $tag)
                            <a href="{{ route('tag.show', $tag) }}" class="rounded-full bg-neutralwarm-100 px-3 py-1 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-100 dark:bg-white/5 dark:text-neutralwarm-50 dark:hover:bg-coffee-500/15">
                                {{ $tag->name }}
                            </a>
                        @empty
                            <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Belum ada tag.</p>
                        @endforelse
                    </div>
                </div>

                @if ($relatedPosts->isNotEmpty())
                    <div class="rounded-[1.6rem] border border-coffee-100 bg-white p-5 shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Artikel terkait</p>
                        <div class="mt-4 space-y-4">
                            @foreach ($relatedPosts as $relatedPost)
                                <a href="{{ route('posts.show', $relatedPost->slug) }}" class="group flex gap-3">
                                    <div class="size-16 shrink-0 overflow-hidden rounded-2xl bg-coffee-100 dark:bg-coffee-500/15">
                                        @if ($relatedPost->featured_image_url)
                                            <img src="{{ $relatedPost->featured_image_url }}" alt="{{ $relatedPost->featured_image_alt ?? $relatedPost->title }}" class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-coffee-900 transition group-hover:text-coffee-700 dark:text-neutralwarm-50 dark:group-hover:text-coffee-100">
                                            {{ $relatedPost->title }}
                                        </p>
                                        <p class="mt-1 text-xs text-neutralwarm-500 dark:text-neutralwarm-100/70">
                                            {{ $relatedPost->published_at?->translatedFormat('d M Y') }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </article>
@endsection
