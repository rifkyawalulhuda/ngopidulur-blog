@extends('layouts.public')

@push('head')
    <meta name="description" content="Kumpulan artikel dengan tag {{ $tag->name }}">
@endpush

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-4">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Tag</p>
            <h1 class="font-lora text-4xl font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ $tag->name }}</h1>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($posts as $post)
                @include('public.partials.post-card', ['post' => $post])
            @empty
                <div class="md:col-span-2 xl:col-span-3">
                    @include('public.partials.empty-state', [
                        'title' => 'Belum ada artikel dengan tag ini',
                        'description' => 'Begitu ada artikel published, daftar bacaan akan muncul di sini.',
                    ])
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </section>
@endsection
