<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

defineProps({
    tags: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['open-create', 'open-edit', 'prompt-delete']);

const openDropdownId = ref(null);
const dropdownStyle = ref({});

const toggleDropdown = (id, event) => {
    if (openDropdownId.value === id) {
        openDropdownId.value = null;
        return;
    }
    openDropdownId.value = id;

    const btn = event.currentTarget;
    const rect = btn.getBoundingClientRect();
    const dropdownHeight = 140;
    const viewportHeight = window.innerHeight;
    const spaceBelow = viewportHeight - rect.bottom;
    const rightOffset = window.innerWidth - rect.right;

    if (spaceBelow < dropdownHeight + 8 && rect.top > dropdownHeight + 8) {
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
</script>

<template>
    <section class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tag untuk memberi penanda ringan pada tulisan.</p>
            </div>
            <button @click="emit('open-create')" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                Tambah Tag
            </button>
        </div>

        <div v-if="tags.loading" class="grid gap-4">
            <div v-for="n in 3" :key="n" class="h-24 animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
        </div>

        <div v-else-if="tags.items.length === 0" class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 shadow-theme-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="mx-auto max-w-xl text-center">
                <p class="text-lg font-semibold text-gray-900 dark:text-white">Belum ada tag</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tambahkan tag pertama untuk memberi penanda pada artikel.</p>
                <button @click="emit('open-create')" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    Tambah Tag
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
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Post</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        <tr v-for="item in tags.items" :key="item.id" class="transition hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ item.name }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ item.slug }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ item.posts_count }}</td>
                            <td class="px-6 py-4">
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
                                                <button @click="emit('open-edit', item); closeDropdown()" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5" role="menuitem">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                    Ubah
                                                </button>
                                                <div class="my-1 border-t border-gray-200 dark:border-gray-700"></div>
                                                <button @click="emit('prompt-delete', 'tag', item); closeDropdown()" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-red-700 transition hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-500/10" role="menuitem">
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
        </div>
    </section>
</template>
