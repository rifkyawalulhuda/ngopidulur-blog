@extends('layouts.public')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <div class="space-y-4 sm:space-y-5">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Pencarian</p>
            <h1 data-search-title class="font-lora text-2xl font-semibold leading-tight text-coffee-900 dark:text-neutralwarm-50 sm:text-3xl md:text-4xl">
                {{ $searchTerm !== '' ? 'Hasil pencarian untuk "'.$searchTerm.'"' : 'Cari artikel' }}
            </h1>

            <form action="{{ route('search') }}" method="GET" class="max-w-2xl" data-live-search-form data-search-endpoint="{{ route('search') }}">
                <label for="search-query" class="sr-only">Cari artikel</label>
                <div class="flex items-center gap-2 rounded-2xl border border-coffee-100 bg-white p-2 shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900 sm:gap-3 sm:rounded-3xl sm:p-3">
                    <input
                        id="search-query"
                        name="q"
                        value="{{ $searchTerm }}"
                        type="search"
                        placeholder="Cari cerita, catatan, atau topik tertentu"
                        autocomplete="off"
                        class="min-w-0 flex-1 border-0 bg-transparent px-2 py-2 text-sm text-coffee-900 outline-none placeholder:text-neutralwarm-500/70 focus:ring-0 dark:text-neutralwarm-50 sm:text-base"
                    >
                    <button type="submit" data-live-search-submit class="inline-flex shrink-0 items-center justify-center rounded-xl bg-coffee-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-coffee-800 disabled:cursor-not-allowed disabled:opacity-70 sm:rounded-2xl sm:px-5 sm:py-3">
                        Cari
                    </button>
                </div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                    <p data-live-search-status class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">
                        {{ $searchTerm !== '' ? $posts->total().' artikel ditemukan' : 'Mulai ketik untuk melihat artikel terkait secara realtime.' }}
                    </p>
                    <p class="text-xs text-neutralwarm-500 dark:text-neutralwarm-100/60">
                        Hasil akan muncul otomatis saat kamu mengetik.
                    </p>
                </div>
            </form>
        </div>

        <div class="mt-8" data-live-search-results>
            @include('public.partials.search-results', ['posts' => $posts, 'searchTerm' => $searchTerm])
        </div>
    </section>
@endsection
