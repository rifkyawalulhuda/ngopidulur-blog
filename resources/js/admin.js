import { createApp } from 'vue';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

const pageFromPath = (path) => {
    if (path.startsWith('/admin/categories')) {
        return 'categories';
    }

    if (path.startsWith('/admin/tags')) {
        return 'tags';
    }

    return 'dashboard';
};

const apiHeaders = {
    'Content-Type': 'application/json',
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': csrfToken,
};

const toastTypes = {
    success: 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-100',
    error: 'border-red-200 bg-red-50 text-red-700 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-100',
    info: 'border-coffee-200 bg-coffee-50 text-coffee-900 dark:border-coffee-800/50 dark:bg-coffee-500/10 dark:text-coffee-50',
};

function useApi(app) {
    return async (url, options = {}) => {
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                ...apiHeaders,
                ...(options.headers || {}),
            },
            ...options,
        });

        let payload = null;

        try {
            payload = await response.json();
        } catch {
            payload = null;
        }

        if (! response.ok) {
            const message = payload?.message || 'Terjadi kesalahan saat memproses permintaan.';
            throw new Error(message);
        }

        return payload;
    };
}

createApp({
    data() {
        const current = pageFromPath(window.location.pathname);

        return {
            current,
            api: null,
            toastTypes,
            dashboard: {
                loading: true,
                stats: {
                    total_posts: 0,
                    published_posts: 0,
                    draft_posts: 0,
                    archived_posts: 0,
                    total_categories: 0,
                    total_tags: 0,
                },
                latest_posts: [],
            },
            categories: {
                loading: true,
                saving: false,
                items: [],
                error: '',
            },
            tags: {
                loading: true,
                saving: false,
                items: [],
                error: '',
            },
            editor: {
                open: false,
                kind: 'category',
                mode: 'create',
                loading: false,
                id: null,
                name: '',
                slug: '',
                description: '',
                is_active: true,
            },
            deleting: {
                open: false,
                kind: 'category',
                item: null,
                loading: false,
            },
            toasts: [],
        };
    },

    computed: {
        pageTitle() {
            if (this.current === 'categories') {
                return 'Kategori';
            }

            if (this.current === 'tags') {
                return 'Tag';
            }

            return 'Dashboard';
        },

        pageSubtitle() {
            if (this.current === 'categories') {
                return 'Kelola kategori untuk menjaga tulisan tetap rapi dan mudah difilter.';
            }

            if (this.current === 'tags') {
                return 'Kelola tag untuk memberi penanda ringan pada tulisan.';
            }

            return 'Ringkasan aktivitas dan konten terbaru hadir di satu tempat.';
        },

        activeCategories() {
            return this.categories.items;
        },

        activeTags() {
            return this.tags.items;
        },
    },

    methods: {
        apiCall(url, options = {}) {
            return useApi(this)(url, options);
        },

        navigate(path) {
            if (window.location.pathname !== path) {
                window.history.pushState({}, '', path);
            }

            this.current = pageFromPath(path);
            this.loadCurrentPage();
        },

        handlePopState() {
            this.current = pageFromPath(window.location.pathname);
            this.loadCurrentPage();
        },

        async loadCurrentPage() {
            if (this.current === 'dashboard') {
                await this.loadDashboard();
            }

            if (this.current === 'categories') {
                await this.loadCategories();
            }

            if (this.current === 'tags') {
                await this.loadTags();
            }
        },

        async loadDashboard() {
            this.dashboard.loading = true;

            try {
                const payload = await this.apiCall('/admin/api/dashboard');
                this.dashboard.stats = payload.stats;
                this.dashboard.latest_posts = payload.latest_posts || [];
            } catch (error) {
                this.toast(error.message, 'error');
            } finally {
                this.dashboard.loading = false;
            }
        },

        async loadCategories() {
            this.categories.loading = true;
            this.categories.error = '';

            try {
                const payload = await this.apiCall('/admin/api/categories');
                this.categories.items = payload.items || [];
            } catch (error) {
                this.categories.error = error.message;
                this.toast(error.message, 'error');
            } finally {
                this.categories.loading = false;
            }
        },

        async loadTags() {
            this.tags.loading = true;
            this.tags.error = '';

            try {
                const payload = await this.apiCall('/admin/api/tags');
                this.tags.items = payload.items || [];
            } catch (error) {
                this.tags.error = error.message;
                this.toast(error.message, 'error');
            } finally {
                this.tags.loading = false;
            }
        },

        openCategoryCreate() {
            this.editor = {
                open: true,
                kind: 'category',
                mode: 'create',
                loading: false,
                id: null,
                name: '',
                slug: '',
                description: '',
                is_active: true,
            };
        },

        openTagCreate() {
            this.editor = {
                open: true,
                kind: 'tag',
                mode: 'create',
                loading: false,
                id: null,
                name: '',
                slug: '',
                description: '',
                is_active: true,
            };
        },

        async openCategoryEdit(item) {
            this.editor.open = true;
            this.editor.kind = 'category';
            this.editor.mode = 'edit';
            this.editor.loading = true;
            this.editor.id = item.slug;

            try {
                const payload = await this.apiCall(`/admin/api/categories/${item.slug}`);
                this.fillEditorFromItem('category', payload.item);
            } catch (error) {
                this.toast(error.message, 'error');
                this.editor.open = false;
            } finally {
                this.editor.loading = false;
            }
        },

        async openTagEdit(item) {
            this.editor.open = true;
            this.editor.kind = 'tag';
            this.editor.mode = 'edit';
            this.editor.loading = true;
            this.editor.id = item.slug;

            try {
                const payload = await this.apiCall(`/admin/api/tags/${item.slug}`);
                this.fillEditorFromItem('tag', payload.item);
            } catch (error) {
                this.toast(error.message, 'error');
                this.editor.open = false;
            } finally {
                this.editor.loading = false;
            }
        },

        fillEditorFromItem(kind, item) {
            this.editor.kind = kind;
            this.editor.id = item.slug;
            this.editor.name = item.name || '';
            this.editor.slug = item.slug || '';
            this.editor.description = item.description || '';
            this.editor.is_active = item.is_active ?? true;
        },

        closeEditor() {
            this.editor.open = false;
        },

        async saveEditor() {
            this.editor.loading = true;

            const body = {
                name: this.editor.name,
                slug: this.editor.slug,
                description: this.editor.kind === 'category' ? this.editor.description : undefined,
                is_active: this.editor.kind === 'category' ? this.editor.is_active : undefined,
            };

            const endpoint = this.editor.kind === 'category'
                ? '/admin/api/categories'
                : '/admin/api/tags';

            const method = this.editor.mode === 'create' ? 'POST' : 'PUT';
            const url = this.editor.mode === 'create'
                ? endpoint
                : `${endpoint}/${this.editor.id}`;

            try {
                const payload = await this.apiCall(url, {
                    method,
                    body: JSON.stringify(body),
                });

                this.toast(payload.message || 'Tersimpan.', 'success');
                this.editor.open = false;

                if (this.editor.kind === 'category') {
                    await this.loadCategories();
                } else {
                    await this.loadTags();
                }
            } catch (error) {
                this.toast(error.message, 'error');
            } finally {
                this.editor.loading = false;
            }
        },

        promptDelete(kind, item) {
            this.deleting = {
                open: true,
                kind,
                item,
                loading: false,
            };
        },

        closeDelete() {
            this.deleting = {
                open: false,
                kind: 'category',
                item: null,
                loading: false,
            };
        },

        async confirmDelete() {
            if (! this.deleting.item) {
                return;
            }

            const kind = this.deleting.kind;
            const item = this.deleting.item;
            this.deleting.loading = true;

            const endpoint = kind === 'category'
                ? `/admin/api/categories/${item.slug}`
                : `/admin/api/tags/${item.slug}`;

            try {
                const payload = await this.apiCall(endpoint, {
                    method: 'DELETE',
                });

                this.toast(payload.message || 'Terhapus.', 'success');
                this.closeDelete();

                if (kind === 'category') {
                    await this.loadCategories();
                } else {
                    await this.loadTags();
                }
            } catch (error) {
                this.toast(error.message, 'error');
            } finally {
                this.deleting.loading = false;
            }
        },

        toast(message, type = 'info') {
            const id = crypto.randomUUID();

            this.toasts.push({ id, message, type });

            window.setTimeout(() => {
                this.toasts = this.toasts.filter((toast) => toast.id !== id);
            }, 3500);
        },

        slugHint(name) {
            return name
                .toString()
                .trim()
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)/g, '');
        },
    },

    async mounted() {
        this.api = useApi(this);
        window.addEventListener('popstate', this.handlePopState);
        await this.loadCurrentPage();
    },

    beforeUnmount() {
        window.removeEventListener('popstate', this.handlePopState);
    },

    template: `
        <div class="space-y-6">
            <section class="rounded-3xl border border-coffee-100 bg-white px-6 py-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="space-y-2">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-coffee-700 dark:text-coffee-100">
                            Ngopi Dulur Admin
                        </p>
                        <h2 class="font-lora text-3xl font-semibold text-coffee-900 dark:text-neutralwarm-50">
                            {{ pageTitle }}
                        </h2>
                        <p class="max-w-2xl text-sm leading-6 text-neutralwarm-500 dark:text-neutralwarm-100/75">
                            {{ pageSubtitle }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            @click="navigate('/admin/dashboard')"
                            class="rounded-full border px-4 py-2 text-sm font-semibold transition"
                            :class="current === 'dashboard' ? 'border-coffee-700 bg-coffee-700 text-white' : 'border-coffee-100 bg-white text-coffee-700 hover:bg-coffee-50 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5'">
                            Dashboard
                        </button>
                        <button
                            @click="navigate('/admin/categories')"
                            class="rounded-full border px-4 py-2 text-sm font-semibold transition"
                            :class="current === 'categories' ? 'border-coffee-700 bg-coffee-700 text-white' : 'border-coffee-100 bg-white text-coffee-700 hover:bg-coffee-50 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5'">
                            Kategori
                        </button>
                        <button
                            @click="navigate('/admin/tags')"
                            class="rounded-full border px-4 py-2 text-sm font-semibold transition"
                            :class="current === 'tags' ? 'border-coffee-700 bg-coffee-700 text-white' : 'border-coffee-100 bg-white text-coffee-700 hover:bg-coffee-50 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5'">
                            Tag
                        </button>
                    </div>
                </div>
            </section>

            <section v-if="current === 'dashboard'" class="space-y-6">
                <div v-if="dashboard.loading" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <div v-for="n in 6" :key="n" class="h-28 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                </div>

                <template v-else>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Total Posts</p>
                            <p class="mt-2 text-3xl font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ dashboard.stats.total_posts }}</p>
                        </div>
                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Published</p>
                            <p class="mt-2 text-3xl font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ dashboard.stats.published_posts }}</p>
                        </div>
                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Draft</p>
                            <p class="mt-2 text-3xl font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ dashboard.stats.draft_posts }}</p>
                        </div>
                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Archived</p>
                            <p class="mt-2 text-3xl font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ dashboard.stats.archived_posts }}</p>
                        </div>
                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Kategori</p>
                            <p class="mt-2 text-3xl font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ dashboard.stats.total_categories }}</p>
                        </div>
                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Tag</p>
                            <p class="mt-2 text-3xl font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ dashboard.stats.total_tags }}</p>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-3xl border border-coffee-100 bg-white shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                        <div class="border-b border-coffee-100 px-6 py-4 dark:border-coffee-800/40">
                            <h3 class="text-sm font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Tulisan terbaru</h3>
                        </div>

                        <div v-if="dashboard.latest_posts.length === 0" class="px-6 py-12">
                            <div class="rounded-3xl border border-dashed border-coffee-200 bg-coffee-50 px-6 py-10 text-center dark:border-coffee-800/50 dark:bg-coffee-500/10">
                                <p class="text-lg font-semibold text-coffee-900 dark:text-neutralwarm-50">Belum ada tulisan</p>
                                <p class="mt-2 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Dashboard akan menampilkan tulisan terbaru saat data sudah masuk.</p>
                            </div>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-coffee-100 dark:divide-coffee-800/40">
                                <thead class="bg-coffee-50/70 dark:bg-white/5">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Judul</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Kategori</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Publish</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-coffee-100 dark:divide-coffee-800/30">
                                    <tr v-for="post in dashboard.latest_posts" :key="post.id" class="transition hover:bg-coffee-50/70 dark:hover:bg-white/5">
                                        <td class="px-6 py-4">
                                            <div class="space-y-1">
                                                <p class="font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ post.title }}</p>
                                                <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ post.author_name || 'Admin' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ post.category_name || '-' }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]"
                                                :class="post.status === 'published' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-100' : post.status === 'draft' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-100' : 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-neutralwarm-100'">
                                                {{ post.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ post.published_at || '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
            </section>

            <section v-else-if="current === 'categories'" class="space-y-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Kategori untuk mengelompokkan tulisan.</p>
                    </div>
                    <button @click="openCategoryCreate" class="inline-flex items-center gap-2 rounded-full bg-coffee-700 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800">
                        Tambah Kategori
                    </button>
                </div>

                <div v-if="categories.loading" class="grid gap-4">
                    <div v-for="n in 3" :key="n" class="h-24 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                </div>

                <div v-else-if="categories.items.length === 0" class="rounded-3xl border border-dashed border-coffee-200 bg-white px-6 py-12 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <div class="mx-auto max-w-xl text-center">
                        <p class="text-lg font-semibold text-coffee-900 dark:text-neutralwarm-50">Belum ada kategori</p>
                        <p class="mt-2 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Tambahkan kategori pertama untuk mulai merapikan tulisan.</p>
                        <button @click="openCategoryCreate" class="mt-6 inline-flex items-center gap-2 rounded-full bg-coffee-700 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800">
                            Tambah Kategori
                        </button>
                    </div>
                </div>

                <div v-else class="overflow-hidden rounded-3xl border border-coffee-100 bg-white shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-coffee-100 dark:divide-coffee-800/40">
                            <thead class="bg-coffee-50/70 dark:bg-white/5">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Nama</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Slug</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Post</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-coffee-100 dark:divide-coffee-800/30">
                                <tr v-for="item in categories.items" :key="item.id" class="transition hover:bg-coffee-50/70 dark:hover:bg-white/5">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ item.name }}</p>
                                        <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ item.description || 'Tanpa deskripsi' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ item.slug }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]"
                                            :class="item.is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-100' : 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-neutralwarm-100'">
                                            {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ item.posts_count }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <button @click="openCategoryEdit(item)" class="rounded-full border border-coffee-100 px-3 py-2 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">
                                                Ubah
                                            </button>
                                            <button @click="promptDelete('category', item)" class="rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10">
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

            <section v-else class="space-y-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Tag untuk memberi penanda ringan pada tulisan.</p>
                    </div>
                    <button @click="openTagCreate" class="inline-flex items-center gap-2 rounded-full bg-coffee-700 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800">
                        Tambah Tag
                    </button>
                </div>

                <div v-if="tags.loading" class="grid gap-4">
                    <div v-for="n in 3" :key="n" class="h-24 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                </div>

                <div v-else-if="tags.items.length === 0" class="rounded-3xl border border-dashed border-coffee-200 bg-white px-6 py-12 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <div class="mx-auto max-w-xl text-center">
                        <p class="text-lg font-semibold text-coffee-900 dark:text-neutralwarm-50">Belum ada tag</p>
                        <p class="mt-2 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Tambahkan tag pertama untuk memberi penanda pada artikel.</p>
                        <button @click="openTagCreate" class="mt-6 inline-flex items-center gap-2 rounded-full bg-coffee-700 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800">
                            Tambah Tag
                        </button>
                    </div>
                </div>

                <div v-else class="overflow-hidden rounded-3xl border border-coffee-100 bg-white shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-coffee-100 dark:divide-coffee-800/40">
                            <thead class="bg-coffee-50/70 dark:bg-white/5">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Nama</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Slug</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Post</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-coffee-100 dark:divide-coffee-800/30">
                                <tr v-for="item in tags.items" :key="item.id" class="transition hover:bg-coffee-50/70 dark:hover:bg-white/5">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ item.name }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ item.slug }}</td>
                                    <td class="px-6 py-4 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ item.posts_count }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <button @click="openTagEdit(item)" class="rounded-full border border-coffee-100 px-3 py-2 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">
                                                Ubah
                                            </button>
                                            <button @click="promptDelete('tag', item)" class="rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10">
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

            <div
                v-if="editor.open"
                class="fixed inset-0 z-[70] flex items-center justify-center bg-neutralwarm-950/50 px-4 py-8 backdrop-blur-sm">
                <div class="w-full max-w-2xl rounded-3xl border border-coffee-100 bg-white shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <div class="flex items-center justify-between border-b border-coffee-100 px-6 py-4 dark:border-coffee-800/40">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">
                                {{ editor.kind === 'category' ? 'Kategori' : 'Tag' }}
                            </p>
                            <h3 class="mt-1 font-lora text-2xl font-semibold text-coffee-900 dark:text-neutralwarm-50">
                                {{ editor.mode === 'create' ? 'Tambah' : 'Ubah' }} {{ editor.kind === 'category' ? 'kategori' : 'tag' }}
                            </h3>
                        </div>
                        <button @click="closeEditor" class="rounded-full border border-coffee-100 bg-white px-3 py-2 text-sm font-semibold text-coffee-700 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-coffee-100">
                            Tutup
                        </button>
                    </div>

                    <div class="space-y-4 px-6 py-6">
                        <div v-if="editor.loading" class="space-y-4">
                            <div class="h-11 animate-pulse rounded-2xl bg-coffee-50 dark:bg-white/5"></div>
                            <div class="h-11 animate-pulse rounded-2xl bg-coffee-50 dark:bg-white/5"></div>
                            <div v-if="editor.kind === 'category'" class="h-24 animate-pulse rounded-2xl bg-coffee-50 dark:bg-white/5"></div>
                        </div>

                        <template v-else>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Nama</label>
                                <input v-model="editor.name" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50" placeholder="Contoh: Catatan Harian">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Slug</label>
                                <input v-model="editor.slug" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50" placeholder="otomatis-dari-nama">
                                <p class="text-xs text-neutralwarm-500 dark:text-neutralwarm-100/60">Boleh dikosongkan agar otomatis dibuat dari nama.</p>
                            </div>

                            <div v-if="editor.kind === 'category'" class="space-y-2">
                                <label class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Deskripsi</label>
                                <textarea v-model="editor.description" rows="4" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50" placeholder="Deskripsi singkat kategori"></textarea>
                            </div>

                            <label v-if="editor.kind === 'category'" class="flex items-center gap-3 rounded-2xl border border-coffee-100 px-4 py-3 text-sm text-neutralwarm-500 dark:border-coffee-800/50 dark:text-neutralwarm-100/70">
                                <input v-model="editor.is_active" type="checkbox" class="size-4 rounded border-coffee-300 text-coffee-700 focus:ring-coffee-300">
                                Kategori aktif
                            </label>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button @click="closeEditor" type="button" class="rounded-full border border-coffee-100 px-4 py-2.5 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">
                                    Batal
                                </button>
                                <button @click="saveEditor" type="button" class="rounded-full bg-coffee-700 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800" :disabled="editor.loading">
                                    {{ editor.loading ? 'Menyimpan...' : 'Simpan' }}
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div
                v-if="deleting.open"
                class="fixed inset-0 z-[80] flex items-center justify-center bg-neutralwarm-950/50 px-4 py-8 backdrop-blur-sm">
                <div class="w-full max-w-lg rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Konfirmasi hapus</p>
                    <h3 class="mt-2 font-lora text-2xl font-semibold text-coffee-900 dark:text-neutralwarm-50">
                        Hapus {{ deleting.kind === 'category' ? 'kategori' : 'tag' }} ini?
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-neutralwarm-500 dark:text-neutralwarm-100/70">
                        {{ deleting.kind === 'category'
                            ? 'Kategori yang masih dipakai artikel tidak dapat dihapus.'
                            : 'Tag akan dihapus dan relasi pada artikel dibersihkan otomatis.' }}
                    </p>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button @click="closeDelete" class="rounded-full border border-coffee-100 px-4 py-2.5 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">
                            Batal
                        </button>
                        <button @click="confirmDelete" class="rounded-full bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700" :disabled="deleting.loading">
                            {{ deleting.loading ? 'Menghapus...' : 'Ya, hapus' }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="fixed right-4 top-4 z-[90] flex w-full max-w-sm flex-col gap-3">
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="rounded-2xl border px-4 py-3 text-sm shadow-soft"
                    :class="toastTypes[toast.type] || toastTypes.info">
                    {{ toast.message }}
                </div>
            </div>
        </div>
    `,
}).mount('#ngopi-dulur-admin-app');
