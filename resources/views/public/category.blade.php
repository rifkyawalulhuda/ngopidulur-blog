@extends('layouts.public')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <div class="space-y-3 sm:space-y-4">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">Kategori</p>
            <h1 class="font-lora text-2xl font-semibold text-coffee-900 dark:text-neutralwarm-50 sm:text-3xl md:text-4xl">{{ $category->name }}</h1>
            @if ($category->description)
                <p class="max-w-3xl text-sm leading-7 text-neutralwarm-500 dark:text-neutralwarm-100/75 sm:text-base sm:leading-8">{{ $category->description }}</p>
            @endif
        </div>

        <div class="mt-6 grid grid-cols-1 gap-5 sm:mt-8 sm:grid-cols-2 sm:gap-6 xl:grid-cols-3">
            @forelse ($posts as $post)
                @include('public.partials.post-card', ['post' => $post])
            @empty
                <div class="sm:col-span-2 xl:col-span-3">
                    @include('public.partials.empty-state', [
                        'title' => 'Belum ada artikel di kategori ini',
                        'description' => 'Begitu ada artikel published, daftar bacaan akan muncul di sini.',
                    ])
                </div>
            @endforelse
        </div>

        <div class="mt-6 sm:mt-8">
            {{ $posts->links() }}
        </div>
    </section>
@endsection
