<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

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
    'change-sort',
    'change-page',
    'change-per-page',
]);

const openDropdownId = ref(null);
const dropdownStyle = ref({});

const toggleDropdown = (id, event) => {
    if (openDropdownId.value === id) {
        openDropdownId.value = null;
        return;
    }
    openDropdownId.value = id;

    // Hitung posisi dropdown tepat di bawah tombol yang diklik
    const btn = event.currentTarget;
    const rect = btn.getBoundingClientRect();
    const dropdownHeight = 220;
    const viewportHeight = window.innerHeight;
    const spaceBelow = viewportHeight - rect.bottom;
    const rightOffset = window.innerWidth - rect.right;

    // Default: muncul di bawah tombol, align kanan
    if (spaceBelow < dropdownHeight + 8 && rect.top > dropdownHeight + 8) {
        // Flip ke atas hanya jika benar-benar tidak cukup ruang di bawah
        dropdownStyle.value = {
            position: 'fixed',
            bottom: (viewportHeight - rect.top + 4) + 'px',
            right: rightOffset + 'px',
            top: 'auto',
        };
    } else {
        dropdownStyle.value = {
            position: 'fixed',
            top: (rect.bottom + 4) + 'px',
            right: rightOffset + 'px',
            bottom: 'auto',
        };
    }
};

const closeDropdown = () => {
    openDropdownId.value = null;
};

