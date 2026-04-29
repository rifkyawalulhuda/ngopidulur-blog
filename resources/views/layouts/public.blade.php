<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Ngopi Dulur' }}</title>

    @vite(['resources/css/app.css'])

    @stack('head')
</head>

<body class="min-h-full bg-neutralwarm-50 font-sans text-neutralwarm-900 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
    <div class="flex min-h-screen flex-col">
        <header class="border-b border-coffee-100 bg-white/80 backdrop-blur dark:border-coffee-800/40 dark:bg-neutralwarm-900/80">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-2xl bg-coffee-700 text-sm font-bold text-white shadow-soft">
                        ND
                    </div>
                    <div>
                        <p class="font-lora text-lg font-semibold text-coffee-900 dark:text-neutralwarm-50">Ngopi Dulur</p>
                        <p class="text-xs text-neutralwarm-500 dark:text-neutralwarm-100/70">Warm Coffee Meets Modern Tech</p>
                    </div>
                </a>

                <a href="{{ route('login') }}" class="rounded-full border border-coffee-100 px-4 py-2 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">
                    Masuk Admin
                </a>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>
