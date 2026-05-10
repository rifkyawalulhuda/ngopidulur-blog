<script setup>
const props = defineProps({
    posts: {
        type: Object,
        required: true,
    },
    categories: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits([
    'open-create',
    'load-posts',
    'reset-filters',
    'preview-post',
    'open-edit',
    'prompt-action',
    'prompt-delete',
]);

const resetFilters = () => {
    emit('reset-filters');
    emit('load-posts');
};
</script>

<template>
    <section class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-4 border-b border-gray-100 pb-4 dark:border-gray-800 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar tulisan</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola artikel, filter cepat, dan aksi publikasi dari satu tempat.</p>
                </div>
                <button @click="emit('open-create')" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    Tulis Artikel Baru
                </button>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-12 lg:items-end">
                <label class="space-y-2 lg:col-span-4">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Cari judul</span>
                    <input
                        v-model="posts.filters.search"
                        @keyup.enter="emit('load-posts')"
                        type="search"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                        placeholder="Cari tulisan...">
                </label>

                <label class="space-y-2 lg:col-span-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Status</span>
                    <select v-model="posts.filters.status" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        <option value="">Semua</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </label>

                <label class="space-y-2 lg:col-span-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Kategori</span>
                    <select v-model="posts.filters.category" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        <option value="">Semua</option>
                        <option v-for="category in categories.items" :key="category.id" :value="category.slug">
                            {{ category.name }}
                        </option>
                    </select>
                </label>

                <label class="space-y-2 lg:col-span-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Urutkan</span>
                    <select v-model="posts.filters.sort" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        <option value="updated_at">Update terbaru</option>
                        <option value="published_at">Tanggal publish</option>
                    </select>
                </label>

                <div class="flex flex-wrap gap-2 lg:col-span-2 lg:justify-end">
                    <button @click="emit('load-posts')" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                        Terapkan
                    </button>
                    <button @click="resetFilters" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <div v-if="posts.loading" class="grid gap-4">
            <div v-for="n in 3" :key="n" class="h-28 animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
        </div>

        <div v-else-if="posts.items.length === 0" class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 shadow-theme-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="mx-auto max-w-xl text-center">
                <p class="text-lg font-semibold text-gray-900 dark:text-white">Belum ada artikel</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Mulai dengan draft pertama, lalu terbitkan saat kontennya siap.</p>
                <button @click="emit('open-create')" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    Tulis Artikel Baru
                </button>
            </div>
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Artikel terbaru</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ posts.meta.total }} artikel ditemukan</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Judul</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Slug</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Kategori</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal Publish</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Update Terakhir</th>
                            <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        <tr v-for="item in posts.items" :key="item.id" class="transition hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ item.title }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.author_name || 'Admin' }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ item.slug }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ item.category_name || '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                                    :class="item.status === 'published' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : item.status === 'draft' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' : 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300'">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ item.published_at || '-' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ item.updated_at || '-' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button @click="emit('preview-post', item)" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">
                                        Preview
                                    </button>
                                    <button @click="emit('open-edit', item)" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">
                                        Ubah
                                    </button>
                                    <button v-if="item.status !== 'published'" @click="emit('prompt-action', 'published', item)" class="rounded-lg border border-emerald-200 px-3 py-2 text-sm font-medium text-emerald-700 transition hover:bg-emerald-50 dark:border-emerald-500/30 dark:text-emerald-300 dark:hover:bg-emerald-500/10">
                                        Terbitkan
                                    </button>
                                    <button v-if="item.status !== 'archived'" @click="emit('prompt-action', 'archived', item)" class="rounded-lg border border-amber-200 px-3 py-2 text-sm font-medium text-amber-700 transition hover:bg-amber-50 dark:border-amber-500/30 dark:text-amber-300 dark:hover:bg-amber-500/10">
                                        Arsipkan
                                    </button>
                                    <button @click="emit('prompt-delete', 'post', item)" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-300 dark:hover:bg-red-500/10">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</template>