const handleClickOutside = (event) => {
    if (openDropdownId.value !== null && !event.target.closest('[data-dropdown-actions]')) {
        closeDropdown();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

const resetFilters = () => {
    emit('reset-filters');
    emit('load-posts');
};

const handleSort = (column) => {
    if (props.posts.filters.sort_column === column) {
        const newDirection = props.posts.filters.sort_direction === 'asc' ? 'desc' : 'asc';
        emit('change-sort', { sort_column: column, sort_direction: newDirection });
    } else {
        emit('change-sort', { sort_column: column, sort_direction: 'desc' });
    }
    emit('load-posts');
};

const getSortIcon = (column) => {
    if (props.posts.filters.sort_column !== column) {
        return 'sort-default';
    }
    return props.posts.filters.sort_direction === 'asc' ? 'sort-asc' : 'sort-desc';
};

// Pagination
const perPageOptions = [15, 30, 50, 100];

const paginationPages = computed(() => {
    const current = props.posts.meta.current_page;
    const last = props.posts.meta.last_page;
    const pages = [];

    if (last <= 7) {
        for (let i = 1; i <= last; i++) pages.push(i);
        return pages;
    }

    // Always show first page
    pages.push(1);

    if (current > 3) pages.push('...');

    // Pages around current
    const start = Math.max(2, current - 1);
    const end = Math.min(last - 1, current + 1);
    for (let i = start; i <= end; i++) pages.push(i);

    if (current < last - 2) pages.push('...');

    // Always show last page
    pages.push(last);

    return pages;
});

const paginationInfo = computed(() => {
    const { current_page, per_page, total } = props.posts.meta;
    const from = total === 0 ? 0 : (current_page - 1) * per_page + 1;
    const to = Math.min(current_page * per_page, total);
    return { from, to, total };
});
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
                        <option value="">Default</option>
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
            <div v-for="n in 5" :key="n" class="h-16 animate-pulse rounded-2xl bg-gray-50 dark:bg-white/5"></div>
        </div>

        <div v-else-if="posts.items.length === 0" class="rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-white/5">
                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Belum ada tulisan</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulailah dengan menulis artikel baru.</p>
            <button @click="emit('open-create')" class="mt-4 inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                Tulis Artikel Baru
            </button>
        </div>

        <div v-else class="rounded-2xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <button @click="handleSort('title')" class="flex items-center gap-1 hover:text-brand-600 dark:hover:text-brand-400">
                                    Judul
                                    <svg v-if="getSortIcon('title') === 'sort-default'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                    <svg v-else-if="getSortIcon('title') === 'sort-asc'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Slug</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Kategori</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <button @click="handleSort('published_at')" class="flex items-center gap-1 hover:text-brand-600 dark:hover:text-brand-400">
                                    Tanggal Publish
                                    <svg v-if="getSortIcon('published_at') === 'sort-default'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                    <svg v-else-if="getSortIcon('published_at') === 'sort-asc'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <button @click="handleSort('updated_at')" class="flex items-center gap-1 hover:text-brand-600 dark:hover:text-brand-400">
                                    Update Terakhir
                                    <svg v-if="getSortIcon('updated_at') === 'sort-default'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                    <svg v-else-if="getSortIcon('updated_at') === 'sort-asc'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </th>
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
                                <div class="relative flex justify-end" data-dropdown-actions>
                                    <button @click="toggleDropdown(item.id, $event)" class="inline-flex items-center justify-center rounded-lg border border-gray-300 p-2 text-gray-500 transition hover:bg-gray-50 hover:text-gray-700 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="12" cy="5" r="2" />
                                            <circle cx="12" cy="12" r="2" />
                                            <circle cx="12" cy="19" r="2" />
                                        </svg>
                                    </button>
                                    <Teleport to="body">
                                        <div v-if="openDropdownId === item.id" :style="dropdownStyle" class="z-[9999] w-44 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900" data-dropdown-actions>
                                            <div class="space-y-1" role="menu">
                                                <button @click="emit('preview-post', item); closeDropdown()" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5" role="menuitem">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                    Preview
                                                </button>
                                                <button @click="emit('open-edit', item); closeDropdown()" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5" role="menuitem">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                    Ubah
                                                </button>
                                                <button v-if="item.status !== 'published'" @click="emit('prompt-action', 'published', item); closeDropdown()" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-emerald-700 transition hover:bg-emerald-50 dark:text-emerald-300 dark:hover:bg-emerald-500/10" role="menuitem">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    Terbitkan
                                                </button>
                                                <button v-if="item.status !== 'archived'" @click="emit('prompt-action', 'archived', item); closeDropdown()" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-amber-700 transition hover:bg-amber-50 dark:text-amber-300 dark:hover:bg-amber-500/10" role="menuitem">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                                    Arsipkan
                                                </button>
                                                <div class="my-1 border-t border-gray-200 dark:border-gray-700"></div>
                                                <button @click="emit('prompt-delete', 'post', item); closeDropdown()" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-red-700 transition hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-500/10" role="menuitem">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </Teleport>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col gap-4 border-t border-gray-200 px-5 py-4 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Tampilkan</span>
                    <select
                        :value="posts.meta.per_page"
                        @change="emit('change-per-page', Number($event.target.value))"
                        class="rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                    >
                        <option v-for="opt in perPageOptions" :key="opt" :value="opt">{{ opt }}</option>
                    </select>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        dari <span class="font-medium text-gray-900 dark:text-white">{{ paginationInfo.total }}</span> tulisan
                    </span>
                </div>

                <nav v-if="posts.meta.last_page > 1" class="flex items-center gap-1" aria-label="Pagination">
                    <!-- Previous -->
                    <button
                        :disabled="posts.meta.current_page <= 1"
                        @click="emit('change-page', posts.meta.current_page - 1)"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-300 text-gray-500 transition hover:bg-gray-50 hover:text-gray-700 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white"
                        aria-label="Halaman sebelumnya"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </button>

                    <!-- Page numbers -->
                    <template v-for="(page, idx) in paginationPages" :key="idx">
                        <span v-if="page === '...'" class="inline-flex h-9 w-9 items-center justify-center text-sm text-gray-400">…</span>
                        <button
                            v-else
                            @click="emit('change-page', page)"
                            :class="page === posts.meta.current_page
                                ? 'bg-brand-500 text-white border-brand-500 shadow-theme-xs'
                                : 'border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white'"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border text-sm font-medium transition"
                        >
                            {{ page }}
                        </button>
                    </template>

                    <!-- Next -->
                    <button
                        :disabled="posts.meta.current_page >= posts.meta.last_page"
                        @click="emit('change-page', posts.meta.current_page + 1)"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-300 text-gray-500 transition hover:bg-gray-50 hover:text-gray-700 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white"
                        aria-label="Halaman berikutnya"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                </nav>
            </div>
        </div>
    </section>
</template>
