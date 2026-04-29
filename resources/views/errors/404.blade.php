@extends('layouts.public')

@push('head')
    <meta name="robots" content="noindex,nofollow">
@endpush

@section('content')
    <section class="mx-auto flex min-h-[calc(100vh-13rem)] max-w-4xl items-center px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
            <div class="space-y-4">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-coffee-700 dark:text-coffee-100">Halaman tidak ditemukan</p>
                <h1 class="font-lora text-5xl font-semibold leading-tight text-coffee-900 dark:text-neutralwarm-50">
                    404
                </h1>
                <p class="text-base leading-8 text-neutralwarm-500 dark:text-neutralwarm-100/75">
                    Sepertinya halaman yang dicari tidak ada, sudah diarsipkan, atau memang tidak untuk diakses publik.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-full bg-coffee-700 px-5 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800">
                        Kembali ke Beranda
                    </a>
                    <a href="{{ route('search') }}" class="inline-flex items-center justify-center rounded-full border border-coffee-100 px-5 py-3 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">
                        Cari Artikel
                    </a>
                </div>
            </div>

            <div class="rounded-[2rem] border border-coffee-100 bg-white p-8 shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                <div class="space-y-4">
                    <div class="inline-flex rounded-2xl bg-coffee-100 px-4 py-2 text-sm font-semibold text-coffee-700 dark:bg-coffee-500/20 dark:text-coffee-100">
                        Ngopi dulu, ya.
                    </div>
                    <p class="text-sm leading-7 text-neutralwarm-500 dark:text-neutralwarm-100/70">
                        Kalau yang dicari adalah artikel published, kamu bisa kembali ke halaman depan atau menelusuri kategori dan tag.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
