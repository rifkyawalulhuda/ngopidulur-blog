@extends('layouts.public')

@section('content')
    <div
        data-reading-progress-root
        class="pointer-events-none fixed inset-x-0 top-0 z-[70] h-1.5 bg-transparent"
        aria-hidden="true"
    >
        <div
            data-reading-progress-bar
            class="h-full w-0 rounded-r-full bg-[linear-gradient(90deg,#8b4a22_0%,#c67a3e_100%)] shadow-[0_8px_20px_-10px_rgba(139,74,34,0.75)] transition-[width] duration-150 dark:bg-[linear-gradient(90deg,#f0c8a8_0%,#c67a3e_100%)]"
        ></div>
    </div>

    <button
        type="button"
        data-scroll-top
        class="pointer-events-none fixed bottom-4 right-4 z-[65] inline-flex translate-y-4 items-center gap-2 rounded-full border border-coffee-100 bg-white/96 px-4 py-3 text-sm font-semibold text-coffee-700 opacity-0 shadow-[0_22px_45px_-24px_rgba(59,36,20,0.55)] backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:bg-[#fff8f1] dark:border-coffee-700/40 dark:bg-neutralwarm-900/92 dark:text-coffee-100 dark:hover:bg-white/8 sm:bottom-6 sm:right-6 sm:px-5"
        aria-label="Kembali ke atas"
    >
        <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M10 15V5M10 5L5.5 9.5M10 5L14.5 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span class="hidden sm:inline">Ke atas</span>
    </button>

    <article data-reading-article class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
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

            <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
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

                <div class="rounded-[1.6rem] border border-coffee-100 bg-white p-5 shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Artikel terkait</p>
                    <div class="mt-4 space-y-4">
                        @forelse ($relatedPosts as $relatedPost)
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
                        @empty
                            <p class="text-sm leading-7 text-neutralwarm-500 dark:text-neutralwarm-100/70">
                                Belum ada artikel lain yang bisa direkomendasikan untuk bacaan selanjutnya.
                            </p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </article>
@endsection
