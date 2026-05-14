@extends('layouts.public')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <div class="space-y-3">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Artikel</p>
            <h1 class="font-lora text-3xl font-semibold text-coffee-900 dark:text-neutralwarm-50 sm:text-4xl">
                {{ $selectedCategory ? 'Kategori: '.$selectedCategory->name : 'Semua Artikel' }}
            </h1>
            <p class="max-w-3xl text-sm leading-7 text-neutralwarm-500 dark:text-neutralwarm-100/75 sm:text-base sm:leading-8">
                {{ $selectedCategory
                    ? 'Semua tulisan dalam kategori ini.'
                    : 'Jelajahi seluruh artikel dan saring lewat kategori.' }}
            </p>
        </div>

        {{-- Mobile: Filter kategori collapsible --}}
        <div class="mt-6 lg:hidden" x-data="{ open: {{ $selectedCategory ? 'true' : 'false' }} }">
            <button
                @click="open = !open"
                type="button"
                class="flex w-full items-center justify-between rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm font-semibold text-coffee-900 shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-neutralwarm-50"
            >
                <span class="flex items-center gap-2">
                    <svg class="size-4 text-coffee-700 dark:text-coffee-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M7 8h10M11 12h4"/></svg>
                    {{ $selectedCategory ? 'Kategori: '.$selectedCategory->name : 'Filter Kategori' }}
                </span>
                <svg class="size-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mt-2 overflow-hidden rounded-2xl border border-coffee-100 bg-white shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                <div class="space-y-1 p-3">
                    <a href="{{ route('posts.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $selectedCategory === null ? 'bg-coffee-700 text-white dark:bg-coffee-500/80' : 'text-coffee-900 hover:bg-coffee-50 dark:text-neutralwarm-50 dark:hover:bg-white/5' }}">
                        <span>Semua Artikel</span>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs {{ $selectedCategory === null ? 'bg-white/20 text-white' : 'bg-coffee-100 text-coffee-700 dark:bg-coffee-500/15 dark:text-coffee-100' }}">{{ $posts->total() }}</span>
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('posts.index', ['category' => $category->slug]) }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $selectedCategory?->is($category) ? 'bg-coffee-700 text-white dark:bg-coffee-500/80' : 'text-coffee-900 hover:bg-coffee-50 dark:text-neutralwarm-50 dark:hover:bg-white/5' }}">
                            <span>{{ $category->name }}</span>
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs {{ $selectedCategory?->is($category) ? 'bg-white/20 text-white' : 'bg-coffee-100 text-coffee-700 dark:bg-coffee-500/15 dark:text-coffee-100' }}">{{ $category->published_posts_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-12 lg:items-start">
            {{-- Desktop: Sidebar filter --}}
            <aside class="hidden lg:col-span-4 lg:block xl:col-span-3">
                <div class="lg:sticky lg:top-28">
                    <div class="overflow-hidden rounded-[1.8rem] border border-coffee-100 bg-white shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                        <div class="border-b border-coffee-100 px-5 py-5 dark:border-coffee-700/40">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Filter Kategori</p>
                            <h2 class="mt-2 font-lora text-xl font-semibold text-coffee-900 dark:text-neutralwarm-50">Topik Bacaan</h2>
                        </div>
                        <div class="max-h-[70vh] space-y-1 overflow-y-auto px-3 py-3">
                            <a href="{{ route('posts.index') }}" class="flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold transition {{ $selectedCategory === null ? 'bg-coffee-700 text-white shadow-soft dark:bg-coffee-500/80' : 'text-coffee-900 hover:bg-coffee-50 dark:text-neutralwarm-50 dark:hover:bg-white/5' }}">
                                <span>Semua Artikel</span>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs {{ $selectedCategory === null ? 'bg-white/15 text-white' : 'bg-coffee-100 text-coffee-700 dark:bg-coffee-500/15 dark:text-coffee-100' }}">{{ $posts->total() }}</span>
                            </a>
                            @foreach ($categories as $category)
                                <a href="{{ route('posts.index', ['category' => $category->slug]) }}" class="flex items-start justify-between gap-3 rounded-2xl px-4 py-3 transition {{ $selectedCategory?->is($category) ? 'bg-coffee-700 text-white shadow-soft dark:bg-coffee-500/80' : 'hover:bg-coffee-50 dark:hover:bg-white/5' }}">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold {{ $selectedCategory?->is($category) ? 'text-white' : 'text-coffee-900 dark:text-neutralwarm-50' }}">{{ $category->name }}</p>
                                        <p class="mt-0.5 line-clamp-2 text-xs leading-5 {{ $selectedCategory?->is($category) ? 'text-white/80' : 'text-neutralwarm-500 dark:text-neutralwarm-100/65' }}">{{ $category->description ?: 'Kumpulan tulisan hangat.' }}</p>
                                    </div>
                                    <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $selectedCategory?->is($category) ? 'bg-white/15 text-white' : 'bg-coffee-100 text-coffee-700 dark:bg-coffee-500/15 dark:text-coffee-100' }}">{{ $category->published_posts_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            <div class="min-w-0 space-y-5 lg:col-span-8 xl:col-span-9">
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-coffee-100 bg-white px-4 py-3 shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900 sm:rounded-[1.8rem] sm:px-5 sm:py-4">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-coffee-900 dark:text-neutralwarm-50">
                            {{ $selectedCategory ? 'Kategori: '.$selectedCategory->name : 'Semua artikel published' }}
                        </p>
                        <p class="mt-0.5 text-xs text-neutralwarm-500 dark:text-neutralwarm-100/70 sm:text-sm">
                            {{ $posts->total() }} artikel ditemukan
                        </p>
                    </div>
                    @if ($selectedCategory)
                        <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-coffee-100 px-3 py-2 text-xs font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-700/40 dark:text-coffee-100 dark:hover:bg-white/5 sm:rounded-2xl sm:px-4 sm:py-3 sm:text-sm">
                            Reset filter
                            <span aria-hidden="true">&times;</span>
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 xl:grid-cols-2 2xl:grid-cols-3">
                    @forelse ($posts as $post)
                        @include('public.partials.post-card', ['post' => $post])
                    @empty
                        <div class="sm:col-span-2 xl:col-span-2 2xl:col-span-3">
                            @include('public.partials.empty-state', [
                                'title' => 'Belum ada artikel untuk filter ini',
                                'description' => 'Coba pilih kategori lain atau kembali ke semua artikel.',
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
