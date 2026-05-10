<div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
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
