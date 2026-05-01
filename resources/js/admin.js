import { createApp, defineAsyncComponent } from 'vue';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const TinyMceEditor = defineAsyncComponent(() => import('./components/tinymce-vue-editor'));

const pageFromPath = (path) => {
    if (path.startsWith('/admin/posts/create')) {
        return 'post-create';
    }

    if (path.match(/^\/admin\/posts\/[^/]+\/edit$/)) {
        return 'post-edit';
    }

    if (path.startsWith('/admin/posts')) {
        return 'posts';
    }

    if (path.startsWith('/admin/categories')) {
        return 'categories';
    }

    if (path.startsWith('/admin/tags')) {
        return 'tags';
    }

    if (path.startsWith('/admin/media')) {
        return 'media';
    }

    if (path.startsWith('/admin/settings')) {
        return 'settings';
    }

    return 'dashboard';
};

const postIdFromPath = (path) => {
    const match = path.match(/^\/admin\/posts\/([^/]+)\/edit$/);

    return match ? match[1] : null;
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
        const headers = {
            ...apiHeaders,
            ...(options.headers || {}),
        };

        if (options.body instanceof FormData) {
            delete headers['Content-Type'];
        }

        const response = await fetch(url, {
            credentials: 'same-origin',
            headers,
            ...options,
        });

        let payload = null;

        try {
            payload = await response.json();
        } catch {
            payload = null;
        }

        if (! response.ok) {
            const firstError = payload?.errors ? Object.values(payload.errors).flat()[0] : null;
            const message = payload?.message && payload.message !== 'The given data was invalid.'
                ? payload.message
                : firstError || 'Terjadi kesalahan saat memproses permintaan.';
            throw new Error(message);
        }

        return payload;
    };
}

