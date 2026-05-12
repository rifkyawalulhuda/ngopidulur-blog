<script setup>
defineProps({
    media: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['open-post-edit']);
</script>

<template>
    <section class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Koleksi gambar dari gambar unggulan dan konten tulisan, lengkap dengan tautan ke tulisan terkait.</p>
            </div>
        </div>

        <div v-if="media.loading" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div v-for="n in 8" :key="n" class="aspect-square animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
        </div>

        <div v-else-if="media.items.length === 0" class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 shadow-theme-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="mx-auto max-w-xl text-center">
                <p class="text-lg font-semibold text-gray-900 dark:text-white">Belum ada gambar</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Gambar dari featured image atau konten tulisan akan muncul di sini setelah tersimpan.</p>
            </div>
        </div>

        <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
            <article v-for="item in media.items" :key="item.id" class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-sm transition hover:-translate-y-0.5 hover:shadow-theme-md dark:border-gray-800 dark:bg-gray-900">
                <div class="relative aspect-square overflow-hidden bg-gray-50 dark:bg-white/5">
                    <img :src="item.image_url || item.thumbnail_url" :alt="item.image_alt || item.post_title" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                </div>
                <div class="p-4">
                    <button @click="emit('open-post-edit', { id: item.post_id })" class="line-clamp-2 text-left text-sm font-semibold leading-6 text-gray-900 transition hover:text-brand-600 dark:text-white dark:hover:text-brand-300">
                        {{ item.related_title || item.post_title }}
                    </button>
                </div>
            </article>
        </div>
    </section>
</template>
