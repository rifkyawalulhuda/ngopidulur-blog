<article class="group overflow-hidden rounded-[1.6rem] border border-coffee-100 bg-white shadow-soft transition hover:-translate-y-0.5 hover:shadow-[0_22px_48px_-28px_rgba(59,36,20,0.48)] dark:border-coffee-700/40 dark:bg-neutralwarm-900">
    <a href="{{ route('posts.show', $post->slug) }}" class="block h-full">
        <div class="relative">
            @if ($post->featured_image_url)
                <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?? $post->title }}" class="h-52 w-full object-cover">
            @else
                <div class="flex h-52 items-center justify-center bg-[radial-gradient(circle_at_top,rgba(168,106,58,0.16),transparent_58%),linear-gradient(180deg,rgba(244,232,220,0.8),rgba(251,246,241,1))] text-coffee-700 dark:bg-[radial-gradient(circle_at_top,rgba(168,106,58,0.24),transparent_58%),linear-gradient(180deg,rgba(59,36,20,0.96),rgba(31,23,19,1))] dark:text-coffee-100">
                    <span class="font-lora text-xl">Ngopi Dulur</span>
                </div>
            @endif

            @if ($post->is_featured)
                <span class="absolute left-4 top-4 inline-flex rounded-full bg-coffee-700 px-3 py-1 text-xs font-semibold text-white shadow-soft">
                    Unggulan
                </span>
            @endif
        </div>

        <div class="space-y-3 p-5">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutralwarm-500 dark:text-neutralwarm-100/70">
                <span class="rounded-full bg-coffee-100 px-2.5 py-1 text-coffee-700 dark:bg-coffee-500/20 dark:text-coffee-100">
                    {{ $post->category?->name ?? 'Artikel' }}
                </span>
                <span>{{ $post->published_at?->translatedFormat('d M Y') }}</span>
            </div>

            <h3 class="font-lora text-xl font-semibold leading-tight text-coffee-900 transition group-hover:text-coffee-700 dark:text-neutralwarm-50 dark:group-hover:text-coffee-100">
                {{ $post->title }}
            </h3>

            <p class="line-clamp-3 text-sm leading-7 text-neutralwarm-500 dark:text-neutralwarm-100/75">
                {{ $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->rendered_content ?? ''), 150) }}
            </p>

            <div class="flex items-center justify-between gap-4 pt-2">
                <span class="truncate text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">
                    {{ $post->author?->name ?? 'Admin' }}
                </span>
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-coffee-700 dark:text-coffee-100">
                    Baca selengkapnya
                    <span aria-hidden="true">→</span>
                </span>
            </div>
        </div>
    </a>
</article>