createApp({
    components: {
        TinyMceEditor,
    },

    data() {
        const current = pageFromPath(window.location.pathname);

        return {
            current,
            api: null,
            themeObserver: null,
            tinyMceTheme: 'light',
            tinyMceMountKey: 0,
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
            media: {
                loading: true,
                items: [],
                error: '',
            },
            settings: {
                loading: true,
                saving: false,
                site_name: '',
                site_tagline: '',
                site_description: '',
                logo: '',
                logo_url: '',
                logo_file: null,
                favicon: '',
                favicon_url: '',
                favicon_file: null,
                default_meta_title: '',
                default_meta_description: '',
                default_og_image: '',
                default_og_image_url: '',
                default_og_image_file: null,
                footer_note: '',
                social_links: {
                    instagram: '',
                    x: '',
                    github: '',
                    linkedin: '',
                    youtube: '',
                },
                hero_badge: '',
                hero_heading: '',
                hero_subheading: '',
                hero_cta_text: '',
                default_theme: 'terang',
                remove_logo: false,
                remove_favicon: false,
                remove_default_og_image: false,
            },
            posts: {
                loading: true,
                saving: false,
                items: [],
                filters: {
                    search: '',
                    status: '',
                    category: '',
                    sort: 'updated_at',
                },
                meta: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 0,
                },
                error: '',
            },
            postEditor: {
                open: false,
                mode: 'create',
                loading: false,
                saving: false,
                id: null,
                title: '',
                slug: '',
                excerpt: '',
                content_format: 'richtext',
                content: '',
                featured_image_file: null,
                featured_image_url: '',
                featured_image_alt: '',
                category_id: '',
                tag_ids: [],
                status: 'draft',
                is_featured: false,
                meta_title: '',
                meta_description: '',
                published_at: '',
                preview: {
                    open: false,
                    loading: false,
                    html: '',
                    title: '',
                },
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

            if (this.current === 'media') {
                return 'Media';
            }

            if (this.current === 'settings') {
                return 'Pengaturan';
            }

            if (this.current === 'posts' || this.current === 'post-create' || this.current === 'post-edit') {
                return 'Tulisan';
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

            if (this.current === 'media') {
                return 'Lihat gambar unggulan yang sudah terhubung ke tulisan tanpa membuat media library yang terlalu berat.';
            }

            if (this.current === 'settings') {
                return 'Atur identitas blog, aset brand, SEO default, dan nuansa public blog dari satu tempat.';
            }

            if (this.current === 'posts') {
                return 'Kelola artikel, filter konten, dan lakukan aksi terbit atau arsip dari satu tempat.';
            }

            if (this.current === 'post-create') {
                return 'Tulis draft baru, pilih kategori, dan siapkan artikel sebelum diterbitkan.';
            }

            if (this.current === 'post-edit') {
                return 'Ubah isi, status, dan metadata artikel tanpa meninggalkan panel admin.';
            }

            return 'Ringkasan aktivitas dan konten terbaru hadir di satu tempat.';
        },

        activeCategories() {
            return this.categories.items;
        },

        activeTags() {
            return this.tags.items;
        },

        tinyMceInit() {
            const darkMode = this.tinyMceTheme === 'dark';

            return {
                menubar: false,
                branding: false,
                promotion: false,
                browser_spellcheck: true,
                min_height: 520,
                autoresize_bottom_margin: 20,
                resize: false,
                toolbar_mode: 'sliding',
                plugins: 'advlist autolink autoresize code help link lists preview visualblocks wordcount',
                toolbar: 'undo redo | blocks | bold italic blockquote | bullist numlist | link | removeformat | code visualblocks preview',
                block_formats: 'Paragraf=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Kutipan=blockquote; Preformatted=pre',
                quickbars_selection_toolbar: 'bold italic | quicklink blockquote',
                contextmenu: false,
                skin: darkMode ? 'oxide-dark' : 'oxide',
                content_css: darkMode ? 'dark' : 'default',
                content_style: `
                    body {
                        font-family: Inter, "Plus Jakarta Sans", system-ui, sans-serif;
                        font-size: 16px;
                        line-height: 1.8;
                        color: ${darkMode ? '#f7efe7' : '#2d1f18'};
                        background-color: ${darkMode ? '#20140f' : '#fffdfb'};
                        margin: 1rem;
                    }
                    p { margin: 0 0 1rem; }
                    h1, h2, h3, h4, h5, h6 {
                        font-family: Lora, Georgia, serif;
                        color: ${darkMode ? '#fff7f0' : '#2a160e'};
                        margin: 1.4rem 0 0.8rem;
                    }
                    blockquote {
                        border-left: 4px solid ${darkMode ? '#a15f2a' : '#b56a2d'};
                        margin: 1.25rem 0;
                        padding: 0.1rem 0 0.1rem 1rem;
                        color: ${darkMode ? '#e7d4c7' : '#6f513d'};
                    }
                    a {
                        color: ${darkMode ? '#f2b26b' : '#8f5624'};
                    }
                    ul, ol {
                        margin: 0 0 1rem 1.25rem;
                    }
                    code, pre {
                        background: ${darkMode ? '#2c1c15' : '#f7efe8'};
                        border-radius: 0.5rem;
                    }
                `,
            };
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

            if (this.current === 'posts') {
                await this.loadPosts();
            }

            if (this.current === 'post-create') {
                await this.loadPostEditor('create');
            }

            if (this.current === 'post-edit') {
                await this.loadPostEditor('edit', postIdFromPath(window.location.pathname));
            }

            if (this.current === 'categories') {
                await this.loadCategories();
            }

            if (this.current === 'tags') {
                await this.loadTags();
            }

            if (this.current === 'media') {
                await this.loadMedia();
            }

            if (this.current === 'settings') {
                await this.loadSettings();
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

        async loadMedia() {
            this.media.loading = true;
            this.media.error = '';

            try {
                const payload = await this.apiCall('/admin/api/media');
                this.media.items = payload.items || [];
            } catch (error) {
                this.media.error = error.message;
                this.toast(error.message, 'error');
            } finally {
                this.media.loading = false;
            }
        },

        async loadSettings() {
            this.settings.loading = true;

            try {
                const payload = await this.apiCall('/admin/api/settings');
                this.fillSettings(payload.item || {});
            } catch (error) {
                this.toast(error.message, 'error');
            } finally {
                this.settings.loading = false;
            }
        },

        async loadPosts() {
            this.posts.loading = true;
            this.posts.error = '';

            try {
                const params = new URLSearchParams();

                if (this.posts.filters.search) {
                    params.set('search', this.posts.filters.search);
                }

                if (this.posts.filters.status) {
                    params.set('status', this.posts.filters.status);
                }

                if (this.posts.filters.category) {
                    params.set('category', this.posts.filters.category);
                }

                if (this.posts.filters.sort) {
                    params.set('sort', this.posts.filters.sort);
                }

                const payload = await this.apiCall(`/admin/api/posts?${params.toString()}`);
                this.posts.items = payload.items || [];
                this.posts.meta = payload.meta || this.posts.meta;
                this.categories.items = payload.categories || this.categories.items;
                this.tags.items = payload.tags || this.tags.items;
                this.posts.filters = {
                    ...this.posts.filters,
                    ...(payload.filters || {}),
                };
            } catch (error) {
                this.posts.error = error.message;
                this.toast(error.message, 'error');
            } finally {
                this.posts.loading = false;
            }
        },

        async ensurePostOptionsLoaded() {
            const tasks = [];

            if (this.categories.items.length === 0) {
                tasks.push(this.loadCategories());
            }

            if (this.tags.items.length === 0) {
                tasks.push(this.loadTags());
            }

            await Promise.all(tasks);
        },

        resetPostEditor() {
            this.postEditor = {
                open: true,
                mode: 'create',
                loading: false,
                saving: false,
                id: null,
                title: '',
                slug: '',
                excerpt: '',
                content_format: 'richtext',
                content: '',
                featured_image_file: null,
                featured_image_url: '',
                featured_image_alt: '',
                category_id: this.categories.items[0]?.id || '',
                tag_ids: [],
                status: 'draft',
                is_featured: false,
                meta_title: '',
                meta_description: '',
                published_at: '',
                preview: {
                    open: false,
                    loading: false,
                    html: '',
                    title: '',
                },
            };
        },

        async loadPostEditor(mode, id = null) {
            try {
                await this.ensurePostOptionsLoaded();
                this.resetPostEditor();
                this.postEditor.mode = mode;
                this.postEditor.id = id;
                this.postEditor.open = true;
                this.postEditor.loading = mode === 'edit';

                if (mode === 'edit' && id) {
                    const payload = await this.apiCall(`/admin/api/posts/${id}`);
                    this.fillPostEditor(payload.item);
                }
            } catch (error) {
                this.toast(error.message, 'error');

                if (mode === 'create') {
                    this.navigate('/admin/posts');
                }
            } finally {
                this.postEditor.loading = false;
            }
        },

        fillPostEditor(item) {
            this.postEditor.id = item.id;
            this.postEditor.title = item.title || '';
            this.postEditor.slug = item.slug || '';
            this.postEditor.excerpt = item.excerpt || '';
            this.postEditor.content_format = item.content_format || 'richtext';
            this.postEditor.content = item.content || '';
            this.postEditor.featured_image_url = item.featured_image_url || '';
            this.postEditor.featured_image_alt = item.featured_image_alt || '';
            this.postEditor.category_id = item.category_id || item.category?.id || '';
            this.postEditor.tag_ids = (item.tag_ids || []).map((tagId) => Number(tagId));
            this.postEditor.status = item.status || 'draft';
            this.postEditor.is_featured = Boolean(item.is_featured);
            this.postEditor.meta_title = item.meta_title || '';
            this.postEditor.meta_description = item.meta_description || '';
            this.postEditor.published_at = item.published_at ? item.published_at.slice(0, 16) : '';
        },

        syncPostEditorFromItem(item) {
            if (! item) {
                return false;
            }

            const currentPostId = Number(this.postEditor.id || postIdFromPath(window.location.pathname) || 0);
            const itemId = Number(item.id || 0);

            if (! currentPostId || ! itemId || currentPostId !== itemId) {
                return false;
            }

            this.postEditor.mode = 'edit';
            this.fillPostEditor(item);

            return true;
        },

        openPostCreate() {
            this.navigate('/admin/posts/create');
        },

        async openPostEdit(item) {
            this.navigate(`/admin/posts/${item.id}/edit`);
        },

        changePostFilters(patch) {
            this.posts.filters = {
                ...this.posts.filters,
                ...patch,
            };
        },

        async submitPost(statusOverride = null) {
            this.postEditor.saving = true;

            const formData = new FormData();
            formData.append('title', this.postEditor.title);
            formData.append('slug', this.postEditor.slug || '');
            formData.append('excerpt', this.postEditor.excerpt || '');
            formData.append('content_format', this.postEditor.content_format);
            formData.append('content', this.postEditor.content);
            formData.append('category_id', this.postEditor.category_id);
            this.postEditor.tag_ids.forEach((tagId) => formData.append('tags[]', tagId));
            formData.append('status', statusOverride || this.postEditor.status);
            formData.append('is_featured', this.postEditor.is_featured ? '1' : '0');
            formData.append('meta_title', this.postEditor.meta_title || '');
            formData.append('meta_description', this.postEditor.meta_description || '');
            formData.append('published_at', this.postEditor.published_at || '');
            formData.append('featured_image_alt', this.postEditor.featured_image_alt || '');

            if (this.postEditor.featured_image_file) {
                formData.append('featured_image', this.postEditor.featured_image_file);
            }

            const isEdit = this.postEditor.mode === 'edit' && this.postEditor.id;
            const url = isEdit
                ? `/admin/api/posts/${this.postEditor.id}`
                : '/admin/api/posts';

            if (isEdit) {
                formData.append('_method', 'PUT');
            }

            try {
                const payload = await this.apiCall(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                this.toast(payload.message || 'Artikel berhasil disimpan.', 'success');

                if (isEdit) {
                    await this.loadPostEditor('edit', this.postEditor.id);
                } else {
                    this.navigate(`/admin/posts/${payload.item.id}/edit`);
                }

                await this.loadPosts();
            } catch (error) {
                this.toast(error.message, 'error');
            } finally {
                this.postEditor.saving = false;
            }
        },

        async publishPost(item) {
            const syncEditor = this.syncPostEditorFromItem({ id: item.id });

            if (syncEditor) {
                this.postEditor.saving = true;
            }

            try {
                const payload = await this.apiCall(`/admin/api/posts/${item.id}/publish`, {
                    method: 'POST',
                });

                this.syncPostEditorFromItem(payload.item);
                this.toast(payload.message || 'Artikel berhasil diterbitkan.', 'success');
                await this.loadPosts();
            } catch (error) {
                this.toast(error.message, 'error');
            } finally {
                if (syncEditor) {
                    this.postEditor.saving = false;
                }
            }
        },

        async archivePost(item) {
            const syncEditor = this.syncPostEditorFromItem({ id: item.id });

            if (syncEditor) {
                this.postEditor.saving = true;
            }

            try {
                const payload = await this.apiCall(`/admin/api/posts/${item.id}/archive`, {
                    method: 'POST',
                });

                this.syncPostEditorFromItem(payload.item);
                this.toast(payload.message || 'Artikel berhasil diarsipkan.', 'success');
                await this.loadPosts();
            } catch (error) {
                this.toast(error.message, 'error');
            } finally {
                if (syncEditor) {
                    this.postEditor.saving = false;
                }
            }
        },

        async previewPost() {
            if (! this.postEditor.id) {
                this.toast('Simpan artikel terlebih dahulu sebelum preview.', 'info');
                return;
            }

            this.postEditor.preview = {
                open: true,
                loading: true,
                html: '',
                title: this.postEditor.title || 'Pratinjau',
            };
            this.postEditor.preview.loading = true;

            try {
                const payload = await this.apiCall(`/admin/api/posts/${this.postEditor.id}/preview`);
                this.postEditor.preview = {
                    open: true,
                    loading: false,
                    html: payload.preview_html || payload.item.rendered_content || '',
                    title: payload.item.title || this.postEditor.title || 'Pratinjau',
                };
            } catch (error) {
                this.toast(error.message, 'error');
            } finally {
                this.postEditor.preview.loading = false;
            }
        },

        async previewPostById(item) {
            this.postEditor.preview = {
                open: true,
                loading: true,
                html: '',
                title: item.title || 'Pratinjau',
            };
            this.postEditor.preview.loading = true;

            try {
                const payload = await this.apiCall(`/admin/api/posts/${item.id}/preview`);
                this.postEditor.preview = {
                    open: true,
                    loading: false,
                    html: payload.preview_html || payload.item.rendered_content || '',
                    title: payload.item.title || item.title || 'Pratinjau',
                };
            } catch (error) {
                this.toast(error.message, 'error');
            } finally {
                this.postEditor.preview.loading = false;
            }
        },

        closePostPreview() {
            this.postEditor.preview = {
                open: false,
                loading: false,
                html: '',
                title: '',
            };
        },

        setPostContentFormat(format) {
            if (this.postEditor.content_format === format) {
                return;
            }

            this.postEditor.content_format = format;

            if (format === 'richtext') {
                this.$nextTick(() => {
                    this.syncTinyMceTheme();
                });
            }
        },

        syncTinyMceTheme() {
            const nextTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';

            if (this.tinyMceTheme !== nextTheme) {
                this.tinyMceTheme = nextTheme;
                this.tinyMceMountKey += 1;
            }
        },

        applyPostFormatting(type) {
            const textarea = this.$refs.postContent;

            if (this.postEditor.content_format !== 'markdown' || ! textarea) {
                return;
            }

            const start = textarea.selectionStart ?? 0;
            const end = textarea.selectionEnd ?? 0;
            const selected = this.postEditor.content.slice(start, end) || this.getFormattingPlaceholder(type);

            const insert = (before, after = '') => {
                const value = `${before}${selected}${after}`;
                this.postEditor.content = `${this.postEditor.content.slice(0, start)}${value}${this.postEditor.content.slice(end)}`;
                this.$nextTick(() => {
                    textarea.focus();
                    const cursor = start + before.length;
                    textarea.setSelectionRange(cursor, cursor + selected.length);
                });
            };

            if (type === 'bold') {
                insert('**', '**');
            } else if (type === 'italic') {
                insert('*', '*');
            } else if (type === 'heading') {
                insert('# ', '');
            } else if (type === 'quote') {
                insert('> ', '');
            } else if (type === 'list') {
                insert('- ', '');
            } else if (type === 'ordered') {
                insert('1. ', '');
            } else if (type === 'link') {
                const url = window.prompt('Masukkan URL tautan');

                if (! url) {
                    return;
                }

                insert('[', `](${url})`);
            }
        },

        getFormattingPlaceholder(type) {
            if (type === 'heading') {
                return 'Judul kecil';
            }

            if (type === 'quote') {
                return 'Kutipan hangat';
            }

            if (type === 'list') {
                return 'Item daftar';
            }

            if (type === 'ordered') {
                return 'Item daftar';
            }

            if (type === 'link') {
                return 'Teks tautan';
            }

            return 'Teks';
        },

        updatePostFeaturedImage(event) {
            const [file] = event.target.files || [];

            this.postEditor.featured_image_file = file || null;

            if (file) {
                this.postEditor.featured_image_url = URL.createObjectURL(file);
            }
        },

        removePostFeaturedImage() {
            this.postEditor.featured_image_file = null;
            this.postEditor.featured_image_url = '';
            this.postEditor.featured_image_alt = '';

            const input = this.$refs.postFeaturedImage;

            if (input) {
                input.value = '';
            }
        },

        closePostEditor() {
            this.postEditor.open = false;
            this.navigate('/admin/posts');
        },

        fillSettings(item) {
            this.settings = {
                ...this.settings,
                site_name: item.site_name || '',
                site_tagline: item.site_tagline || '',
                site_description: item.site_description || '',
                logo: item.logo || '',
                logo_url: item.logo_url || '',
                logo_file: null,
                favicon: item.favicon || '',
                favicon_url: item.favicon_url || '',
                favicon_file: null,
                default_meta_title: item.default_meta_title || '',
                default_meta_description: item.default_meta_description || '',
                default_og_image: item.default_og_image || '',
                default_og_image_url: item.default_og_image_url || '',
                default_og_image_file: null,
                footer_note: item.footer_note || '',
                social_links: {
                    instagram: item.social_links?.instagram || '',
                    x: item.social_links?.x || '',
                    github: item.social_links?.github || '',
                    linkedin: item.social_links?.linkedin || '',
                    youtube: item.social_links?.youtube || '',
                },
                hero_badge: item.hero_badge || '',
                hero_heading: item.hero_heading || '',
                hero_subheading: item.hero_subheading || '',
                hero_cta_text: item.hero_cta_text || '',
                default_theme: item.default_theme || 'terang',
                remove_logo: false,
                remove_favicon: false,
                remove_default_og_image: false,
            };
        },

        updateSettingsAsset(kind, event) {
            const file = (event.target.files || [])[0] || null;
            const fileField = `${kind}_file`;
            const urlField = `${kind}_url`;
            const removeField = `remove_${kind}`;

            this.settings[fileField] = file;
            this.settings[removeField] = false;

            if (file) {
                this.settings[urlField] = URL.createObjectURL(file);
            }
        },

        clearSettingsAsset(kind) {
            const fileField = `${kind}_file`;
            const urlField = `${kind}_url`;
            const pathField = kind;
            const removeField = `remove_${kind}`;

            this.settings[fileField] = null;
            this.settings[urlField] = '';
            this.settings[pathField] = '';
            this.settings[removeField] = true;

            const input = this.$refs[`settings_${kind}`];

            if (input) {
                input.value = '';
            }
        },

        async saveSettings() {
            this.settings.saving = true;

            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('site_name', this.settings.site_name);
            formData.append('site_tagline', this.settings.site_tagline);
            formData.append('site_description', this.settings.site_description);
            formData.append('default_meta_title', this.settings.default_meta_title);
            formData.append('default_meta_description', this.settings.default_meta_description);
            formData.append('footer_note', this.settings.footer_note);
            formData.append('hero_badge', this.settings.hero_badge);
            formData.append('hero_heading', this.settings.hero_heading);
            formData.append('hero_subheading', this.settings.hero_subheading);
            formData.append('hero_cta_text', this.settings.hero_cta_text);
            formData.append('default_theme', this.settings.default_theme);
            formData.append('remove_logo', this.settings.remove_logo ? '1' : '0');
            formData.append('remove_favicon', this.settings.remove_favicon ? '1' : '0');
            formData.append('remove_default_og_image', this.settings.remove_default_og_image ? '1' : '0');

            Object.entries(this.settings.social_links).forEach(([key, value]) => {
                formData.append(`social_links[${key}]`, value || '');
            });

            if (this.settings.logo_file) {
                formData.append('logo', this.settings.logo_file);
            }

            if (this.settings.favicon_file) {
                formData.append('favicon', this.settings.favicon_file);
            }

            if (this.settings.default_og_image_file) {
                formData.append('default_og_image', this.settings.default_og_image_file);
            }

            try {
                const payload = await this.apiCall('/admin/api/settings', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                this.fillSettings(payload.item || {});
                this.toast(payload.message || 'Pengaturan berhasil diperbarui.', 'success');
            } catch (error) {
                this.toast(error.message, 'error');
            } finally {
                this.settings.saving = false;
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

            let endpoint = `/admin/api/tags/${item.slug}`;

            if (kind === 'category') {
                endpoint = `/admin/api/categories/${item.slug}`;
            } else if (kind === 'post') {
                endpoint = `/admin/api/posts/${item.id}`;
            }

            try {
                const payload = await this.apiCall(endpoint, {
                    method: 'DELETE',
                });

                this.toast(payload.message || 'Terhapus.', 'success');
                this.closeDelete();

                if (kind === 'category') {
                    await this.loadCategories();
                } else if (kind === 'post') {
                    await this.loadPosts();
                    if (this.current === 'post-edit' || this.current === 'post-create') {
                        this.navigate('/admin/posts');
                    }
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
        this.syncTinyMceTheme();
        this.themeObserver = new MutationObserver(() => this.syncTinyMceTheme());
        this.themeObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class'],
        });
        window.addEventListener('popstate', this.handlePopState);
        await this.loadCurrentPage();
    },

    beforeUnmount() {
        this.themeObserver?.disconnect();
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
                            @click="navigate('/admin/posts')"
                            class="rounded-full border px-4 py-2 text-sm font-semibold transition"
                            :class="current === 'posts' || current === 'post-create' || current === 'post-edit' ? 'border-coffee-700 bg-coffee-700 text-white' : 'border-coffee-100 bg-white text-coffee-700 hover:bg-coffee-50 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5'">
                            Tulisan
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
                        <button
                            @click="navigate('/admin/media')"
                            class="rounded-full border px-4 py-2 text-sm font-semibold transition"
                            :class="current === 'media' ? 'border-coffee-700 bg-coffee-700 text-white' : 'border-coffee-100 bg-white text-coffee-700 hover:bg-coffee-50 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5'">
                            Media
                        </button>
                        <button
                            @click="navigate('/admin/settings')"
                            class="rounded-full border px-4 py-2 text-sm font-semibold transition"
                            :class="current === 'settings' ? 'border-coffee-700 bg-coffee-700 text-white' : 'border-coffee-100 bg-white text-coffee-700 hover:bg-coffee-50 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5'">
                            Pengaturan
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

            <section v-if="current === 'posts'" class="space-y-6">
                <div class="rounded-3xl border border-coffee-100 bg-white p-5 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <div class="grid gap-4 lg:grid-cols-12 lg:items-end">
                        <label class="space-y-2 lg:col-span-4">
                            <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Cari judul</span>
                            <input
                                v-model="posts.filters.search"
                                @keyup.enter="loadPosts"
                                type="search"
                                class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50"
                                placeholder="Cari tulisan...">
                        </label>

                        <label class="space-y-2 lg:col-span-2">
                            <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Status</span>
                            <select v-model="posts.filters.status" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                <option value="">Semua</option>
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </label>

                        <label class="space-y-2 lg:col-span-2">
                            <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Kategori</span>
                            <select v-model="posts.filters.category" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                <option value="">Semua</option>
                                <option v-for="category in categories.items" :key="category.id" :value="category.slug">
                                    {{ category.name }}
                                </option>
                            </select>
                        </label>

                        <label class="space-y-2 lg:col-span-2">
                            <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Urutkan</span>
                            <select v-model="posts.filters.sort" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                <option value="updated_at">Update terbaru</option>
                                <option value="published_at">Tanggal publish</option>
                            </select>
                        </label>

                        <div class="flex flex-wrap gap-2 lg:col-span-2 lg:justify-end">
                            <button @click="loadPosts" class="inline-flex items-center justify-center rounded-full bg-coffee-700 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800">
                                Terapkan
                            </button>
                            <button @click="changePostFilters({ search: '', status: '', category: '', sort: 'updated_at' }); loadPosts()" class="inline-flex items-center justify-center rounded-full border border-coffee-100 px-4 py-2.5 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Daftar artikel terbaru dengan filter yang cepat dipakai.</p>
                    <button @click="openPostCreate" class="inline-flex items-center gap-2 rounded-full bg-coffee-700 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800">
                        Tulis Artikel Baru
                    </button>
                </div>

                <div v-if="posts.loading" class="grid gap-4">
                    <div v-for="n in 3" :key="n" class="h-28 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                </div>

                <div v-else-if="posts.items.length === 0" class="rounded-3xl border border-dashed border-coffee-200 bg-white px-6 py-12 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <div class="mx-auto max-w-xl text-center">
                        <p class="text-lg font-semibold text-coffee-900 dark:text-neutralwarm-50">Belum ada artikel</p>
                        <p class="mt-2 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Mulai dengan draft pertama, lalu terbitkan saat kontennya siap.</p>
                        <button @click="openPostCreate" class="mt-6 inline-flex items-center gap-2 rounded-full bg-coffee-700 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800">
                            Tulis Artikel Baru
                        </button>
                    </div>
                </div>

                <div v-else class="overflow-hidden rounded-3xl border border-coffee-100 bg-white shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-coffee-100 dark:divide-coffee-800/40">
                            <thead class="bg-coffee-50/70 dark:bg-white/5">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Judul</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Slug</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Kategori</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Tanggal Publish</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Update Terakhir</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-coffee-100 dark:divide-coffee-800/30">
                                <tr v-for="item in posts.items" :key="item.id" class="transition hover:bg-coffee-50/70 dark:hover:bg-white/5">
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <p class="font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ item.title }}</p>
                                            <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ item.author_name || 'Admin' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ item.slug }}</td>
                                    <td class="px-6 py-4 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ item.category_name || '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]"
                                            :class="item.status === 'published' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-100' : item.status === 'draft' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-100' : 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-neutralwarm-100'">
                                            {{ item.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ item.published_at || '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ item.updated_at || '-' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <button @click="previewPostById(item)" class="rounded-full border border-coffee-100 px-3 py-2 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">
                                                Preview
                                            </button>
                                            <button @click="openPostEdit(item)" class="rounded-full border border-coffee-100 px-3 py-2 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">
                                                Ubah
                                            </button>
                                            <button v-if="item.status !== 'published'" @click="publishPost(item)" class="rounded-full border border-emerald-200 px-3 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50 dark:border-emerald-500/30 dark:text-emerald-100 dark:hover:bg-emerald-500/10">
                                                Terbitkan
                                            </button>
                                            <button v-if="item.status !== 'archived'" @click="archivePost(item)" class="rounded-full border border-amber-200 px-3 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50 dark:border-amber-500/30 dark:text-amber-100 dark:hover:bg-amber-500/10">
                                                Arsipkan
                                            </button>
                                            <button @click="promptDelete('post', item)" class="rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10">
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

            <section v-else-if="current === 'post-create' || current === 'post-edit'" class="space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <button @click="navigate('/admin/posts')" class="rounded-full border border-coffee-100 px-4 py-2.5 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">
                            Kembali
                        </button>
                        <span class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">
                            {{ postEditor.mode === 'create' ? 'Mode membuat draft baru' : 'Mode mengedit artikel' }}
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button @click="previewPost" class="inline-flex items-center gap-2 rounded-full border border-coffee-100 px-4 py-2.5 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5" :disabled="postEditor.loading || postEditor.saving">
                            Preview
                        </button>
                        <button @click="submitPost('draft')" class="inline-flex items-center gap-2 rounded-full border border-coffee-100 px-4 py-2.5 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5" :disabled="postEditor.loading || postEditor.saving">
                            {{ postEditor.saving ? 'Menyimpan...' : 'Simpan Draft' }}
                        </button>
                        <button @click="submitPost('published')" class="inline-flex items-center gap-2 rounded-full bg-coffee-700 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800" :disabled="postEditor.loading || postEditor.saving">
                            {{ postEditor.saving ? 'Menyimpan...' : 'Terbitkan' }}
                        </button>
                    </div>
                </div>

                <div v-if="postEditor.loading" class="grid gap-4 xl:grid-cols-12">
                    <div class="xl:col-span-8 space-y-4">
                        <div class="h-16 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                        <div class="h-16 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                        <div class="h-64 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                    </div>
                    <div class="xl:col-span-4 space-y-4">
                        <div class="h-48 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                        <div class="h-64 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                    </div>
                </div>

                <div v-else class="grid gap-6 xl:grid-cols-12">
                    <div class="space-y-6 xl:col-span-8">
                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <div class="grid gap-4">
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Judul</span>
                                    <input v-model="postEditor.title" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50" placeholder="Contoh: Secangkir Pagi">
                                </label>

                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Slug</span>
                                    <input v-model="postEditor.slug" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50" placeholder="otomatis-dari-judul">
                                    <p class="text-xs text-neutralwarm-500 dark:text-neutralwarm-100/60">Kosongkan untuk slug otomatis. Jika diisi manual, slug harus unik.</p>
                                </label>

                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Ringkasan</span>
                                    <textarea v-model="postEditor.excerpt" rows="3" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50" placeholder="Opsional, untuk kartu artikel dan SEO"></textarea>
                                </label>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Editor</p>
                                    <p class="mt-1 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Mode Visual menyimpan HTML, Mode Markdown menyimpan markdown asli.</p>
                                </div>
                                <div class="inline-flex rounded-full border border-coffee-100 bg-coffee-50 p-1 dark:border-coffee-800/50 dark:bg-white/5">
                                    <button @click="setPostContentFormat('richtext')" class="rounded-full px-4 py-2 text-sm font-semibold transition" :class="postEditor.content_format === 'richtext' ? 'bg-white text-coffee-700 shadow-soft dark:bg-neutralwarm-900 dark:text-neutralwarm-50' : 'text-coffee-700 dark:text-coffee-100'">
                                        Mode Visual
                                    </button>
                                    <button @click="setPostContentFormat('markdown')" class="rounded-full px-4 py-2 text-sm font-semibold transition" :class="postEditor.content_format === 'markdown' ? 'bg-white text-coffee-700 shadow-soft dark:bg-neutralwarm-900 dark:text-neutralwarm-50' : 'text-coffee-700 dark:text-coffee-100'">
                                        Mode Markdown
                                    </button>
                                </div>
                            </div>

                            <div v-if="postEditor.content_format === 'markdown'" class="mt-4 flex flex-wrap gap-2">
                                <button @click="applyPostFormatting('bold')" type="button" class="rounded-full border border-coffee-100 px-3 py-2 text-xs font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">B</button>
                                <button @click="applyPostFormatting('italic')" type="button" class="rounded-full border border-coffee-100 px-3 py-2 text-xs font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">I</button>
                                <button @click="applyPostFormatting('heading')" type="button" class="rounded-full border border-coffee-100 px-3 py-2 text-xs font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">H1</button>
                                <button @click="applyPostFormatting('quote')" type="button" class="rounded-full border border-coffee-100 px-3 py-2 text-xs font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">Quote</button>
                                <button @click="applyPostFormatting('list')" type="button" class="rounded-full border border-coffee-100 px-3 py-2 text-xs font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">UL</button>
                                <button @click="applyPostFormatting('ordered')" type="button" class="rounded-full border border-coffee-100 px-3 py-2 text-xs font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">OL</button>
                                <button @click="applyPostFormatting('link')" type="button" class="rounded-full border border-coffee-100 px-3 py-2 text-xs font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">Link</button>
                            </div>

                            <div class="mt-4 space-y-3">
                                <div v-if="postEditor.content_format === 'richtext'" class="ngopi-tinymce overflow-hidden rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/50 dark:bg-neutralwarm-900">
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
                                    class="w-full rounded-3xl border border-coffee-100 bg-white px-4 py-3 text-sm leading-7 text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50"
                                    placeholder="# Judul artikel"></textarea>

                                <p class="text-xs text-neutralwarm-500 dark:text-neutralwarm-100/60">
                                    {{ postEditor.content_format === 'markdown' ? 'Markdown akan dirender dan disanitasi di server.' : 'Konten visual disimpan sebagai HTML yang sudah disanitasi di server.' }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Preview aman</p>
                                    <p class="mt-1 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Preview ditarik dari server dan memakai HTML yang sudah disanitasi.</p>
                                </div>
                                <button @click="previewPost" class="rounded-full border border-coffee-100 px-4 py-2.5 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">
                                    Buka preview
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 xl:col-span-4">
                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Meta & Status</p>

                            <div class="mt-4 space-y-4">
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Kategori</span>
                                    <select v-model="postEditor.category_id" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                        <option value="">Pilih kategori</option>
                                        <option v-for="category in categories.items" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </label>

                                <div class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Tag</span>
                                    <div class="grid grid-cols-1 gap-2 rounded-2xl border border-coffee-100 p-4 dark:border-coffee-800/50">
                                        <label v-for="tag in tags.items" :key="tag.id" class="flex items-center gap-3 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/75">
                                            <input v-model="postEditor.tag_ids" :value="tag.id" type="checkbox" class="size-4 rounded border-coffee-300 text-coffee-700 focus:ring-coffee-300">
                                            {{ tag.name }}
                                        </label>
                                        <p v-if="tags.items.length === 0" class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Belum ada tag.</p>
                                    </div>
                                </div>

                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Status</span>
                                    <select v-model="postEditor.status" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </label>

                                <label class="flex items-center gap-3 rounded-2xl border border-coffee-100 px-4 py-3 text-sm text-neutralwarm-500 dark:border-coffee-800/50 dark:text-neutralwarm-100/70">
                                    <input v-model="postEditor.is_featured" type="checkbox" class="size-4 rounded border-coffee-300 text-coffee-700 focus:ring-coffee-300">
                                    Jadikan featured
                                </label>

                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Meta title</span>
                                    <input v-model="postEditor.meta_title" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50" placeholder="Opsional">
                                </label>

                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Meta description</span>
                                    <textarea v-model="postEditor.meta_description" rows="4" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50" placeholder="Opsional"></textarea>
                                </label>

                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Published at</span>
                                    <input v-model="postEditor.published_at" type="datetime-local" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Featured image</p>

                            <div class="mt-4 space-y-4">
                                <div class="overflow-hidden rounded-3xl border border-dashed border-coffee-200 bg-coffee-50 dark:border-coffee-800/50 dark:bg-white/5">
                                    <img v-if="postEditor.featured_image_url" :src="postEditor.featured_image_url" class="h-56 w-full object-cover" :alt="postEditor.featured_image_alt || postEditor.title || 'Featured image'">
                                    <div v-else class="flex h-56 items-center justify-center px-6 text-center text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">
                                        Belum ada featured image
                                    </div>
                                </div>

                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Upload gambar</span>
                                    <input ref="postFeaturedImage" @change="updatePostFeaturedImage" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 file:mr-4 file:rounded-full file:border-0 file:bg-coffee-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>

                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Alt text</span>
                                    <input v-model="postEditor.featured_image_alt" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50" placeholder="Opsional">
                                </label>

                                <div class="flex flex-wrap gap-2">
                                    <button @click="removePostFeaturedImage" type="button" class="rounded-full border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10">
                                        Hapus gambar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Aksi cepat</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button @click="submitPost('draft')" class="rounded-full border border-coffee-100 px-4 py-2.5 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5" :disabled="postEditor.loading || postEditor.saving">
                                    {{ postEditor.saving ? 'Memproses...' : 'Simpan Draft' }}
                                </button>
                                <button @click="submitPost('published')" class="rounded-full bg-coffee-700 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800 disabled:cursor-not-allowed disabled:opacity-60" :disabled="postEditor.loading || postEditor.saving">
                                    {{ postEditor.saving ? 'Memproses...' : 'Terbitkan' }}
                                </button>
                                <button v-if="postEditor.id && postEditor.status !== 'archived'" @click="archivePost({ id: postEditor.id })" class="rounded-full border border-amber-200 px-4 py-2.5 text-sm font-semibold text-amber-700 transition hover:bg-amber-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-amber-500/30 dark:text-amber-100 dark:hover:bg-amber-500/10" :disabled="postEditor.loading || postEditor.saving">
                                    {{ postEditor.saving ? 'Memproses...' : 'Arsipkan' }}
                                </button>
                                <button v-if="postEditor.id" @click="promptDelete('post', { id: postEditor.id, slug: postEditor.slug || postEditor.id })" class="rounded-full border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10" :disabled="postEditor.loading || postEditor.saving">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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

            <section v-else-if="current === 'tags'" class="space-y-6">
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

            <section v-else-if="current === 'media'" class="space-y-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Semua gambar unggulan dari tulisan terkumpul di sini agar mudah dicek ulang.</p>
                    </div>
                </div>

                <div v-if="media.loading" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <div v-for="n in 6" :key="n" class="h-72 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                </div>

                <div v-else-if="media.items.length === 0" class="rounded-3xl border border-dashed border-coffee-200 bg-white px-6 py-12 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <div class="mx-auto max-w-xl text-center">
                        <p class="text-lg font-semibold text-coffee-900 dark:text-neutralwarm-50">Belum ada media</p>
                        <p class="mt-2 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Gambar unggulan dari tulisan akan muncul di sini setelah artikel menyimpan featured image.</p>
                    </div>
                </div>

                <div v-else class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <article v-for="item in media.items" :key="item.id" class="overflow-hidden rounded-3xl border border-coffee-100 bg-white shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                        <div class="aspect-[16/10] overflow-hidden bg-coffee-50 dark:bg-white/5">
                            <img :src="item.thumbnail_url" :alt="item.featured_image_alt || item.post_title" class="h-full w-full object-cover">
                        </div>
                        <div class="space-y-4 p-5">
                            <div class="space-y-1">
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-coffee-700 dark:text-coffee-100">{{ item.status }}</p>
                                <p class="font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ item.post_title }}</p>
                                <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">{{ item.category_name || 'Tanpa kategori' }}</p>
                            </div>

                            <div class="space-y-2 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">
                                <p><span class="font-medium text-coffee-900 dark:text-neutralwarm-50">Path:</span> {{ item.featured_image }}</p>
                                <p><span class="font-medium text-coffee-900 dark:text-neutralwarm-50">Update:</span> {{ item.updated_at || '-' }}</p>
                                <p><span class="font-medium text-coffee-900 dark:text-neutralwarm-50">Publish:</span> {{ item.published_at || '-' }}</p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <a :href="item.featured_image_url" target="_blank" rel="noreferrer noopener" class="rounded-full border border-coffee-100 px-3 py-2 text-sm font-semibold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:text-coffee-100 dark:hover:bg-white/5">
                                    Buka URL
                                </a>
                                <button @click="openPostEdit({ id: item.post_id })" class="rounded-full bg-coffee-700 px-3 py-2 text-sm font-semibold text-white transition hover:bg-coffee-800">
                                    Buka tulisan
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section v-else-if="current === 'settings'" class="space-y-6">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Pengaturan ini langsung memengaruhi identitas public blog, SEO default, dan tema awal pembaca.</p>
                    <button @click="saveSettings" class="inline-flex items-center gap-2 rounded-full bg-coffee-700 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800" :disabled="settings.loading || settings.saving">
                        {{ settings.saving ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                    </button>
                </div>

                <div v-if="settings.loading" class="grid gap-6 xl:grid-cols-12">
                    <div class="xl:col-span-8 space-y-4">
                        <div class="h-32 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                        <div class="h-48 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                    </div>
                    <div class="xl:col-span-4 space-y-4">
                        <div class="h-64 animate-pulse rounded-3xl border border-coffee-100 bg-white dark:border-coffee-800/40 dark:bg-neutralwarm-900"></div>
                    </div>
                </div>

                <div v-else class="grid gap-6 xl:grid-cols-12">
                    <div class="space-y-6 xl:col-span-8">
                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Identitas situs</p>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <label class="space-y-2 md:col-span-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Nama situs</span>
                                    <input v-model="settings.site_name" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                                <label class="space-y-2 md:col-span-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Tagline</span>
                                    <input v-model="settings.site_tagline" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                                <label class="space-y-2 md:col-span-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Deskripsi situs</span>
                                    <textarea v-model="settings.site_description" rows="3" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50"></textarea>
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Tema default</span>
                                    <select v-model="settings.default_theme" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                        <option value="terang">Terang</option>
                                        <option value="dark_espresso">Dark Espresso</option>
                                    </select>
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Teks footer</span>
                                    <input v-model="settings.footer_note" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Hero homepage</p>
                            <div class="mt-4 grid gap-4">
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Badge</span>
                                    <input v-model="settings.hero_badge" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Judul hero</span>
                                    <input v-model="settings.hero_heading" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Subtitle hero</span>
                                    <textarea v-model="settings.hero_subheading" rows="3" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50"></textarea>
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Teks CTA</span>
                                    <input v-model="settings.hero_cta_text" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">SEO default</p>
                            <div class="mt-4 grid gap-4">
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">SEO title default</span>
                                    <input v-model="settings.default_meta_title" type="text" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">SEO description default</span>
                                    <textarea v-model="settings.default_meta_description" rows="3" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50"></textarea>
                                </label>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Tautan sosial</p>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Instagram</span>
                                    <input v-model="settings.social_links.instagram" type="url" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">X / Twitter</span>
                                    <input v-model="settings.social_links.x" type="url" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">GitHub</span>
                                    <input v-model="settings.social_links.github" type="url" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                                <label class="space-y-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">LinkedIn</span>
                                    <input v-model="settings.social_links.linkedin" type="url" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                                <label class="space-y-2 md:col-span-2">
                                    <span class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">YouTube</span>
                                    <input v-model="settings.social_links.youtube" type="url" class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none transition focus:border-coffee-300 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 xl:col-span-4">
                        <div class="rounded-3xl border border-coffee-100 bg-white p-6 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Aset brand</p>
                            <div class="mt-4 space-y-5">
                                <div class="space-y-3">
                                    <p class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Logo</p>
                                    <div class="flex h-32 items-center justify-center overflow-hidden rounded-3xl border border-dashed border-coffee-200 bg-coffee-50 dark:border-coffee-800/50 dark:bg-white/5">
                                        <img v-if="settings.logo_url" :src="settings.logo_url" alt="Logo situs" class="h-full w-full object-contain p-4">
                                        <p v-else class="px-4 text-center text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Belum ada logo</p>
                                    </div>
                                    <input ref="settings_logo" @change="updateSettingsAsset('logo', $event)" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 file:mr-4 file:rounded-full file:border-0 file:bg-coffee-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                    <button v-if="settings.logo_url" @click="clearSettingsAsset('logo')" class="rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10">
                                        Hapus logo
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <p class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Favicon</p>
                                    <div class="flex h-24 items-center justify-center overflow-hidden rounded-3xl border border-dashed border-coffee-200 bg-coffee-50 dark:border-coffee-800/50 dark:bg-white/5">
                                        <img v-if="settings.favicon_url" :src="settings.favicon_url" alt="Favicon situs" class="size-16 object-contain">
                                        <p v-else class="px-4 text-center text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Belum ada favicon</p>
                                    </div>
                                    <input ref="settings_favicon" @change="updateSettingsAsset('favicon', $event)" type="file" accept=".ico,image/jpeg,image/png,image/webp" class="block w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 file:mr-4 file:rounded-full file:border-0 file:bg-coffee-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                    <button v-if="settings.favicon_url" @click="clearSettingsAsset('favicon')" class="rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10">
                                        Hapus favicon
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <p class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Default Open Graph image</p>
                                    <div class="flex h-40 items-center justify-center overflow-hidden rounded-3xl border border-dashed border-coffee-200 bg-coffee-50 dark:border-coffee-800/50 dark:bg-white/5">
                                        <img v-if="settings.default_og_image_url" :src="settings.default_og_image_url" alt="Default OG image" class="h-full w-full object-cover">
                                        <p v-else class="px-4 text-center text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">Belum ada gambar Open Graph</p>
                                    </div>
                                    <input ref="settings_default_og_image" @change="updateSettingsAsset('default_og_image', $event)" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 file:mr-4 file:rounded-full file:border-0 file:bg-coffee-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50">
                                    <button v-if="settings.default_og_image_url" @click="clearSettingsAsset('default_og_image')" class="rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-100 dark:hover:bg-red-500/10">
                                        Hapus OG image
                                    </button>
                                </div>
                            </div>
                        </div>
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
                        Hapus {{ deleting.kind === 'category' ? 'kategori' : deleting.kind === 'post' ? 'artikel' : 'tag' }} ini?
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-neutralwarm-500 dark:text-neutralwarm-100/70">
                        {{ deleting.kind === 'category'
                            ? 'Kategori yang masih dipakai artikel tidak dapat dihapus.'
                            : deleting.kind === 'post'
                                ? 'Artikel akan dipindahkan ke tempat sampah dan tetap bisa dipulihkan selama belum dihapus permanen.'
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

            <div
                v-if="postEditor.preview.open"
                class="fixed inset-0 z-[85] flex items-center justify-center bg-neutralwarm-950/60 px-4 py-8 backdrop-blur-sm">
                <div class="w-full max-w-5xl rounded-3xl border border-coffee-100 bg-white shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <div class="flex items-center justify-between border-b border-coffee-100 px-6 py-4 dark:border-coffee-800/40">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">Preview aman</p>
                            <h3 class="mt-1 font-lora text-2xl font-semibold text-coffee-900 dark:text-neutralwarm-50">{{ postEditor.preview.title }}</h3>
                        </div>
                        <button @click="closePostPreview" class="rounded-full border border-coffee-100 bg-white px-3 py-2 text-sm font-semibold text-coffee-700 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-coffee-100">
                            Tutup
                        </button>
                    </div>

                    <div class="max-h-[75vh] overflow-y-auto px-6 py-6">
                        <div v-if="postEditor.preview.loading" class="space-y-4">
                            <div class="h-8 animate-pulse rounded-2xl bg-coffee-50 dark:bg-white/5"></div>
                            <div class="h-64 animate-pulse rounded-3xl bg-coffee-50 dark:bg-white/5"></div>
                        </div>

                        <article v-else class="prose prose-neutral max-w-none dark:prose-invert" v-html="postEditor.preview.html"></article>
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
