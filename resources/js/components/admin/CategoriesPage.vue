<script setup>
defineProps({
    categories: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['open-create', 'open-edit', 'prompt-delete']);
</script>

<template>
    <section class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kategori untuk mengelompokkan tulisan.</p>
            </div>
            <button @click="emit('open-create')" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                Tambah Kategori
            </button>
        </div>

        <div v-if="categories.loading" class="grid gap-4">
            <div v-for="n in 3" :key="n" class="h-24 animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
        </div>

        <div v-else-if="categories.items.length === 0" class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 shadow-theme-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="mx-auto max-w-xl text-center">
                <p class="text-lg font-semibold text-gray-900 dark:text-white">Belum ada kategori</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tambahkan kategori pertama untuk mulai merapikan tulisan.</p>
                <button @click="emit('open-create')" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    Tambah Kategori
                </button>
            </div>
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Slug</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Post</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        <tr v-for="item in categories.items" :key="item.id" class="transition hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ item.name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.description || 'Tanpa deskripsi' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ item.slug }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]"
                                    :class="item.is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-100' : 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-gray-300'">
                                    {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ item.posts_count }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <button @click="emit('open-edit', item)" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">
                                        Ubah
                                    </button>
                                    <button @click="emit('prompt-delete', 'category', item)" class="rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10">
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
