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
                <p class="text-sm text-gray-500 dark:text-gray-400">Semua gambar unggulan dari tulisan terkumpul di sini agar mudah dicek ulang.</p>
            </div>
        </div>

        <div v-if="media.loading" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div v-for="n in 6" :key="n" class="h-72 animate-pulse rounded-3xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
        </div>

        <div v-else-if="media.items.length === 0" class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 shadow-theme-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="mx-auto max-w-xl text-center">
                <p class="text-lg font-semibold text-gray-900 dark:text-white">Belum ada media</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Gambar unggulan dari tulisan akan muncul di sini setelah artikel menyimpan featured image.</p>
            </div>
        </div>

        <div v-else class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <article v-for="item in media.items" :key="item.id" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="aspect-[16/10] overflow-hidden bg-gray-50 dark:bg-white/5">
                    <img :src="item.thumbnail_url" :alt="item.featured_image_alt || item.post_title" class="h-full w-full object-cover">
                </div>
                <div class="space-y-4 p-5">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-brand-500 dark:text-brand-300">{{ item.status }}</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ item.post_title }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.category_name || 'Tanpa kategori' }}</p>
                    </div>

                    <div class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                        <p><span class="font-medium text-gray-900 dark:text-white">Path:</span> {{ item.featured_image }}</p>
                        <p><span class="font-medium text-gray-900 dark:text-white">Update:</span> {{ item.updated_at || '-' }}</p>
                        <p><span class="font-medium text-gray-900 dark:text-white">Publish:</span> {{ item.published_at || '-' }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a :href="item.featured_image_url" target="_blank" rel="noreferrer noopener" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">
                            Buka URL
                        </a>
                        <button @click="emit('open-post-edit', { id: item.post_id })" class="rounded-full bg-brand-500 px-3 py-2 text-sm font-semibold text-white transition hover:bg-brand-600">
                            Buka tulisan
                        </button>
                    </div>
                </div>
            </article>
        </div>
    </section>
</template>
