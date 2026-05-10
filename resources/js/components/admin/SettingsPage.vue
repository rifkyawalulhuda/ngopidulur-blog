<script setup>
import { ref } from 'vue';

defineProps({
    settings: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['save-settings', 'update-asset', 'clear-asset']);

const logoInput = ref(null);
const faviconInput = ref(null);
const defaultOgImageInput = ref(null);

const clearAsset = (kind) => {
    if (kind === 'logo' && logoInput.value) {
        logoInput.value.value = '';
    }

    if (kind === 'favicon' && faviconInput.value) {
        faviconInput.value.value = '';
    }

    if (kind === 'default_og_image' && defaultOgImageInput.value) {
        defaultOgImageInput.value.value = '';
    }

    emit('clear-asset', kind);
};
</script>

<template>
    <section class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <p class="text-sm text-gray-500 dark:text-gray-400">Pengaturan ini langsung memengaruhi identitas public blog, SEO default, dan tema awal pembaca.</p>
            <button @click="emit('save-settings')" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600" :disabled="settings.loading || settings.saving">
                {{ settings.saving ? 'Menyimpan...' : 'Simpan Pengaturan' }}
            </button>
        </div>

        <div v-if="settings.loading" class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8 space-y-4">
                <div class="h-32 animate-pulse rounded-3xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                <div class="h-48 animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
            </div>
            <div class="xl:col-span-4 space-y-4">
                <div class="h-64 animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
            </div>
        </div>

        <div v-else class="grid gap-6 xl:grid-cols-12">
            <div class="space-y-6 xl:col-span-8">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Identitas situs</p>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <label class="space-y-2 md:col-span-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Nama situs</span>
                            <input v-model="settings.site_name" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                        <label class="space-y-2 md:col-span-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tagline</span>
                            <input v-model="settings.site_tagline" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                        <label class="space-y-2 md:col-span-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Deskripsi situs</span>
                            <textarea v-model="settings.site_description" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"></textarea>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tema default</span>
                            <select v-model="settings.default_theme" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="terang">Terang</option>
                                <option value="dark_espresso">Dark Espresso</option>
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Teks footer</span>
                            <input v-model="settings.footer_note" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Hero homepage</p>
                    <div class="mt-4 grid gap-4">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Badge</span>
                            <input v-model="settings.hero_badge" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Judul hero</span>
                            <input v-model="settings.hero_heading" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Subtitle hero</span>
                            <textarea v-model="settings.hero_subheading" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"></textarea>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Teks CTA</span>
                            <input v-model="settings.hero_cta_text" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">SEO default</p>
                    <div class="mt-4 grid gap-4">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">SEO title default</span>
                            <input v-model="settings.default_meta_title" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">SEO description default</span>
                            <textarea v-model="settings.default_meta_description" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"></textarea>
                        </label>
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Tautan sosial</p>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Instagram</span>
                            <input v-model="settings.social_links.instagram" type="url" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">X / Twitter</span>
                            <input v-model="settings.social_links.x" type="url" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">GitHub</span>
                            <input v-model="settings.social_links.github" type="url" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">LinkedIn</span>
                            <input v-model="settings.social_links.linkedin" type="url" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                        <label class="space-y-2 md:col-span-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">YouTube</span>
                            <input v-model="settings.social_links.youtube" type="url" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-6 xl:col-span-4">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Aset brand</p>
                    <div class="mt-4 space-y-5">
                        <div class="space-y-3">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Logo</p>
                            <div class="flex h-32 items-center justify-center overflow-hidden rounded-3xl border border-dashed border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-white/5">
                                <img v-if="settings.logo_url" :src="settings.logo_url" alt="Logo situs" class="h-full w-full object-contain p-4">
                                <p v-else class="px-4 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada logo</p>
                            </div>
                            <input ref="logoInput" @change="emit('update-asset', 'logo', $event)" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 file:mr-4 file:rounded-full file:border-0 file:bg-brand-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <button v-if="settings.logo_url" @click="clearAsset('logo')" class="rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10">
                                Hapus logo
                            </button>
                        </div>

                        <div class="space-y-3">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Favicon</p>
                            <div class="flex h-24 items-center justify-center overflow-hidden rounded-3xl border border-dashed border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-white/5">
                                <img v-if="settings.favicon_url" :src="settings.favicon_url" alt="Favicon situs" class="size-16 object-contain">
                                <p v-else class="px-4 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada favicon</p>
                            </div>
                            <input ref="faviconInput" @change="emit('update-asset', 'favicon', $event)" type="file" accept=".ico,image/jpeg,image/png,image/webp" class="block w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 file:mr-4 file:rounded-full file:border-0 file:bg-brand-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <button v-if="settings.favicon_url" @click="clearAsset('favicon')" class="rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10">
                                Hapus favicon
                            </button>
                        </div>

                        <div class="space-y-3">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Default Open Graph image</p>
                            <div class="flex h-40 items-center justify-center overflow-hidden rounded-3xl border border-dashed border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-white/5">
                                <img v-if="settings.default_og_image_url" :src="settings.default_og_image_url" alt="Default OG image" class="h-full w-full object-cover">
                                <p v-else class="px-4 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada gambar Open Graph</p>
                            </div>
                            <input ref="defaultOgImageInput" @change="emit('update-asset', 'default_og_image', $event)" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 file:mr-4 file:rounded-full file:border-0 file:bg-brand-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <button v-if="settings.default_og_image_url" @click="clearAsset('default_og_image')" class="rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10">
                                Hapus OG image
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
