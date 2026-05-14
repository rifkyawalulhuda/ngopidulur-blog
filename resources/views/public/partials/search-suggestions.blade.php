@php
    $hasQuery = $searchTerm !== '';
@endphp

@if (! $hasQuery)
    <div class="rounded-2xl border border-[#efddcc] bg-white p-4 text-sm text-[#7c695d] shadow-[0_24px_55px_-30px_rgba(90,46,22,0.45)] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-neutralwarm-100/70">
        Mulai ketik judul atau topik yang ingin kamu cari.
    </div>
@elseif ($posts->isEmpty())
    <div class="rounded-2xl border border-[#efddcc] bg-white p-4 text-sm text-[#7c695d] shadow-[0_24px_55px_-30px_rgba(90,46,22,0.45)] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-neutralwarm-100/70">
        Belum ada artikel yang cocok untuk "<span class="font-semibold text-[#2f1c12] dark:text-neutralwarm-50">{{ $searchTerm }}</span>".
    </div>
@else
    <div class="overflow-hidden rounded-2xl border border-[#efddcc] bg-white shadow-[0_28px_60px_-30px_rgba(90,46,22,0.55)] dark:border-coffee-700/40 dark:bg-neutralwarm-900">
        <div class="border-b border-[#f1e3d6] px-4 py-3 dark:border-white/8">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#8b4a22] dark:text-coffee-100/90">
                Hasil cepat
            </p>
            <p class="mt-1 text-sm text-[#7c695d] dark:text-neutralwarm-100/70">
                {{ $total }} artikel relevan untuk "<span class="font-semibold text-[#2f1c12] dark:text-neutralwarm-50">{{ $searchTerm }}</span>"
            </p>
        </div>

        <div class="max-h-[420px] divide-y divide-[#f3e6da] overflow-y-auto dark:divide-white/8">
            @foreach ($posts as $post)
                <a href="{{ route('posts.show', $post->slug) }}" class="group flex items-start gap-3 px-4 py-3 transition hover:bg-[#fcf5ee] dark:hover:bg-white/5">
                    <span class="mt-1 inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#fbf2e9] text-[#8b4a22] dark:bg-white/6 dark:text-coffee-100">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M8 6.75H16M8 10.75H16M8 14.75H12.5M6.25 4.75H17.75C18.8546 4.75 19.75 5.64543 19.75 6.75V17.25C19.75 18.3546 18.8546 19.25 17.75 19.25H6.25C5.14543 19.25 4.25 18.3546 4.25 17.25V6.75C4.25 5.64543 5.14543 4.75 6.25 4.75Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-[#9b8476] dark:text-neutralwarm-100/55">
                            <span class="rounded-full bg-[#f7ebdf] px-2.5 py-0.5 text-[#8b4a22] dark:bg-coffee-500/18 dark:text-coffee-100">
                                {{ $post->category?->name ?? 'Artikel' }}
                            </span>
                            <span>{{ $post->published_at?->translatedFormat('d M Y') }}</span>
                        </div>

                        <p class="mt-1.5 line-clamp-2 font-lora text-base font-semibold leading-snug text-[#2f1c12] transition group-hover:text-[#8b4a22] dark:text-neutralwarm-50 dark:group-hover:text-coffee-100">
                            {{ $post->title }}
                        </p>

                        <p class="mt-1 line-clamp-2 text-sm leading-6 text-[#7c695d] dark:text-neutralwarm-100/70">
                            {{ $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->rendered_content ?? ''), 96) }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="border-t border-[#f1e3d6] bg-[#fdf8f1] px-4 py-3 dark:border-white/8 dark:bg-white/5">
            <a href="{{ route('search', ['q' => $searchTerm]) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#8b4a22] transition hover:text-[#6f3818] dark:text-coffee-100 dark:hover:text-coffee-50">
                Lihat semua hasil pencarian
                <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
@endif
