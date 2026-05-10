@extends('layouts.public')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-4">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Artikel</p>
            <h1 class="font-lora text-4xl font-semibold text-coffee-900 dark:text-neutralwarm-50">
                {{ $selectedCategory ? 'Artikel Kategori '.$selectedCategory->name : 'Semua Artikel Published' }}
            </h1>
            <p class="max-w-3xl text-base leading-8 text-neutralwarm-500 dark:text-neutralwarm-100/75">
                {{ $selectedCategory
                    ? 'Seduh semua tulisan published dalam kategori ini, lengkap dengan cerita, catatan, dan insight yang relevan.'
                    : 'Jelajahi seluruh artikel published di Ngopi Dulur dan saring bacaanmu lewat kategori di sidebar.' }}
            </p>
        </div>

        <div class="mt-10 grid gap-8 lg:grid-cols-12 lg:items-start">
            <aside class="lg:col-span-4 xl:col-span-3">
                <div class="lg:sticky lg:top-28">
                    <div class="overflow-hidden rounded-[1.8rem] border border-coffee-100 bg-white shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                        <div class="border-b border-coffee-100 px-5 py-5 dark:border-coffee-700/40">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Filter Kategori</p>
                            <h2 class="mt-2 font-lora text-2xl font-semibold text-coffee-900 dark:text-neutralwarm-50">Side Navbar Topik</h2>
                            <p class="mt-2 text-sm leading-7 text-neutralwarm-500 dark:text-neutralwarm-100/70">
                                Klik kategori untuk langsung menyaring kumpulan artikel yang tampil.
                            </p>
                        </div>

                        <div class="max-h-[70vh] space-y-2 overflow-y-auto px-4 py-4">
                            <a
                                href="{{ route('posts.index') }}"
                                class="flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold transition {{ $selectedCategory === null ? 'bg-coffee-700 text-white shadow-soft dark:bg-coffee-500/80' : 'text-coffee-900 hover:bg-coffee-50 dark:text-neutralwarm-50 dark:hover:bg-white/5' }}"
                            >
                                <span>Semua Artikel</span>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs {{ $selectedCategory === null ? 'bg-white/15 text-white' : 'bg-coffee-100 text-coffee-700 dark:bg-coffee-500/15 dark:text-coffee-100' }}">
                                    {{ $posts->total() }}
                                </span>
                            </a>

                            @foreach ($categories as $category)
                                <a
                                    href="{{ route('posts.index', ['category' => $category->slug]) }}"
                                    class="flex items-start justify-between gap-3 rounded-2xl px-4 py-3 transition {{ $selectedCategory?->is($category) ? 'bg-coffee-700 text-white shadow-soft dark:bg-coffee-500/80' : 'hover:bg-coffee-50 dark:hover:bg-white/5' }}"
                                >
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold {{ $selectedCategory?->is($category) ? 'text-white' : 'text-coffee-900 dark:text-neutralwarm-50' }}">
                                            {{ $category->name }}
                                        </p>
                                        <p class="mt-1 line-clamp-2 text-xs leading-6 {{ $selectedCategory?->is($category) ? 'text-white/80' : 'text-neutralwarm-500 dark:text-neutralwarm-100/65' }}">
                                            {{ $category->description ?: 'Kumpulan tulisan hangat yang relevan dengan topik ini.' }}
                                        </p>
                                    </div>
                                    <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $selectedCategory?->is($category) ? 'bg-white/15 text-white' : 'bg-coffee-100 text-coffee-700 dark:bg-coffee-500/15 dark:text-coffee-100' }}">
                                        {{ $category->published_posts_count }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            <div class="min-w-0 space-y-6 lg:col-span-8 xl:col-span-9">
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-[1.8rem] border border-coffee-100 bg-white px-5 py-4 shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-coffee-900 dark:text-neutralwarm-50">
                            {{ $selectedCategory ? 'Kategori aktif: '.$selectedCategory->name : 'Menampilkan semua artikel published' }}
                        </p>
                        <p class="mt-1 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">
                            {{ $posts->total() }} artikel ditemukan
                        </p>
                    </div>

                    @if ($selectedCategory)
                        <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-coffee-100 px-4 py-3 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-700/40 dark:text-coffee-100 dark:hover:bg-white/5">
                            Reset filter
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-2 2xl:grid-cols-3">
                    @forelse ($posts as $post)
                        @include('public.partials.post-card', ['post' => $post])
                    @empty
                        <div class="xl:col-span-2 2xl:col-span-3">
                            @include('public.partials.empty-state', [
                                'title' => 'Belum ada artikel untuk filter ini',
                                'description' => 'Coba pilih kategori lain atau kembali ke semua artikel published.',
                            ])
                        </div>
                    @endforelse
                </div>

                <div class="pt-2">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
