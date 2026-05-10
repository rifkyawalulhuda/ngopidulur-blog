<script setup>
import { defineAsyncComponent, ref } from 'vue';

const TinyMceEditor = defineAsyncComponent(() => import('../tinymce-vue-editor'));

const props = defineProps({
    postEditor: {
        type: Object,
        required: true,
    },
    categories: {
        type: Object,
        required: true,
    },
    tags: {
        type: Object,
        required: true,
    },
    tinyMceTheme: {
        type: String,
        required: true,
    },
    tinyMceMountKey: {
        type: Number,
        required: true,
    },
    tinyMceInit: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits([
    'navigate-posts',
    'preview-post',
    'prompt-action',
    'set-content-format',
    'update-featured-image',
    'remove-featured-image',
    'prompt-delete',
]);

const postFeaturedImage = ref(null);
const postContent = ref(null);

const getFormattingPlaceholder = (type) => {
    if (type === 'link') {
        return 'tautan';
    }

    if (type === 'quote') {
        return 'kutipan';
    }

    if (type === 'heading') {
        return 'Judul';
    }

    if (type === 'list' || type === 'ordered') {
        return 'item';
    }

    return 'teks';
};

const applyFormatting = (type) => {
    const textarea = postContent.value;

    if (!textarea) {
        return;
    }

    const start = textarea.selectionStart ?? 0;
    const end = textarea.selectionEnd ?? 0;
    const selected = props.postEditor.content.slice(start, end) || getFormattingPlaceholder(type);

    const insert = (before, after = '') => {
        const value = `${before}${selected}${after}`;
        props.postEditor.content = `${props.postEditor.content.slice(0, start)}${value}${props.postEditor.content.slice(end)}`;

        requestAnimationFrame(() => {
            textarea.focus();
            const cursor = start + before.length;
            textarea.setSelectionRange(cursor, cursor + selected.length);
        });
    };

    if (type === 'bold') {
        insert('**', '**');
        return;
    }

    if (type === 'italic') {
        insert('*', '*');
        return;
    }

    if (type === 'heading') {
        insert('## ');
        return;
    }

    if (type === 'quote') {
        insert('> ');
        return;
    }

    if (type === 'list') {
        insert('- ');
        return;
    }

    if (type === 'ordered') {
        insert('1. ');
        return;
    }

    if (type === 'link') {
        insert('[', '](https://)');
    }
};

const removeFeaturedImage = () => {
    if (postFeaturedImage.value) {
        postFeaturedImage.value.value = '';
    }

    emit('remove-featured-image');
};
</script>

<template>
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <button @click="emit('navigate-posts')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">
                    Kembali
                </button>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ postEditor.mode === 'create' ? 'Mode membuat draft baru' : 'Mode mengedit artikel' }}
                </span>
            </div>

            <div class="flex flex-wrap gap-2">
                <button @click="emit('preview-post')" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white" :disabled="postEditor.loading || postEditor.saving">
                    Preview
                </button>
                <button @click="emit('prompt-action', 'draft')" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white" :disabled="postEditor.loading || postEditor.saving">
                    {{ postEditor.saving ? 'Menyimpan...' : 'Simpan Draft' }}
                </button>
                <button @click="emit('prompt-action', 'published')" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600" :disabled="postEditor.loading || postEditor.saving">
                    {{ postEditor.saving ? 'Menyimpan...' : 'Terbitkan' }}
                </button>
            </div>
        </div>

        <div v-if="postEditor.loading" class="grid gap-4 xl:grid-cols-12">
            <div class="xl:col-span-8 space-y-4">
                <div class="h-16 animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                <div class="h-16 animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                <div class="h-64 animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
            </div>
            <div class="xl:col-span-4 space-y-4">
                <div class="h-48 animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                <div class="h-64 animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
            </div>
        </div>

        <div v-else class="grid gap-6 xl:grid-cols-12">
            <div class="space-y-6 xl:col-span-8">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="grid gap-4">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Judul</span>
                            <input v-model="postEditor.title" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" placeholder="Contoh: Secangkir Pagi">
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Slug</span>
                            <input v-model="postEditor.slug" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" placeholder="otomatis-dari-judul">
                            <p class="text-xs text-gray-500 dark:text-gray-500">Kosongkan untuk slug otomatis. Jika diisi manual, slug harus unik.</p>
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Ringkasan</span>
                            <textarea v-model="postEditor.excerpt" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" placeholder="Opsional, untuk kartu artikel dan SEO"></textarea>
                        </label>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Editor</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mode Visual menyimpan HTML, Mode Markdown menyimpan markdown asli.</p>
                        </div>
                        <div class="inline-flex rounded-full border border-gray-200 bg-gray-50 p-1 dark:border-gray-700 dark:bg-white/5">
                            <button @click="emit('set-content-format', 'richtext')" class="rounded-full px-4 py-2 text-sm font-semibold transition" :class="postEditor.content_format === 'richtext' ? 'bg-white text-brand-500 shadow-theme-sm dark:bg-gray-900 dark:text-white' : 'text-brand-500 dark:text-brand-300'">
                                Mode Visual
                            </button>
                            <button @click="emit('set-content-format', 'markdown')" class="rounded-full px-4 py-2 text-sm font-semibold transition" :class="postEditor.content_format === 'markdown' ? 'bg-white text-brand-500 shadow-theme-sm dark:bg-gray-900 dark:text-white' : 'text-brand-500 dark:text-brand-300'">
                                Mode Markdown
                            </button>
                        </div>
                    </div>

                    <div v-if="postEditor.content_format === 'markdown'" class="mt-4 flex flex-wrap gap-2">
                        <button @click="applyFormatting('bold')" type="button" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">B</button>
                        <button @click="applyFormatting('italic')" type="button" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">I</button>
                        <button @click="applyFormatting('heading')" type="button" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">H1</button>
                        <button @click="applyFormatting('quote')" type="button" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">Quote</button>
                        <button @click="applyFormatting('list')" type="button" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">UL</button>
                        <button @click="applyFormatting('ordered')" type="button" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">OL</button>
                        <button @click="applyFormatting('link')" type="button" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">Link</button>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div v-if="postEditor.content_format === 'richtext'" class="ngopi-tinymce overflow-hidden rounded-3xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                            <tiny-mce-editor
                                :key="'tinymce-' + tinyMceTheme + '-' + tinyMceMountKey"
                                v-model="postEditor.content"
                                license-key="gpl"
                                output-format="html"
                                tinymce-script-src="/vendor/tinymce/tinymce.min.js"
                                :init="tinyMceInit" />
                        </div>
                        <textarea
                            v-else
                            ref="postContent"
                            v-model="postEditor.content"
                            rows="16"
                            class="w-full rounded-3xl border border-gray-200 bg-white px-4 py-3 text-sm leading-7 text-gray-900 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            placeholder="# Judul artikel"></textarea>

                        <p class="text-xs text-gray-500 dark:text-gray-500">
                            {{ postEditor.content_format === 'markdown' ? 'Markdown akan dirender dan disanitasi di server.' : 'Konten visual disimpan sebagai HTML yang sudah disanitasi di server.' }}
                        </p>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Preview aman</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Preview ditarik dari server dan memakai HTML yang sudah disanitasi.</p>
                        </div>
                        <button @click="emit('preview-post')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">
                            Buka preview
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-6 xl:col-span-4">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Meta & Status</p>

                    <div class="mt-4 space-y-4">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Kategori</span>
                            <select v-model="postEditor.category_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">Pilih kategori</option>
                                <option v-for="category in categories.items" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </label>

                        <div class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tag</span>
                            <div class="grid grid-cols-1 gap-2 rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                                <label v-for="tag in tags.items" :key="tag.id" class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                                    <input v-model="postEditor.tag_ids" :value="tag.id" type="checkbox" class="size-4 rounded border-gray-300 text-brand-500 focus:ring-brand-300">
                                    {{ tag.name }}
                                </label>
                                <p v-if="tags.items.length === 0" class="text-sm text-gray-500 dark:text-gray-400">Belum ada tag.</p>
                            </div>
                        </div>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Status</span>
                            <select v-model="postEditor.status" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </label>

                        <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <input v-model="postEditor.is_featured" type="checkbox" class="size-4 rounded border-gray-300 text-brand-500 focus:ring-brand-300">
                            Jadikan featured
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Meta title</span>
                            <input v-model="postEditor.meta_title" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" placeholder="Opsional">
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Meta description</span>
                            <textarea v-model="postEditor.meta_description" rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" placeholder="Opsional"></textarea>
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Published at</span>
                            <input v-model="postEditor.published_at" type="datetime-local" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Featured image</p>

                    <div class="mt-4 space-y-4">
                        <div class="overflow-hidden rounded-3xl border border-dashed border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-white/5">
                            <img v-if="postEditor.featured_image_url" :src="postEditor.featured_image_url" class="h-56 w-full object-cover" :alt="postEditor.featured_image_alt || postEditor.title || 'Featured image'">
                            <div v-else class="flex h-56 items-center justify-center px-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada featured image
                            </div>
                        </div>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Upload gambar</span>
                            <input ref="postFeaturedImage" @change="emit('update-featured-image', $event)" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 file:mr-4 file:rounded-full file:border-0 file:bg-brand-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Alt text</span>
                            <input v-model="postEditor.featured_image_alt" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" placeholder="Opsional">
                        </label>

                        <div class="flex flex-wrap gap-2">
                            <button @click="removeFeaturedImage" type="button" class="rounded-full border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10">
                                Hapus gambar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Aksi cepat</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <button @click="emit('prompt-action', 'draft')" class="rounded-full border border-gray-200 px-4 py-2.5 text-sm font-semibold text-brand-500 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700 dark:text-brand-300 dark:hover:bg-white/5" :disabled="postEditor.loading || postEditor.saving">
                            {{ postEditor.saving ? 'Memproses...' : 'Simpan Draft' }}
                        </button>
                        <button @click="emit('prompt-action', 'published')" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-60" :disabled="postEditor.loading || postEditor.saving">
                            {{ postEditor.saving ? 'Memproses...' : 'Terbitkan' }}
                        </button>
                        <button v-if="postEditor.id && postEditor.status !== 'archived'" @click="emit('prompt-action', 'archived', { id: postEditor.id, title: postEditor.title })" class="rounded-full border border-amber-200 px-4 py-2.5 text-sm font-semibold text-amber-700 transition hover:bg-amber-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-amber-500/30 dark:text-amber-100 dark:hover:bg-amber-500/10" :disabled="postEditor.loading || postEditor.saving">
                            {{ postEditor.saving ? 'Memproses...' : 'Arsipkan' }}
                        </button>
                        <button v-if="postEditor.id" @click="emit('prompt-delete', 'post', { id: postEditor.id, slug: postEditor.slug || postEditor.id })" class="rounded-full border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10" :disabled="postEditor.loading || postEditor.saving">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
