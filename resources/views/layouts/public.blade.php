<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full {{ ($blogTheme ?? 'light') === 'dark' ? 'dark' : '' }}" data-theme="{{ $blogTheme ?? 'light' }}">

<head>
    @php
        $resolvedTitle = trim((string) ($metaTitle ?? $title ?? data_get($blogSettings ?? [], 'default_meta_title', data_get($blogSettings ?? [], 'site_name', 'Ngopi Dulur'))));
        $resolvedDescription = trim((string) ($metaDescription ?? data_get($blogSettings ?? [], 'default_meta_description', data_get($blogSettings ?? [], 'site_description', 'Blog pribadi hangat untuk catatan, ide, dan tulisan santai.'))));
        $resolvedCanonical = $canonicalUrl ?? url()->current();
        $resolvedRobots = $metaRobots ?? 'index,follow';
        $resolvedOgTitle = trim((string) ($ogTitle ?? $resolvedTitle));
        $resolvedOgDescription = trim((string) ($ogDescription ?? $resolvedDescription));
        $resolvedOgType = $ogType ?? 'website';
        $resolvedOgImage = $ogImage ?? data_get($blogSettingAssets ?? [], 'default_og_image_url');
        $siteName = data_get($blogSettings ?? [], 'site_name', 'Ngopi Dulur');
        $siteTagline = data_get($blogSettings ?? [], 'site_tagline', 'Warm Coffee Meets Modern Tech');
        $homeUrl = route('home');
        $homeSections = [
            'articles' => $homeUrl.'#latest-articles',
            'categories' => $homeUrl.'#popular-categories',
            'about' => $homeUrl.'#site-footer',
        ];
        $isHome = request()->routeIs('home');
        $searchHref = $isHome ? '#home-search' : route('search');
        $socialGithub = data_get($blogSocialLinks ?? [], 'github', '#');
        $socialInstagram = data_get($blogSocialLinks ?? [], 'instagram', '#');
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        (function () {
            const storageKey = 'ngopi-dulur-theme';
            const storedTheme = localStorage.getItem(storageKey);
            const defaultTheme = document.documentElement.dataset.theme || 'light';
            const theme = storedTheme || defaultTheme;

            document.documentElement.classList.toggle('dark', theme === 'dark');
            document.documentElement.dataset.theme = theme;
        })();
    </script>

    <title>{{ $resolvedTitle }}</title>
    <meta name="description" content="{{ $resolvedDescription }}">
    <meta name="robots" content="{{ $resolvedRobots }}">
    <link rel="canonical" href="{{ $resolvedCanonical }}">

    <meta property="og:title" content="{{ $resolvedOgTitle }}">
    <meta property="og:description" content="{{ $resolvedOgDescription }}">
    <meta property="og:type" content="{{ $resolvedOgType }}">
    <meta property="og:url" content="{{ $resolvedCanonical }}">
    @if ($resolvedOgImage)
        <meta property="og:image" content="{{ $resolvedOgImage }}">
        <meta name="twitter:card" content="summary_large_image">
    @else
        <meta name="twitter:card" content="summary">
    @endif

    @if (data_get($blogSettingAssets ?? [], 'favicon_url'))
        <link rel="icon" href="{{ data_get($blogSettingAssets, 'favicon_url') }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/public.js'])

    @stack('head')
</head>

