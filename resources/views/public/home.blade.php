@extends('layouts.public')

@section('content')
    <section class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-6xl items-center px-4 py-16 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-6">
            <span class="inline-flex items-center rounded-full border border-coffee-100 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                Ngopi Dulur
            </span>

            <div class="space-y-4">
                <h1 class="font-lora text-5xl font-semibold leading-tight text-coffee-900 dark:text-neutralwarm-50">
                    Ruang menulis personal yang hangat, sederhana, dan siap tumbuh.
                </h1>
                <p class="max-w-2xl text-base leading-7 text-neutralwarm-500 dark:text-neutralwarm-100/75">
                    Ini adalah shell publik awal untuk blog Ngopi Dulur. Fondasinya dipersiapkan untuk tulisan yang rapi, cepat dibaca, dan enak dipelihara.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('login') }}" class="rounded-full bg-coffee-700 px-5 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800">
                    Masuk ke Admin
                </a>
                <span class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">
                    Blog publik akan menyusul pada milestone berikutnya.
                </span>
            </div>
        </div>
    </section>
@endsection
