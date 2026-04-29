@extends('layouts.public')

@push('head')
    <meta name="description" content="{{ $metaDescription ?? $post->excerpt ?? $post->title }}">
    <meta property="og:title" content="{{ $title ?? $post->title }}">
    <meta property="og:description" content="{{ $metaDescription ?? $post->excerpt ?? $post->title }}">
    @if ($post->featured_image_url)
        <meta property="og:image" content="{{ $post->featured_image_url }}">
    @endif
@endpush

@section('content')
    <article class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="space-y-4">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">
                {{ $post->category?->name ?? 'Ngopi Dulur' }}
            </p>
            <h1 class="font-lora text-4xl font-semibold leading-tight text-coffee-900 dark:text-neutralwarm-50">
                {{ $post->title }}
            </h1>
            <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">
                Ditulis oleh {{ $post->author?->name ?? 'Admin' }} · {{ $post->published_at?->translatedFormat('d F Y') }}
                @if ($post->reading_time_minutes)
                    · {{ $post->reading_time_minutes }} menit baca
                @endif
            </p>
        </div>

        @if ($post->featured_image_url)
            <div class="mt-8 overflow-hidden rounded-[2rem] border border-coffee-100 bg-white shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?? $post->title }}" class="h-auto w-full object-cover">
            </div>
        @endif

        <div class="prose prose-neutral mt-10 max-w-none dark:prose-invert">
            {!! $post->rendered_content !!}
        </div>
    </article>
@endsection
