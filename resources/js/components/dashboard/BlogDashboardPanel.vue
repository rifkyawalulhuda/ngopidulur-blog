<script setup>
import { computed, defineAsyncComponent, onMounted, ref } from 'vue';

const TrafikChart = defineAsyncComponent(() => import('./TrafikChart.vue'));
const KategoriChart = defineAsyncComponent(() => import('./KategoriChart.vue'));

const props = defineProps({
    endpoint: {
        type: String,
        required: true,
    },
});

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const loading = ref(true);
const error = ref('');
const previewLoading = ref(false);
const previewHtml = ref('');
const previewTitle = ref('');
const previewOpen = ref(false);
const state = ref({
    stats: {},
    monthly_target: {
        target: 8,
        completed: 0,
        remaining: 8,
        progress_percentage: 0,
        message: '',
    },
    charts: {
        traffic: {
            labels: [],
            series: [],
        },
        categories: {
            labels: [],
            series: [],
        },
    },
    recent_posts: [],
    top_posts: [],
    activities: [],
});

const statCards = computed(() => [
    state.value.stats.total_posts,
    state.value.stats.total_views,
    state.value.stats.monthly_visitors,
    state.value.stats.draft_posts,
].filter(Boolean));

const fetchDashboard = async () => {
    loading.value = true;
    error.value = '';

    try {
        const response = await fetch(props.endpoint, {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        const payload = await response.json();

        if (! response.ok) {
            throw new Error(payload.message || 'Data dashboard belum bisa dimuat.');
        }

        state.value = payload;
    } catch (err) {
        error.value = err.message || 'Terjadi kesalahan saat memuat dashboard.';
    } finally {
        loading.value = false;
    }
};

const openPreview = async (post) => {
    if (! post?.preview_api_url) {
        return;
    }

    previewOpen.value = true;
    previewLoading.value = true;
    previewTitle.value = post.title;
    previewHtml.value = '';

    try {
        const response = await fetch(post.preview_api_url, {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
        });
        const payload = await response.json();

        if (! response.ok) {
            throw new Error(payload.message || 'Preview tulisan belum bisa dimuat.');
        }

        previewHtml.value = payload.preview_html || payload.item?.rendered_content || '';
    } catch (err) {
        previewHtml.value = `<p class="text-red-600">${err.message || 'Preview gagal dimuat.'}</p>`;
    } finally {
        previewLoading.value = false;
    }
};

const closePreview = () => {
    previewOpen.value = false;
    previewLoading.value = false;
    previewTitle.value = '';
    previewHtml.value = '';
};

const formatNumber = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));

