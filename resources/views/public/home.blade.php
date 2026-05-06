@extends('layouts.public')

@section('content')
    @php
        $heroImage = asset('images/home/coffee-hero.png');
        $hasFeaturedPost = filled($featuredPost);
        $hasLatestPosts = $latestPosts->count() > 0;
        $hasCategories = $categories->count() > 0;
    @endphp

    <div class="ngopi-shell">
        <section class="relative overflow-hidden border-b border-[#ead8c8]/75 dark:border-coffee-700/35">
            <div class="absolute inset-y-0 right-0 hidden w-[56%] lg:block">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_left,rgba(255,248,241,0.95),rgba(255,248,241,0.28)_38%,transparent_70%)] dark:bg-[radial-gradient(circle_at_left,rgba(31,23,19,0.95),rgba(31,23,19,0.55)_42%,transparent_74%)]"></div>
                <img src="{{ $heroImage }}" alt="Secangkir kopi hangat" class="h-full w-full object-cover object-center">
            </div>

            <div class="mx-auto max-w-7xl px-4 py-7 sm:px-6 sm:py-10 lg:px-8 lg:py-16">
                <div class="grid gap-7 lg:grid-cols-[minmax(0,1.08fr)_minmax(340px,0.92fr)] lg:items-center xl:gap-12">
                    <div class="relative z-10 max-w-3xl space-y-6 sm:space-y-7">
                        <div class="ngopi-reveal inline-flex items-center gap-2 rounded-full border border-[#efdfcf] bg-white/82 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-[#8b4a22] shadow-[0_18px_30px_-26px_rgba(90,46,22,0.45)] backdrop-blur-sm dark:border-coffee-700/40 dark:bg-neutralwarm-900/65 dark:text-coffee-100">
                            <span class="size-2 rounded-full bg-[#8b4a22] dark:bg-coffee-100"></span>
                            {{ $heroBadge }}
                        </div>

                        <div class="ngopi-reveal space-y-4" style="animation-delay: 0.08s;">
                            <h1 class="max-w-2xl font-lora text-[2.35rem] font-semibold leading-[1.08] tracking-[-0.03em] text-[#2f1c12] dark:text-neutralwarm-50 sm:text-[3rem] lg:text-[4.05rem]">
                                {{ $heroHeading }}
                            </h1>
                            <p class="max-w-xl text-lg leading-8 text-[#7c695d] dark:text-neutralwarm-100/72">
                                {{ $heroSubheading }}
                            </p>
                        </div>

                        <form action="{{ route('search') }}" method="GET" class="ngopi-reveal max-w-3xl" style="animation-delay: 0.16s;">
                            <label for="home-search" class="sr-only">Cari artikel</label>
                            <div class="flex items-center gap-3 rounded-[1.65rem] border border-[#efddcc] bg-white/90 p-3 shadow-[0_26px_60px_-40px_rgba(90,46,22,0.45)] backdrop-blur-sm dark:border-coffee-700/40 dark:bg-neutralwarm-900/78">
                                <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-[1.1rem] bg-[#fbf2e9] text-[#8b4a22] dark:bg-white/6 dark:text-coffee-100">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M21 21L16.65 16.65M18 10.5C18 14.6421 14.6421 18 10.5 18C6.35786 18 3 14.6421 3 10.5C3 6.35786 6.35786 3 10.5 3C14.6421 3 18 6.35786 18 10.5Z" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>

                                <input
                                    id="home-search"
                                    name="q"
                                    value="{{ $searchTerm ?? '' }}"
                                    type="search"
                                    placeholder="Cari cerita, catatan, atau topik tertentu"
                                    class="min-w-0 flex-1 border-0 bg-transparent text-base text-[#2f1c12] outline-none placeholder:text-[#978378] focus:ring-0 dark:text-neutralwarm-50 dark:placeholder:text-neutralwarm-100/45"
                                >

                                <button type="submit" class="ngopi-solid-button shrink-0 px-6 sm:px-7">
                                    Cari
                                </button>
                            </div>
                        </form>

                        <div class="ngopi-reveal flex flex-wrap gap-3" style="animation-delay: 0.24s;">
                            <a href="#latest-articles" class="ngopi-solid-button">
                                Baca artikel
                            </a>
                            <a href="#popular-categories" class="ngopi-outline-button">
                                Jelajahi kategori
                            </a>
                        </div>

                        <div class="relative mt-2 overflow-hidden rounded-[2.2rem] border border-[#eddcc9] bg-[linear-gradient(140deg,rgba(255,255,255,0.95),rgba(250,241,231,0.82))] p-4 shadow-[0_28px_65px_-42px_rgba(90,46,22,0.45)] backdrop-blur-sm dark:border-coffee-700/40 dark:bg-[linear-gradient(140deg,rgba(31,23,19,0.88),rgba(31,23,19,0.72))] lg:hidden">
                            <div class="absolute inset-y-0 right-0 w-1/2">
                                <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(255,250,245,1),rgba(255,250,245,0.08)_78%)] dark:bg-[linear-gradient(90deg,rgba(31,23,19,1),rgba(31,23,19,0.15)_78%)]"></div>
                                <img src="{{ $heroImage }}" alt="Secangkir kopi hangat" class="h-full w-full object-cover object-center">
                            </div>

                            <div class="relative z-10 max-w-[72%]">
                                <p class="ngopi-section-label">Nuansa ruang baca</p>
                                <p class="mt-3 font-lora text-xl font-semibold leading-tight text-[#2f1c12] dark:text-neutralwarm-50">
                                    Hangat, tenang, dan siap menampung cerita terbaikmu.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="relative hidden min-h-[520px] items-center justify-center lg:flex">
                        <div class="absolute inset-0 overflow-hidden rounded-[2.65rem] border border-[#efddcc] bg-[#fdf6ef] shadow-[0_30px_80px_-46px_rgba(90,46,22,0.4)] dark:border-coffee-700/35 dark:bg-neutralwarm-900/82">
                            <img src="{{ $heroImage }}" alt="Secangkir kopi hangat" class="h-full w-full object-cover object-center">
                            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(255,249,244,0.86)_0%,rgba(255,249,244,0.12)_52%,rgba(255,249,244,0)_74%)] dark:bg-[linear-gradient(90deg,rgba(31,23,19,0.9)_0%,rgba(31,23,19,0.18)_54%,rgba(31,23,19,0)_76%)]"></div>
                        </div>

                        <div class="relative z-10 ml-[-16%] w-full max-w-[21rem] xl:max-w-[22.5rem]">
                            <div class="ngopi-panel overflow-hidden p-5">
                                <div class="flex items-center justify-between gap-4">
                                    <p class="ngopi-section-label">Artikel unggulan</p>
                                    <span class="inline-flex size-11 items-center justify-center rounded-full border border-[#ecd9c8] bg-white/92 text-[#8b4a22] dark:border-coffee-700/40 dark:bg-neutralwarm-900/80 dark:text-coffee-100">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 3.75L14.5467 8.91134L20.25 9.7423L16.125 13.7605L17.0983 19.4387L12 16.7588L6.9017 19.4387L7.875 13.7605L3.75 9.7423L9.4533 8.91134L12 3.75Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>

                                @if ($hasFeaturedPost)
                                    <a href="{{ route('posts.show', $featuredPost->slug) }}" class="group mt-5 block space-y-5">
                                        <div class="overflow-hidden rounded-[1.7rem] bg-[#f7efe5] dark:bg-white/5">
                                            @if ($featuredPost->featured_image_url)
                                                <img src="{{ $featuredPost->featured_image_url }}" alt="{{ $featuredPost->featured_image_alt ?? $featuredPost->title }}" class="h-48 w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                                            @else
                                                <div class="flex h-48 items-center justify-center bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.95),rgba(242,228,214,0.92))] text-[#d2c1b0] dark:bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.08),rgba(255,255,255,0.03))] dark:text-white/20">
                                                    <svg class="size-16" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                                                        <path d="M17 41L27.7 29.9C29 28.54 31.15 28.53 32.46 29.87L38 35.5L43.17 30.17C44.47 28.83 46.62 28.8 47.96 30.1L57 39.5" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                                        <rect x="12" y="14" width="40" height="36" rx="8" stroke="currentColor" stroke-width="4" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="space-y-3">
                                            <span class="inline-flex rounded-full bg-[#f7ebdf] px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-[#8b4a22] dark:bg-coffee-500/18 dark:text-coffee-100">
                                                {{ $featuredPost->category?->name ?? 'Catatan' }}
                                            </span>
                                            <h2 class="font-lora text-[2rem] font-semibold leading-tight text-[#2f1c12] transition group-hover:text-[#8b4a22] dark:text-neutralwarm-50 dark:group-hover:text-coffee-100">
                                                {{ $featuredPost->title }}
                                            </h2>
                                            <p class="text-base leading-8 text-[#7c695d] dark:text-neutralwarm-100/74">
                                                {{ $featuredPost->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($featuredPost->rendered_content ?? ''), 170) }}
                                            </p>
                                            <span class="inline-flex items-center gap-2 text-base font-semibold text-[#8b4a22] dark:text-coffee-100">
                                                Lihat semua artikel
                                                <span aria-hidden="true">&rarr;</span>
                                            </span>
                                        </div>
                                    </a>
                                @else
                                    <div class="mt-5 space-y-5">
                                        <div class="flex h-48 items-center justify-center rounded-[1.7rem] bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.94),rgba(242,228,214,0.92))] text-[#d9c8b7] dark:bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.08),rgba(255,255,255,0.03))] dark:text-white/20">
                                            <svg class="size-16" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                                                <path d="M17 41L27.7 29.9C29 28.54 31.15 28.53 32.46 29.87L38 35.5L43.17 30.17C44.47 28.83 46.62 28.8 47.96 30.1L57 39.5" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                                <rect x="12" y="14" width="40" height="36" rx="8" stroke="currentColor" stroke-width="4" />
                                            </svg>
                                        </div>
                                        <div class="space-y-3">
                                            <span class="inline-flex rounded-full bg-[#f7ebdf] px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-[#8b4a22] dark:bg-coffee-500/18 dark:text-coffee-100">
                                                Catatan
                                            </span>
                                            <h2 class="font-lora text-[2rem] font-semibold leading-tight text-[#2f1c12] dark:text-neutralwarm-50">
                                                Belum ada artikel unggulan
                                            </h2>
                                            <p class="text-base leading-8 text-[#7c695d] dark:text-neutralwarm-100/74">
                                                Begitu ada artikel published, sorotan utama akan tampil di sini.
                                            </p>
                                            <a href="#latest-articles" class="inline-flex items-center gap-2 text-base font-semibold text-[#8b4a22] dark:text-coffee-100">
                                                Lihat semua artikel
                                                <span aria-hidden="true">&rarr;</span>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-12">
            <div class="grid gap-7 lg:grid-cols-[minmax(280px,0.72fr)_minmax(0,1.28fr)] lg:items-start">
                <div id="popular-categories" class="space-y-5">
                    <div class="space-y-2">
                        <p class="ngopi-section-label">Kategori populer</p>
                    </div>

                    <div class="space-y-3">
                        @forelse ($categories->take(3) as $category)
                            <a href="{{ route('category.show', $category) }}" class="ngopi-panel flex items-center gap-4 px-4 py-4 transition hover:-translate-y-0.5 hover:shadow-[0_28px_55px_-38px_rgba(90,46,22,0.4)]">
                                <span class="inline-flex size-[3.25rem] shrink-0 items-center justify-center rounded-full bg-[#fbf2e8] text-[#8b4a22] dark:bg-white/6 dark:text-coffee-100">
                                    @if ($loop->first)
                                        <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M7 5.75H17M7 10.75H17M7 15.75H12M5.75 3.75H18.25C19.3546 3.75 20.25 4.64543 20.25 5.75V18.25C20.25 19.3546 19.3546 20.25 18.25 20.25H5.75C4.64543 20.25 3.75 19.3546 3.75 18.25V5.75C3.75 4.64543 4.64543 3.75 5.75 3.75Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @elseif ($loop->iteration === 2)
                                        <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4.75 6.75H19.25V17.25H4.75V6.75Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                                            <path d="M8 10.25L10.5 12.75L16 7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @else
                                        <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8.25 11.25C9.49264 11.25 10.5 10.2426 10.5 9C10.5 7.75736 9.49264 6.75 8.25 6.75C7.00736 6.75 6 7.75736 6 9C6 10.2426 7.00736 11.25 8.25 11.25Z" stroke="currentColor" stroke-width="1.7" />
                                            <path d="M15.75 17.25C16.9926 17.25 18 16.2426 18 15C18 13.7574 16.9926 12.75 15.75 12.75C14.5074 12.75 13.5 13.7574 13.5 15C13.5 16.2426 14.5074 17.25 15.75 17.25Z" stroke="currentColor" stroke-width="1.7" />
                                            <path d="M9.75 10.5L14.25 13.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                            <path d="M14.25 10.5L9.75 13.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                        </svg>
                                    @endif
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="text-[1.35rem] font-semibold leading-tight text-[#2f1c12] dark:text-neutralwarm-50">
                                        {{ $category->name }}
                                    </p>
                                    <p class="mt-1 truncate text-base leading-7 text-[#7c695d] dark:text-neutralwarm-100/72">
                                        {{ $category->description ?: 'Topik bacaan hangat untuk dinikmati pelan-pelan.' }}
                                    </p>
                                </div>

                                <span class="inline-flex items-center gap-1 text-sm font-semibold text-[#8b4a22] dark:text-coffee-100">
                                    <span>{{ $category->published_posts_count }}</span>
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </a>
                        @empty
                            <div class="ngopi-panel px-5 py-6">
                                <p class="text-base leading-7 text-[#7c695d] dark:text-neutralwarm-100/72">
                                    Kategori belum tersedia. Begitu admin menambahkan kategori aktif, daftar akan muncul di sini.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    @if ($hasCategories)
                        <a href="{{ route('category.show', $categories->first()) }}" class="inline-flex items-center gap-2 text-lg font-semibold text-[#8b4a22] dark:text-coffee-100">
                            Lihat semua kategori
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    @endif
                </div>

                <div id="latest-articles" class="space-y-5">
                    <div class="space-y-2">
                        <p class="ngopi-section-label">Artikel terbaru</p>
                    </div>

                    @if ($hasLatestPosts)
                        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-2">
                            @foreach ($latestPosts as $post)
                                @include('public.partials.post-card', ['post' => $post])
                            @endforeach
                        </div>

                        <div class="pt-1">
                            {{ $latestPosts->links() }}
                        </div>
                    @else
                        <div class="ngopi-panel px-6 py-10 text-center sm:px-10 sm:py-14">
                            <span class="ngopi-empty-icon">
                                <svg class="size-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4.75 4.75H19.25V19.25H4.75V4.75Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                                    <path d="M8 8H16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                    <path d="M8 12H16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                    <path d="M8 16H12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                            </span>
                            <h2 class="mt-6 font-lora text-[2rem] font-semibold leading-tight text-[#2f1c12] dark:text-neutralwarm-50">
                                Belum ada artikel terbaru
                            </h2>
                            <p class="mx-auto mt-4 max-w-xl text-lg leading-8 text-[#7c695d] dark:text-neutralwarm-100/72">
                                Setelah ada artikel published, daftar bacaan akan muncul di sini.
                            </p>
                            <a href="/admin/posts/create" class="ngopi-solid-button mt-7">
                                Tulis artikel pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
