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
        <div class="space-y-5 sm:space-y-6">
            <div class="flex flex-wrap items-center gap-2 text-[0.65rem] font-semibold uppercase tracking-[0.18em] sm:gap-3 sm:text-xs sm:tracking-[0.22em]">
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

            <div class="space-y-3 sm:space-y-4">
                <h1 class="font-lora text-2xl font-semibold leading-tight text-coffee-900 dark:text-neutralwarm-50 sm:text-4xl lg:text-5xl">
                    {{ $post->title }}
                </h1>
                @if ($post->excerpt)
                    <p class="max-w-3xl text-base leading-7 text-neutralwarm-500 dark:text-neutralwarm-100/75 sm:text-lg sm:leading-8">
                        {{ $post->excerpt }}
                    </p>
                @endif
            </div>
        </div>

        @if ($post->featured_image_url)
            <div class="mt-6 overflow-hidden rounded-2xl border border-coffee-100 bg-white shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900 sm:mt-8 sm:rounded-[2rem]">
                <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?? $post->title }}" class="h-auto w-full object-cover">
            </div>
        @endif

        <div class="mt-8 grid gap-8 sm:mt-10 lg:grid-cols-[minmax(0,1fr)_280px] lg:gap-10">
            <div>
                <div class="post-content">
                    {!! $post->rendered_content !!}
                </div>
            </div>

            <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
                {{-- Share --}}
                <div class="rounded-[1.6rem] border border-coffee-100 bg-white p-5 shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Bagikan</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        {{-- WhatsApp --}}
                        <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-full border border-coffee-100 bg-white px-4 py-2 text-sm font-medium text-[#25D366] transition hover:bg-[#25D366] hover:text-white hover:border-[#25D366] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:hover:bg-[#25D366] dark:hover:border-[#25D366]">
                            <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </a>
                        {{-- X / Twitter --}}
                        <a href="https://x.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-full border border-coffee-100 bg-white px-4 py-2 text-sm font-medium text-gray-800 transition hover:bg-gray-900 hover:text-white hover:border-gray-900 dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-neutralwarm-50 dark:hover:bg-white dark:hover:text-gray-900 dark:hover:border-white">
                            <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            X
                        </a>
                        {{-- Facebook --}}
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-full border border-coffee-100 bg-white px-4 py-2 text-sm font-medium text-[#1877F2] transition hover:bg-[#1877F2] hover:text-white hover:border-[#1877F2] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:hover:bg-[#1877F2] dark:hover:border-[#1877F2]">
                            <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Facebook
                        </a>
                        {{-- LinkedIn --}}
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-full border border-coffee-100 bg-white px-4 py-2 text-sm font-medium text-[#0A66C2] transition hover:bg-[#0A66C2] hover:text-white hover:border-[#0A66C2] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:hover:bg-[#0A66C2] dark:hover:border-[#0A66C2]">
                            <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            LinkedIn
                        </a>
                        {{-- Email --}}
                        <a href="mailto:?subject={{ urlencode($post->title) }}&body={{ urlencode('Baca artikel ini: ' . url()->current()) }}" class="inline-flex items-center gap-2 rounded-full border border-coffee-100 bg-white px-4 py-2 text-sm font-medium text-coffee-700 transition hover:bg-coffee-100 hover:border-coffee-200 dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-coffee-500/15">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Email
                        </a>
                        {{-- Copy Link --}}
                        <button type="button" onclick="navigator.clipboard.writeText('{{ url()->current() }}').then(() => { this.textContent = 'Tersalin!'; setTimeout(() => { this.innerHTML = '<svg class=\'size-4\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' aria-hidden=\'true\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\'/></svg> Salin Link'; }, 2000); })" class="inline-flex items-center gap-2 rounded-full border border-coffee-100 bg-white px-4 py-2 text-sm font-medium text-coffee-700 transition hover:bg-coffee-100 hover:border-coffee-200 dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-coffee-500/15">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Salin Link
                        </button>
                    </div>
                </div>

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