<body class="min-h-full bg-[#fbf6ef] font-sans text-[#2f1c12] dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
    <div class="flex min-h-screen flex-col">
        <header class="sticky top-0 z-40 border-b border-[#ead8c8]/85 bg-[#fbf6ef]/92 backdrop-blur-xl dark:border-coffee-700/40 dark:bg-neutralwarm-900/88">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ $homeUrl }}" class="flex min-w-0 items-center gap-3 transition hover:opacity-95">
                    @if (data_get($blogSettingAssets ?? [], 'logo_url'))
                        <img src="{{ data_get($blogSettingAssets, 'logo_url') }}" alt="{{ $siteName }}" class="h-11 w-auto rounded-[1.2rem] object-contain shadow-[0_16px_30px_-24px_rgba(90,46,22,0.65)]">
                    @else
                        <div class="flex size-11 shrink-0 items-center justify-center rounded-[1.2rem] bg-[#8b4a22] text-sm font-extrabold text-white shadow-[0_16px_30px_-24px_rgba(90,46,22,0.65)]">
                            ND
                        </div>
                    @endif

                    <div class="min-w-0">
                        <p class="truncate font-lora text-[1.15rem] font-semibold leading-none text-[#2f1c12] dark:text-neutralwarm-50 sm:text-xl">
                            {{ $siteName }}
                        </p>
                        <p class="mt-1 truncate text-[0.72rem] leading-5 text-[#8a776b] dark:text-neutralwarm-100/70 sm:text-sm">
                            {{ $siteTagline }}
                        </p>
                    </div>
                </a>

                <nav class="hidden items-center gap-8 lg:flex">
                    <a href="{{ $homeUrl }}" class="border-b-2 {{ $isHome ? 'border-[#8b4a22] text-[#8b4a22] dark:border-coffee-100 dark:text-coffee-100' : 'border-transparent text-[#2f1c12] dark:text-neutralwarm-50' }} pb-2 text-sm font-semibold transition hover:text-[#8b4a22] dark:hover:text-coffee-100">
                        Beranda
                    </a>
                    <a href="{{ $homeSections['articles'] }}" class="border-b-2 border-transparent pb-2 text-sm font-semibold text-[#2f1c12] transition hover:border-[#8b4a22]/40 hover:text-[#8b4a22] dark:text-neutralwarm-50 dark:hover:border-coffee-100/40 dark:hover:text-coffee-100">
                        Artikel
                    </a>
                    <a href="{{ $homeSections['categories'] }}" class="border-b-2 border-transparent pb-2 text-sm font-semibold text-[#2f1c12] transition hover:border-[#8b4a22]/40 hover:text-[#8b4a22] dark:text-neutralwarm-50 dark:hover:border-coffee-100/40 dark:hover:text-coffee-100">
                        Kategori
                    </a>
                    <a href="{{ $homeSections['about'] }}" class="border-b-2 border-transparent pb-2 text-sm font-semibold text-[#2f1c12] transition hover:border-[#8b4a22]/40 hover:text-[#8b4a22] dark:text-neutralwarm-50 dark:hover:border-coffee-100/40 dark:hover:text-coffee-100">
                        Tentang
                    </a>
                </nav>

                <div class="flex items-center gap-2">
                    <a
                        href="{{ $searchHref }}"
                        data-focus-search="{{ $isHome ? 'true' : 'false' }}"
                        class="inline-flex size-11 items-center justify-center rounded-[1.15rem] border border-[#ead8c8] bg-white text-[#5a2e16] shadow-[0_16px_30px_-26px_rgba(90,46,22,0.45)] transition hover:-translate-y-0.5 hover:border-[#dcbca1] hover:bg-[#fff8f1] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5 lg:hidden"
                        aria-label="Cari artikel"
                    >
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M21 21L16.65 16.65M18 10.5C18 14.6421 14.6421 18 10.5 18C6.35786 18 3 14.6421 3 10.5C3 6.35786 6.35786 3 10.5 3C14.6421 3 18 6.35786 18 10.5Z" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>

                    <a
                        href="{{ $searchHref }}"
                        data-focus-search="{{ $isHome ? 'true' : 'false' }}"
                        class="hidden items-center justify-center rounded-[1.15rem] border border-[#ead8c8] bg-white px-6 py-3 text-sm font-semibold text-[#8b4a22] shadow-[0_16px_30px_-26px_rgba(90,46,22,0.45)] transition hover:-translate-y-0.5 hover:border-[#dcbca1] hover:bg-[#fff8f1] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5 lg:inline-flex"
                    >
                        Cari
                    </a>

                    <button
                        type="button"
                        data-theme-toggle
                        class="hidden size-11 items-center justify-center rounded-[1.15rem] border border-[#ead8c8] bg-white text-[#5a2e16] shadow-[0_16px_30px_-26px_rgba(90,46,22,0.45)] transition hover:-translate-y-0.5 hover:border-[#dcbca1] hover:bg-[#fff8f1] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5 lg:inline-flex"
                        aria-label="Ganti tema"
                    >
                        <svg class="size-5 dark:hidden" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3V5M12 19V21M4.22 4.22L5.64 5.64M18.36 18.36L19.78 19.78M3 12H5M19 12H21M4.22 19.78L5.64 18.36M18.36 5.64L19.78 4.22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                            <circle cx="12" cy="12" r="4.25" stroke="currentColor" stroke-width="1.8" />
                        </svg>
                        <svg class="hidden size-5 dark:block" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M21 14.5C19.6 15.7 17.8 16.5 15.8 16.5C11.3 16.5 7.5 12.7 7.5 8.2C7.5 6.2 8.2 4.4 9.4 3C5.2 3.5 2 7.1 2 11.4C2 16.1 5.9 20 10.6 20C14.9 20 18.5 16.8 21 14.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <button
                        type="button"
                        data-mobile-menu-toggle
                        class="inline-flex size-11 items-center justify-center rounded-[1.15rem] border border-[#ead8c8] bg-white text-[#5a2e16] shadow-[0_16px_30px_-26px_rgba(90,46,22,0.45)] transition hover:-translate-y-0.5 hover:border-[#dcbca1] hover:bg-[#fff8f1] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5 lg:hidden"
                        aria-label="Buka menu"
                        aria-expanded="false"
                    >
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 7H20M4 12H20M4 17H20" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
            </div>

            <div data-mobile-menu class="pointer-events-none fixed inset-0 z-50 hidden">
                <div data-mobile-menu-backdrop class="absolute inset-0 bg-[#2f1c12]/45 opacity-0 transition-opacity duration-300 dark:bg-black/60"></div>
                <div data-mobile-menu-panel class="absolute inset-x-4 top-4 rounded-[1.8rem] border border-[#ead8c8] bg-[#fffaf6] p-5 shadow-[0_32px_70px_-34px_rgba(47,28,18,0.45)] opacity-0 transition duration-300 translate-y-3 dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#8b4a22] dark:text-coffee-100">Ngopi Dulur</p>
                            <p class="mt-2 font-lora text-xl font-semibold text-[#2f1c12] dark:text-neutralwarm-50">{{ $siteName }}</p>
                            <p class="mt-1 text-sm leading-6 text-[#8a776b] dark:text-neutralwarm-100/70">{{ $siteTagline }}</p>
                        </div>
                        <button
                            type="button"
                            data-mobile-menu-close
                            class="inline-flex size-10 items-center justify-center rounded-[1rem] border border-[#ead8c8] bg-white text-[#5a2e16] transition hover:bg-[#fff3e8] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5"
                            aria-label="Tutup menu"
                        >
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>

                    <nav class="mt-6 space-y-2">
                        <a href="{{ $homeUrl }}" data-mobile-menu-link class="flex items-center justify-between rounded-[1.2rem] border border-[#ead8c8] bg-white px-4 py-3 text-sm font-semibold text-[#2f1c12] transition hover:border-[#dcbca1] hover:bg-[#fff7ef] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-neutralwarm-50 dark:hover:bg-white/5">
                            <span>Beranda</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                        <a href="{{ $homeSections['articles'] }}" data-mobile-menu-link class="flex items-center justify-between rounded-[1.2rem] border border-[#ead8c8] bg-white px-4 py-3 text-sm font-semibold text-[#2f1c12] transition hover:border-[#dcbca1] hover:bg-[#fff7ef] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-neutralwarm-50 dark:hover:bg-white/5">
                            <span>Artikel</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                        <a href="{{ $homeSections['categories'] }}" data-mobile-menu-link class="flex items-center justify-between rounded-[1.2rem] border border-[#ead8c8] bg-white px-4 py-3 text-sm font-semibold text-[#2f1c12] transition hover:border-[#dcbca1] hover:bg-[#fff7ef] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-neutralwarm-50 dark:hover:bg-white/5">
                            <span>Kategori</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                        <a href="{{ $homeSections['about'] }}" data-mobile-menu-link class="flex items-center justify-between rounded-[1.2rem] border border-[#ead8c8] bg-white px-4 py-3 text-sm font-semibold text-[#2f1c12] transition hover:border-[#dcbca1] hover:bg-[#fff7ef] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-neutralwarm-50 dark:hover:bg-white/5">
                            <span>Tentang</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </nav>

                    <div class="mt-6 flex items-center justify-between rounded-[1.25rem] border border-[#ead8c8] bg-white px-4 py-3 dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                        <div>
                            <p class="text-sm font-semibold text-[#2f1c12] dark:text-neutralwarm-50">Tema tampilan</p>
                            <p class="mt-1 text-xs text-[#8a776b] dark:text-neutralwarm-100/65">Ganti terang atau Dark Espresso.</p>
                        </div>
                        <button
                            type="button"
                            data-theme-toggle
                            class="inline-flex size-10 items-center justify-center rounded-full border border-[#ead8c8] bg-[#fbf6ef] text-[#5a2e16] transition hover:bg-[#f9eee2] dark:border-coffee-700/40 dark:bg-white/5 dark:text-coffee-100 dark:hover:bg-white/10"
                            aria-label="Ganti tema"
                        >
                            <svg class="size-[1.125rem] dark:hidden" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3V5M12 19V21M4.22 4.22L5.64 5.64M18.36 18.36L19.78 19.78M3 12H5M19 12H21M4.22 19.78L5.64 18.36M18.36 5.64L19.78 4.22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                <circle cx="12" cy="12" r="4.25" stroke="currentColor" stroke-width="1.8" />
                            </svg>
                            <svg class="hidden size-[1.125rem] dark:block" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M21 14.5C19.6 15.7 17.8 16.5 15.8 16.5C11.3 16.5 7.5 12.7 7.5 8.2C7.5 6.2 8.2 4.4 9.4 3C5.2 3.5 2 7.1 2 11.4C2 16.1 5.9 20 10.6 20C14.9 20 18.5 16.8 21 14.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>

        <footer id="site-footer" class="border-t border-[#ead8c8]/90 bg-[#fbf6ef] py-8 dark:border-coffee-700/40 dark:bg-neutralwarm-900/95">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <div class="max-w-xl">
                    <p class="text-base leading-7 text-[#8a776b] dark:text-neutralwarm-100/70">
                        {{ data_get($blogSettings ?? [], 'footer_note', 'Dibuat dengan Laravel, Vue, dan secangkir kopi yang pelan-pelan habis.') }}
                    </p>
                </div>

                <div class="flex flex-col gap-3 text-sm text-[#5a2e16] dark:text-coffee-100 sm:flex-row sm:flex-wrap sm:items-center sm:gap-5 lg:justify-end">
                    <a href="{{ $socialGithub }}" target="_blank" rel="noreferrer noopener" class="font-semibold transition hover:text-[#8b4a22] dark:hover:text-neutralwarm-50">
                        Github
                    </a>
                    <a href="{{ $socialInstagram }}" target="_blank" rel="noreferrer noopener" class="font-semibold transition hover:text-[#8b4a22] dark:hover:text-neutralwarm-50">
                        Instagram
                    </a>
                    <p class="text-[#8a776b] dark:text-neutralwarm-100/70">&copy; 2026 {{ $siteName }}</p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>

</html>
