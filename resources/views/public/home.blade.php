@extends('layouts.public')

@push('head')
    <meta name="description" content="{{ $siteDescription }}">
    <meta property="og:title" content="{{ $siteName }}">
    <meta property="og:description" content="{{ $siteDescription }}">
    <meta name="twitter:card" content="summary_large_image">
@endpush

@section('content')
    <section class="border-b border-coffee-100/70 bg-[linear-gradient(180deg,rgba(251,246,241,1)_0%,rgba(255,255,255,0.8)_100%)] dark:border-coffee-700/40 dark:bg-[linear-gradient(180deg,rgba(31,23,19,1)_0%,rgba(31,23,19,0.88)_100%)]">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.15fr_0.85fr] lg:px-8 lg:py-20">
            <div class="space-y-8">
                <div class="inline-flex items-center gap-2 rounded-full border border-coffee-100 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-coffee-100">
                    <span class="size-2 rounded-full bg-coffee-500"></span>
                    {{ $heroBadge }}
                </div>

                <div class="max-w-3xl space-y-5">
                    <h1 class="font-lora text-4xl font-semibold leading-tight text-coffee-900 dark:text-neutralwarm-50 sm:text-5xl lg:text-title-lg">
                        {{ $heroHeading }}
                    </h1>
                    <p class="max-w-2xl text-base leading-8 text-neutralwarm-500 dark:text-neutralwarm-100/75 sm:text-lg">
                        {{ $heroSubheading }}
                    </p>
                </div>

                <form action="{{ route('search') }}" method="GET" class="max-w-2xl">
                    <label for="home-search" class="sr-only">Cari artikel</label>
                    <div class="flex items-center gap-3 rounded-3xl border border-coffee-100 bg-white p-3 shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                        <input
                            id="home-search"
                            name="q"
                            value="{{ $searchTerm ?? '' }}"
                            type="search"
                            placeholder="Cari cerita, catatan, atau topik tertentu"
                            class="min-w-0 flex-1 border-0 bg-transparent px-2 py-2 text-base text-coffee-900 outline-none placeholder:text-neutralwarm-500/70 focus:ring-0 dark:text-neutralwarm-50"
                        >
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-coffee-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-coffee-700">
                            Cari
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                <div class="overflow-hidden rounded-[2rem] border border-coffee-100 bg-white shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                    <div class="border-b border-coffee-100 px-5 py-4 dark:border-coffee-700/40">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Artikel unggulan</p>
                    </div>

                    @if ($featuredPost)
                        <a href="{{ route('posts.show', $featuredPost->slug) }}" class="group block">
                            @if ($featuredPost->featured_image_url)
                                <img src="{{ $featuredPost->featured_image_url }}" alt="{{ $featuredPost->featured_image_alt ?? $featuredPost->title }}" class="h-64 w-full object-cover">
                            @else
                                <div class="flex h-64 items-center justify-center bg-[radial-gradient(circle_at_top,rgba(168,106,58,0.18),transparent_55%),linear-gradient(180deg,rgba(244,232,220,0.6),rgba(251,246,241,1))] text-coffee-700 dark:bg-[radial-gradient(circle_at_top,rgba(168,106,58,0.22),transparent_55%),linear-gradient(180deg,rgba(59,36,20,0.95),rgba(31,23,19,1))] dark:text-coffee-100">
                                    <span class="font-lora text-3xl">Ngopi Dulur</span>
                                </div>
                            @endif

                            <div class="space-y-4 px-5 py-5">
                                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em]">
                                    <span class="rounded-full bg-coffee-100 px-3 py-1 text-coffee-700 dark:bg-coffee-500/20 dark:text-coffee-100">
                                        {{ $featuredPost->category?->name ?? 'Artikel' }}
                                    </span>
                                    <span class="text-neutralwarm-500 dark:text-neutralwarm-100/70">
                                        {{ $featuredPost->published_at?->translatedFormat('d F Y') }}
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <h2 class="font-lora text-2xl font-semibold leading-tight text-coffee-900 transition group-hover:text-coffee-700 dark:text-neutralwarm-50 dark:group-hover:text-coffee-100">
                                        {{ $featuredPost->title }}
                                    </h2>
                                    <p class="line-clamp-3 text-sm leading-7 text-neutralwarm-500 dark:text-neutralwarm-100/75">
                                        {{ $featuredPost->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($featuredPost->rendered_content ?? ''), 180) }}
                                    </p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">
                                        {{ $featuredPost->author?->name ?? 'Admin' }}
                                    </span>
                                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-coffee-700 dark:text-coffee-100">
                                        Baca selengkapnya
                                        <span aria-hidden="true">&rarr;</span>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @else
                        <div class="p-6">
                            @include('public.partials.empty-state', [
                                'title' => 'Belum ada artikel unggulan',
                                'description' => 'Begitu ada artikel published, sorotan utama akan tampil di sini.',
                            ])
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Kategori</p>
                <h2 class="mt-2 font-lora text-2xl font-semibold text-coffee-900 dark:text-neutralwarm-50">Jelajahi per topik</h2>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            @forelse ($categories as $category)
                <a href="{{ route('category.show', $category) }}" class="inline-flex items-center gap-2 rounded-full border border-coffee-100 bg-white px-4 py-2 text-sm font-semibold text-coffee-700 shadow-soft transition hover:-translate-y-0.5 hover:border-coffee-200 hover:bg-coffee-50 dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5">
                    <span>{{ $category->name }}</span>
                    <span class="rounded-full bg-coffee-100 px-2 py-0.5 text-xs text-coffee-700 dark:bg-coffee-500/20 dark:text-coffee-100">
                        {{ $category->published_posts_count }}
                    </span>
                </a>
            @empty
                <div class="rounded-3xl border border-dashed border-coffee-200 bg-white/80 px-6 py-5 text-sm text-neutralwarm-500 dark:border-coffee-700/40 dark:bg-neutralwarm-900/70 dark:text-neutralwarm-100/70">
                    Kategori belum tersedia.
                </div>
            @endforelse
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-14 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Artikel terbaru</p>
                <h2 class="mt-2 font-lora text-2xl font-semibold text-coffee-900 dark:text-neutralwarm-50">Bacaan terbaru</h2>
            </div>
        </div>

        <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($latestPosts as $post)
                @include('public.partials.post-card', ['post' => $post])
            @empty
                <div class="md:col-span-2 xl:col-span-3">
                    @include('public.partials.empty-state', [
                        'title' => 'Belum ada artikel terbaru',
                        'description' => 'Setelah ada artikel published, daftar bacaan akan muncul di sini.',
                    ])
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $latestPosts->links() }}
        </div>
    </section>
@endsection
