@extends('layouts.public')

@push('head')
    <meta name="description" content="Hasil pencarian artikel Ngopi Dulur">
    <meta name="robots" content="noindex,follow">
@endpush

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-5">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Pencarian</p>
            <h1 class="font-lora text-4xl font-semibold text-coffee-900 dark:text-neutralwarm-50">
                {{ $searchTerm !== '' ? 'Hasil pencarian untuk “'.$searchTerm.'”' : 'Cari artikel' }}
            </h1>

            <form action="{{ route('search') }}" method="GET" class="max-w-2xl">
                <label for="search-query" class="sr-only">Cari artikel</label>
                <div class="flex items-center gap-3 rounded-3xl border border-coffee-100 bg-white p-3 shadow-soft dark:border-coffee-700/40 dark:bg-neutralwarm-900">
                    <input
                        id="search-query"
                        name="q"
                        value="{{ $searchTerm }}"
                        type="search"
                        placeholder="Cari cerita, catatan, atau topik tertentu"
                        class="min-w-0 flex-1 border-0 bg-transparent px-2 py-2 text-base text-coffee-900 outline-none placeholder:text-neutralwarm-500/70 focus:ring-0 dark:text-neutralwarm-50"
                    >
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-coffee-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-coffee-800">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($posts as $post)
                @include('public.partials.post-card', ['post' => $post])
            @empty
                <div class="md:col-span-2 xl:col-span-3">
                    @include('public.partials.empty-state', [
                        'title' => $searchTerm !== '' ? 'Tidak ada hasil yang cocok' : 'Mulai dengan pencarian apa pun',
                        'description' => $searchTerm !== ''
                            ? 'Coba kata kunci lain untuk menemukan artikel yang relevan.'
                            : 'Masukkan kata kunci untuk menelusuri artikel published di Ngopi Dulur.',
                    ])
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </section>
@endsection
