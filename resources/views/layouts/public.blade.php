<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full {{ ($blogTheme ?? 'light') === 'dark' ? 'dark' : '' }}" data-theme="{{ $blogTheme ?? 'light' }}">

<head>
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

    <title>{{ $title ?? ($siteName ?? 'Ngopi Dulur') }}</title>

    @vite(['resources/css/app.css', 'resources/js/public.js'])

    @stack('head')
</head>

<body class="min-h-full bg-neutralwarm-50 font-sans text-neutralwarm-900 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
    <div class="flex min-h-screen flex-col">
        <header class="border-b border-coffee-100 bg-white/80 backdrop-blur dark:border-coffee-700/40 dark:bg-neutralwarm-900/80">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-2xl bg-coffee-700 text-sm font-bold text-white shadow-soft">
                        ND
                    </div>
                    <div class="min-w-0">
                        <p class="truncate font-lora text-lg font-semibold text-coffee-900 dark:text-neutralwarm-50">
                            {{ data_get($blogSettings ?? [], 'site_name', 'Ngopi Dulur') }}
                        </p>
                        <p class="truncate text-xs text-neutralwarm-500 dark:text-neutralwarm-100/70">
                            {{ data_get($blogSettings ?? [], 'site_tagline', 'Warm Coffee Meets Modern Tech') }}
                        </p>
                    </div>
                </a>

                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('search') }}" class="hidden rounded-full border border-coffee-100 px-4 py-2 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-700/50 dark:text-coffee-100 dark:hover:bg-white/5 sm:inline-flex">
                        Cari
                    </a>

                    <button
                        type="button"
                        data-theme-toggle
                        class="inline-flex size-11 items-center justify-center rounded-full border border-coffee-100 bg-white text-coffee-700 shadow-soft transition hover:bg-coffee-50 dark:border-coffee-700/50 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5"
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

                    <a href="{{ route('login') }}" class="rounded-full border border-coffee-100 px-4 py-2 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-700/50 dark:text-coffee-100 dark:hover:bg-white/5">
                        Masuk Admin
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="border-t border-coffee-100 bg-white/70 py-8 dark:border-coffee-700/40 dark:bg-neutralwarm-900/70">
            <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p>{{ data_get($blogSettings ?? [], 'footer_note', 'Dibuat dengan Laravel dan secangkir kopi yang pelan-pelan habis.') }}</p>
                <p>&copy; {{ now()->year }} {{ data_get($blogSettings ?? [], 'site_name', 'Ngopi Dulur') }}</p>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>

</html>