const formatDate = (value) => {
    if (! value) {
        return '-';
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(new Date(value));
};

const growthTone = (growth) => {
    if (! growth || growth.direction === 'neutral') {
        return 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300';
    }

    return growth.direction === 'up'
        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'
        : 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300';
};

const statusTone = (status) => {
    if (status === 'published') {
        return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300';
    }

    if (status === 'draft') {
        return 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300';
    }

    return 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300';
};

onMounted(fetchDashboard);
</script>

<template>
    <div class="space-y-6">
        <div v-if="error" class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200">
            {{ error }}
        </div>

        <template v-if="loading">
            <div class="grid grid-cols-12 gap-4 md:gap-6">
                <div v-for="n in 4" :key="`stat-${n}`" class="col-span-12 sm:col-span-6 xl:col-span-3">
                    <div class="h-[146px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                </div>
                <div class="col-span-12 xl:col-span-4">
                    <div class="h-[220px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                </div>
                <div class="col-span-12 xl:col-span-8">
                    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                        <div class="h-[340px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                        <div class="h-[340px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                    </div>
                </div>
                <div class="col-span-12 xl:col-span-8">
                    <div class="h-[420px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                </div>
                <div class="col-span-12 xl:col-span-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="h-[220px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                        <div class="h-[220px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>
            <div class="grid grid-cols-12 gap-4 md:gap-6">
                <div v-for="card in statCards" :key="card.label" class="col-span-12 sm:col-span-6 xl:col-span-3">
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ card.label }}</p>
                                <h3 class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ formatNumber(card.value) }}</h3>
                            </div>
                            <span v-if="card.growth" class="inline-flex rounded-full px-3 py-1 text-xs font-medium" :class="growthTone(card.growth)">
                                {{ card.growth.label }}
                            </span>
                        </div>
                        <p class="mt-5 text-sm text-gray-500 dark:text-gray-400">{{ card.caption }}</p>
                    </div>
                </div>

                <div class="col-span-12 xl:col-span-4">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Target Konten Bulanan</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jaga ritme publish tetap stabil sepanjang bulan.</p>
                            </div>
                            <span class="inline-flex rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
                                {{ state.monthly_target.completed }}/{{ state.monthly_target.target }}
                            </span>
                        </div>

                        <div class="mt-6">
                            <div class="mb-2 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                <span>Progress publish</span>
                                <span>{{ state.monthly_target.progress_percentage }}%</span>
                            </div>
                            <div class="h-3 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                                <div
                                    class="h-full rounded-full bg-brand-500 transition-all duration-500"
                                    :style="{ width: `${state.monthly_target.progress_percentage}%` }"></div>
                            </div>
                        </div>

                        <p class="mt-6 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ state.monthly_target.message }}</p>
                    </div>
                </div>

                <div class="col-span-12 xl:col-span-8">
                    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-5">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Trafik 30 Hari Terakhir</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pantau views harian tulisan yang dibaca pembaca.</p>
                            </div>
                            <TrafikChart :labels="state.charts.traffic.labels" :series="state.charts.traffic.series" />
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-5">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Distribusi Tulisan per Kategori</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lihat kategori yang paling sering Anda isi akhir-akhir ini.</p>
                            </div>
                            <KategoriChart :labels="state.charts.categories.labels" :series="state.charts.categories.series" />
                        </div>
                    </div>
                </div>

                <div class="col-span-12 xl:col-span-8">
                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tulisan Terbaru</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pantau status, performa awal, dan akses cepat ke tulisan terbaru.</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                                <thead class="bg-gray-50 dark:bg-white/5">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Judul</th>
                                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Kategori</th>
                                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Views</th>
                                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal</th>
                                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                                        <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <tr v-for="post in state.recent_posts" :key="post.id" class="transition hover:bg-gray-50 dark:hover:bg-white/5">
                                        <td class="px-5 py-4">
                                            <div class="space-y-1">
                                                <p class="font-medium text-gray-900 dark:text-white">{{ post.title }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ post.author_name || 'Admin' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ post.category_name || '-' }}</td>
                                        <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ formatNumber(post.views) }}</td>
                                        <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ formatDate(post.published_at || post.updated_at) }}</td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize" :class="statusTone(post.status)">
                                                {{ post.status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a :href="post.edit_url" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">
                                                    Edit
                                                </a>
                                                <button @click="openPreview(post)" type="button" class="rounded-lg bg-brand-500 px-3 py-2 text-sm font-medium text-white transition hover:bg-brand-600">
                                                    Preview
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 xl:col-span-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tulisan Terpopuler</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">5 tulisan dengan views tertinggi saat ini.</p>
                            </div>
                            <div class="space-y-3">
                                <a
                                    v-for="post in state.top_posts"
                                    :key="post.id"
                                    :href="post.edit_url"
                                    class="block rounded-xl border border-gray-200 px-4 py-3 transition hover:border-brand-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:border-brand-500/30 dark:hover:bg-white/5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ post.title }}</p>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ post.category_name || 'Tanpa kategori' }}</p>
                                        </div>
                                        <span class="inline-flex rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
                                            {{ formatNumber(post.views) }}
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Aktivitas Terakhir</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Riwayat singkat aktivitas konten paling baru.</p>
                            </div>
                            <div class="space-y-4">
                                <div v-for="activity in state.activities" :key="`${activity.id}-${activity.time}`" class="flex gap-3">
                                    <div class="mt-1 h-2.5 w-2.5 rounded-full bg-brand-500"></div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ activity.action }}: {{ activity.title }}</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ activity.description }}</p>
                                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ activity.author_name }} • {{ formatDate(activity.time) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div v-if="previewOpen" class="fixed inset-0 z-[85] flex items-center justify-center bg-gray-900/60 px-4 py-8 backdrop-blur-sm">
            <div class="w-full max-w-5xl rounded-3xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Preview tulisan</p>
                        <h3 class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ previewTitle }}</h3>
                    </div>
                    <button @click="closePreview" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">
                        Tutup
                    </button>
                </div>

                <div class="max-h-[75vh] overflow-y-auto px-6 py-6">
                    <div v-if="previewLoading" class="space-y-4">
                        <div class="h-8 animate-pulse rounded-2xl bg-gray-50 dark:bg-white/5"></div>
                        <div class="h-64 animate-pulse rounded-3xl bg-gray-50 dark:bg-white/5"></div>
                    </div>

                    <article v-else class="prose prose-neutral max-w-none dark:prose-invert" v-html="previewHtml"></article>
                </div>
            </div>
        </div>
    </div>
</template>
