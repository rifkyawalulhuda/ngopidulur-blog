@extends('layouts.public')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-4">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Kategori</p>
            <h1 class="font-lora text-4xl font-semibold text-coffee-900 dark:text-neutralwarm-50">Semua Kategori</h1>
            <p class="max-w-3xl text-base leading-8 text-neutralwarm-500 dark:text-neutralwarm-100/75">
                Pilih kategori yang ingin kamu jelajahi untuk menemukan kumpulan tulisan dengan topik yang paling relevan.
            </p>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($categories as $category)
                <a href="{{ route('category.show', $category) }}" class="ngopi-panel flex h-full flex-col gap-4 px-5 py-5 transition hover:-translate-y-0.5 hover:shadow-[0_28px_55px_-38px_rgba(90,46,22,0.4)]">
                    <div class="flex items-start justify-between gap-4">
                        <span class="inline-flex size-[3.25rem] shrink-0 items-center justify-center rounded-full bg-[#fbf2e8] text-[#8b4a22] dark:bg-white/6 dark:text-coffee-100">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M8 7H16M8 12H16M8 17H13M7 3H17C18.1046 3 19 3.89543 19 5V19C19 20.1046 18.1046 21 17 21H7C5.89543 21 5 20.1046 5 19V5C5 3.89543 5.89543 3 7 3Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>

                        <span class="inline-flex items-center rounded-full border border-[#e7d6c7] bg-white/80 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-[#8b4a22] dark:border-coffee-700/40 dark:bg-white/5 dark:text-coffee-100">
                            {{ $category->published_posts_count }} tulisan
                        </span>
                    </div>

                    <div class="space-y-2">
                        <h2 class="text-2xl font-semibold text-[#2f1c12] dark:text-neutralwarm-50">{{ $category->name }}</h2>
                        <p class="line-clamp-3 text-sm leading-7 text-[#8a776b] dark:text-neutralwarm-100/70">
                            {{ $category->description ?: 'Kategori pilihan untuk membaca tulisan-tulisan hangat dari Ngopi Dulur.' }}
                        </p>
                    </div>

                    <div class="mt-auto flex items-center justify-between pt-2 text-sm font-semibold text-[#8b4a22] dark:text-coffee-100">
                        <span>Buka kategori</span>
                        <span aria-hidden="true">&rarr;</span>
                    </div>
                </a>
            @empty
                <div class="md:col-span-2 xl:col-span-3">
                    @include('public.partials.empty-state', [
                        'title' => 'Belum ada kategori aktif',
                        'description' => 'Kategori akan muncul di sini setelah memiliki artikel published.',
                    ])
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $categories->links() }}
        </div>
    </section>
@endsection
