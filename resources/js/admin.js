import { createApp, defineAsyncComponent } from 'vue';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const lazyComponent = (loader) => defineAsyncComponent({
    loader,
    delay: 0,
});

const BlogDashboardPanel = lazyComponent(() => import('./components/dashboard/BlogDashboardPanel.vue'));
const PostsPage = lazyComponent(() => import('./components/admin/PostsPage.vue'));
const PostEditorPage = lazyComponent(() => import('./components/admin/PostEditorPage.vue'));
const CategoriesPage = lazyComponent(() => import('./components/admin/CategoriesPage.vue'));
const TagsPage = lazyComponent(() => import('./components/admin/TagsPage.vue'));
const MediaPage = lazyComponent(() => import('./components/admin/MediaPage.vue'));
const SettingsPage = lazyComponent(() => import('./components/admin/SettingsPage.vue'));

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
    success: {
        card: 'border-emerald-200/80 bg-white text-gray-900 shadow-theme-sm dark:border-emerald-500/20 dark:bg-gray-900 dark:text-white',
        iconWrap: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-100',
        accent: 'bg-emerald-500',
        title: 'Perubahan tersimpan',
        icon: 'check',
    },
    error: {
        card: 'border-red-200/80 bg-white text-gray-900 shadow-theme-sm dark:border-red-500/20 dark:bg-gray-900 dark:text-white',
        iconWrap: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-100',
        accent: 'bg-red-500',
        title: 'Aksi belum berhasil',
        icon: 'x',
    },
    info: {
        card: 'border-brand-200/80 bg-white text-gray-900 shadow-theme-sm dark:border-brand-500/20 dark:bg-gray-900 dark:text-white',
        iconWrap: 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300',
        accent: 'bg-gray-500',
        title: 'Info singkat',
        icon: 'info',
    },
};

const makeToastId = () => {
    if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
        return crypto.randomUUID();
    }

    return `toast-${Date.now()}-${Math.random().toString(36).slice(2, 10)}`;
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

const adminSpa = createApp({
    components: {
        BlogDashboardPanel,
        PostsPage,
        PostEditorPage,
        CategoriesPage,
        TagsPage,
        MediaPage,
        SettingsPage,
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
            actionConfirm: {
                open: false,
                action: '',
                loading: false,
                title: '',
                message: '',
                confirm_label: '',
                confirm_class: '',
                item: null,
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
                return 'Gambar';
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
                return 'Koleksi gambar dari tulisan, featured image, dan konten artikel dengan tautan ke post terkait.';
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
                menubar: 'edit view insert format table tools help',
                branding: false,
                promotion: false,
                browser_spellcheck: true,
                statusbar: true,
                elementpath: true,
                min_height: 520,
                autoresize_bottom_margin: 20,
                resize: false,
                toolbar_mode: 'sliding',
                toolbar_sticky: true,
                plugins: 'advlist anchor autolink autoresize autosave charmap code codesample directionality fullscreen help image insertdatetime link lists nonbreaking preview quickbars searchreplace table visualblocks visualchars wordcount emoticons',
                toolbar: 'undo redo restoredraft | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | blockquote hr | link anchor image table codesample charmap emoticons | insertdatetime nonbreaking | searchreplace visualblocks visualchars | code fullscreen preview',
                block_formats: 'Paragraf=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Kutipan=blockquote; Preformatted=pre',
                style_formats: [
                    { title: 'Paragraf standar', block: 'p' },
                    { title: 'Lead paragraph', block: 'p', classes: 'lead' },
                    { title: 'Kutipan', block: 'blockquote' },
                    { title: 'Kode inline', inline: 'code' },
                ],
                quickbars_selection_toolbar: 'bold italic underline | blocks blockquote | alignleft aligncenter alignright | bullist numlist | quicklink',
                quickbars_insert_toolbar: 'image table hr codesample',
                quickbars_image_toolbar: 'alignleft aligncenter alignright | imageoptions',
                contextmenu: 'link image table',
                image_title: true,
                image_caption: true,
                image_description: true,
                image_dimensions: true,
                automatic_uploads: true,
                paste_data_images: false,
                file_picker_types: 'image',
                object_resizing: 'img table',
                link_default_protocol: 'https',
                table_default_attributes: {
                    class: 'table table-content',
                },
                table_default_styles: {
                    width: '100%',
                },
                table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
                table_sizing_mode: 'responsive',
                codesample_languages: [
                    { text: 'HTML/XML', value: 'markup' },
                    { text: 'CSS', value: 'css' },
                    { text: 'JavaScript', value: 'javascript' },
                    { text: 'PHP', value: 'php' },
                    { text: 'JSON', value: 'json' },
                    { text: 'Markdown', value: 'markdown' },
                    { text: 'Bash', value: 'bash' },
                ],
                autosave_ask_before_unload: true,
                autosave_interval: '20s',
                autosave_restore_when_empty: true,
                images_upload_handler: async (blobInfo, progress) => {
                    const payload = await this.uploadEditorImage(blobInfo.blob());
                    progress(100);

                    return payload.location;
                },
                file_picker_callback: async (callback, _value, meta) => {
                    if (meta.filetype !== 'image') {
                        return;
                    }

                    const input = document.createElement('input');
                    input.type = 'file';
                    input.accept = '.jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp';

                    input.addEventListener('change', async () => {
                        const [file] = Array.from(input.files || []);

                        if (! file) {
                            return;
                        }

                        try {
                            const payload = await this.uploadEditorImage(file);
                            callback(payload.location, {
                                alt: file.name.replace(/\.[^.]+$/, ''),
                                title: file.name,
                            });
                            this.toast('Gambar editor berhasil ditambahkan.', 'success');
                        } catch (error) {
                            this.toast(error.message || 'Upload gambar editor gagal.', 'error');
                        }
                    });

                    input.click();
                },
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
                    hr {
                        border: 0;
                        border-top: 1px solid ${darkMode ? 'rgba(247, 239, 231, 0.18)' : 'rgba(123, 69, 32, 0.18)'};
                        margin: 2rem 0;
                    }
                    figure {
                        margin: 2rem 0;
                    }
                    figure.image {
                        display: table;
                        margin-left: auto;
                        margin-right: auto;
                    }
                    figure.image img {
                        display: block;
                        max-width: 100%;
                        height: auto;
                        border-radius: 1.25rem;
                    }
                    figcaption {
                        display: table-caption;
                        caption-side: bottom;
                        color: ${darkMode ? '#cfb7a7' : '#7b5a46'};
                        font-size: 0.92rem;
                        margin-top: 0.75rem;
                        text-align: center;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 2rem 0;
                    }
                    th, td {
                        border: 1px solid ${darkMode ? 'rgba(247, 239, 231, 0.16)' : 'rgba(123, 69, 32, 0.15)'};
                        padding: 0.75rem 0.9rem;
                        vertical-align: top;
                    }
                    th {
                        background: ${darkMode ? 'rgba(255,255,255,0.04)' : '#f8efe6'};
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

        async uploadEditorImage(file) {
            const formData = new FormData();
            formData.append('image', file, file.name || 'editor-image.png');

            return this.apiCall('/admin/api/posts/images', {
                method: 'POST',
                body: formData,
            });
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

        syncPostListItem(item) {
            if (! item?.id) {
                return;
            }

            const index = this.posts.items.findIndex((post) => Number(post.id) === Number(item.id));

            if (index === -1) {
                return;
            }

            const current = this.posts.items[index];

            this.posts.items[index] = {
                ...current,
                id: item.id,
                title: item.title ?? current.title,
                slug: item.slug ?? current.slug,
                category_name: item.category?.name ?? current.category_name,
                category_slug: item.category?.slug ?? current.category_slug,
                status: item.status ?? current.status,
                published_at: item.published_at ?? current.published_at,
                updated_at: item.updated_at ?? current.updated_at,
                author_name: item.author?.name ?? current.author_name,
                featured_image: item.featured_image ?? current.featured_image,
                featured_image_url: item.featured_image_url ?? current.featured_image_url,
                tags_count: Array.isArray(item.tags) ? item.tags.length : current.tags_count,
            };
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
                return true;
            } catch (error) {
                this.toast(error.message, 'error');
                return false;
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
                this.syncPostListItem(payload.item);
                this.toast(payload.message || 'Artikel berhasil diterbitkan.', 'success');
                await this.loadPosts();
                return true;
            } catch (error) {
                this.toast(error.message, 'error');
                return false;
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
                this.syncPostListItem(payload.item);
                this.toast(payload.message || 'Artikel berhasil diarsipkan.', 'success');
                await this.loadPosts();
                return true;
            } catch (error) {
                this.toast(error.message, 'error');
                return false;
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

        promptPostAction(action, item = null) {
            if (this.postEditor.loading || this.postEditor.saving) {
                return;
            }

            const currentTitle = this.postEditor.title || item?.title || 'tulisan ini';
            const isDraft = action === 'draft';
            const isPublish = action === 'published';

            this.actionConfirm = {
                open: true,
                action,
                loading: false,
                title: isDraft
                    ? 'Simpan sebagai draft?'
                    : isPublish
                        ? 'Terbitkan tulisan ini?'
                        : 'Arsipkan tulisan ini?',
                message: isDraft
                    ? `Perubahan pada "${currentTitle}" akan disimpan dan statusnya menjadi draft.`
                    : isPublish
                        ? `"${currentTitle}" akan tampil di blog publik setelah diterbitkan.`
                        : `"${currentTitle}" akan disembunyikan dari blog publik dan dipindahkan ke status arsip.`,
                confirm_label: isDraft
                    ? 'Ya, simpan draft'
                    : isPublish
                        ? 'Ya, terbitkan'
                        : 'Ya, arsipkan',
                confirm_class: isDraft
                    ? 'bg-brand-500 hover:bg-brand-600'
                    : isPublish
                        ? 'bg-emerald-600 hover:bg-emerald-700'
                        : 'bg-amber-500 text-white hover:bg-amber-400',
                item,
            };
        },

        closeActionConfirm() {
            this.actionConfirm = {
                open: false,
                action: '',
                loading: false,
                title: '',
                message: '',
                confirm_label: '',
                confirm_class: '',
                item: null,
            };
        },

        async confirmPostAction() {
            if (! this.actionConfirm.action) {
                return;
            }

            this.actionConfirm.loading = true;
            let success = false;

            if (this.actionConfirm.action === 'draft') {
                success = await this.submitPost('draft');
            } else if (this.actionConfirm.action === 'published') {
                if (this.actionConfirm.item?.id && this.current === 'posts') {
                    success = await this.publishPost(this.actionConfirm.item);
                } else {
                    success = await this.submitPost('published');
                }
            } else if (this.actionConfirm.action === 'archived' && this.actionConfirm.item?.id) {
                success = await this.archivePost(this.actionConfirm.item);
            }

            if (success) {
                this.closeActionConfirm();
            } else {
                this.actionConfirm.loading = false;
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
            const id = makeToastId();
            const config = this.toastTypes[type] || this.toastTypes.info;

            this.toasts.push({
                id,
                message,
                type,
                title: config.title,
            });

            window.setTimeout(() => {
                this.removeToast(id);
            }, 3500);
        },

        removeToast(id) {
            this.toasts = this.toasts.filter((toast) => toast.id !== id);
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
            <section class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900 md:px-6">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-500">
                            TailAdmin Workspace
                        </p>
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white md:text-3xl">
                            {{ pageTitle }}
                        </h2>
                        <p class="max-w-2xl text-sm leading-6 text-gray-500 dark:text-gray-400">
                            {{ pageSubtitle }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            @click="navigate('/admin/dashboard')"
                            class="inline-flex rounded-lg border px-4 py-2 text-sm font-medium transition"
                            :class="current === 'dashboard' ? 'border-brand-500 bg-brand-500 text-white shadow-theme-xs' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white'">
                            Dashboard
                        </button>
                        <button
                            @click="navigate('/admin/posts')"
                            class="inline-flex rounded-lg border px-4 py-2 text-sm font-medium transition"
                            :class="current === 'posts' || current === 'post-create' || current === 'post-edit' ? 'border-brand-500 bg-brand-500 text-white shadow-theme-xs' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white'">
                            Tulisan
                        </button>
                        <button
                            @click="navigate('/admin/categories')"
                            class="inline-flex rounded-lg border px-4 py-2 text-sm font-medium transition"
                            :class="current === 'categories' ? 'border-brand-500 bg-brand-500 text-white shadow-theme-xs' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white'">
                            Kategori
                        </button>
                        <button
                            @click="navigate('/admin/tags')"
                            class="inline-flex rounded-lg border px-4 py-2 text-sm font-medium transition"
                            :class="current === 'tags' ? 'border-brand-500 bg-brand-500 text-white shadow-theme-xs' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white'">
                            Tag
                        </button>
                        <button
                            @click="navigate('/admin/media')"
                            class="inline-flex rounded-lg border px-4 py-2 text-sm font-medium transition"
                            :class="current === 'media' ? 'border-brand-500 bg-brand-500 text-white shadow-theme-xs' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white'">
                            Media
                        </button>
                        <button
                            @click="navigate('/admin/settings')"
                            class="inline-flex rounded-lg border px-4 py-2 text-sm font-medium transition"
                            :class="current === 'settings' ? 'border-brand-500 bg-brand-500 text-white shadow-theme-xs' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white'">
                            Pengaturan
                        </button>
                    </div>
                </div>
            </section>

            <section v-if="current === 'dashboard'" class="space-y-6">
                <blog-dashboard-panel endpoint="/admin/api/dashboard" />
            </section>

            <posts-page
                v-else-if="current === 'posts'"
                :posts="posts"
                :categories="categories"
                @open-create="openPostCreate"
                @load-posts="loadPosts"
                @reset-filters="changePostFilters({ search: '', status: '', category: '', sort: 'updated_at' })"
                @preview-post="previewPostById"
                @open-edit="openPostEdit"
                @prompt-action="promptPostAction"
                @prompt-delete="promptDelete" />

            <post-editor-page
                v-else-if="current === 'post-create' || current === 'post-edit'"
                :post-editor="postEditor"
                :categories="categories"
                :tags="tags"
                :tiny-mce-theme="tinyMceTheme"
                :tiny-mce-mount-key="tinyMceMountKey"
                :tiny-mce-init="tinyMceInit"
                @navigate-posts="navigate('/admin/posts')"
                @preview-post="previewPost"
                @prompt-action="promptPostAction"
                @set-content-format="setPostContentFormat"
                @update-featured-image="updatePostFeaturedImage"
                @remove-featured-image="removePostFeaturedImage"
                @prompt-delete="promptDelete" />

            <categories-page
                v-else-if="current === 'categories'"
                :categories="categories"
                @open-create="openCategoryCreate"
                @open-edit="openCategoryEdit"
                @prompt-delete="promptDelete" />

            <tags-page
                v-else-if="current === 'tags'"
                :tags="tags"
                @open-create="openTagCreate"
                @open-edit="openTagEdit"
                @prompt-delete="promptDelete" />

            <media-page
                v-else-if="current === 'media'"
                :media="media"
                @open-post-edit="openPostEdit" />

            <settings-page
                v-else-if="current === 'settings'"
                :settings="settings"
                @save-settings="saveSettings"
                @update-asset="updateSettingsAsset"
                @clear-asset="clearSettingsAsset" />

            <div
                v-if="editor.open"
                class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-900/50 px-4 py-8 backdrop-blur-sm">
                <div class="w-full max-w-2xl rounded-3xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">
                                {{ editor.kind === 'category' ? 'Kategori' : 'Tag' }}
                            </p>
                            <h3 class="mt-1 font-lora text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ editor.mode === 'create' ? 'Tambah' : 'Ubah' }} {{ editor.kind === 'category' ? 'kategori' : 'tag' }}
                            </h3>
                        </div>
                        <button @click="closeEditor" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            Tutup
                        </button>
                    </div>

                    <div class="space-y-4 px-6 py-6">
                        <div v-if="editor.loading" class="space-y-4">
                            <div class="h-11 animate-pulse rounded-2xl bg-gray-50 dark:bg-white/5"></div>
                            <div class="h-11 animate-pulse rounded-2xl bg-gray-50 dark:bg-white/5"></div>
                            <div v-if="editor.kind === 'category'" class="h-24 animate-pulse rounded-2xl bg-gray-50 dark:bg-white/5"></div>
                        </div>

                        <template v-else>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Nama</label>
                                <input v-model="editor.name" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" placeholder="Contoh: Catatan Harian">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Slug</label>
                                <input v-model="editor.slug" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" placeholder="otomatis-dari-nama">
                                <p class="text-xs text-gray-500 dark:text-gray-500">Boleh dikosongkan agar otomatis dibuat dari nama.</p>
                            </div>

                            <div v-if="editor.kind === 'category'" class="space-y-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Deskripsi</label>
                                <textarea v-model="editor.description" rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" placeholder="Deskripsi singkat kategori"></textarea>
                            </div>

                            <label v-if="editor.kind === 'category'" class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
                                <input v-model="editor.is_active" type="checkbox" class="size-4 rounded border-gray-300 text-brand-500 focus:ring-brand-300">
                                Kategori aktif
                            </label>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button @click="closeEditor" type="button" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">
                                    Batal
                                </button>
                                <button @click="saveEditor" type="button" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600" :disabled="editor.loading">
                                    {{ editor.loading ? 'Menyimpan...' : 'Simpan' }}
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div
                v-if="actionConfirm.open"
                class="fixed inset-0 z-[79] flex items-center justify-center bg-gray-900/45 px-4 py-8 backdrop-blur-sm">
                <div class="w-full max-w-lg rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Konfirmasi aksi</p>
                    <h3 class="mt-2 font-lora text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ actionConfirm.title }}
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400">
                        {{ actionConfirm.message }}
                    </p>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button @click="closeActionConfirm" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white" :disabled="actionConfirm.loading">
                            Batal
                        </button>
                        <button @click="confirmPostAction" class="rounded-full px-4 py-2.5 text-sm font-semibold text-white transition disabled:cursor-not-allowed disabled:opacity-60" :class="actionConfirm.confirm_class" :disabled="actionConfirm.loading">
                            {{ actionConfirm.loading ? 'Memproses...' : actionConfirm.confirm_label }}
                        </button>
                    </div>
                </div>
            </div>

            <div
                v-if="deleting.open"
                class="fixed inset-0 z-[80] flex items-center justify-center bg-gray-900/50 px-4 py-8 backdrop-blur-sm">
                <div class="w-full max-w-lg rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Konfirmasi hapus</p>
                    <h3 class="mt-2 font-lora text-2xl font-semibold text-gray-900 dark:text-white">
                        Hapus {{ deleting.kind === 'category' ? 'kategori' : deleting.kind === 'post' ? 'artikel' : 'tag' }} ini?
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400">
                        {{ deleting.kind === 'category'
                            ? 'Kategori yang masih dipakai artikel tidak dapat dihapus.'
                            : deleting.kind === 'post'
                                ? 'Artikel akan dipindahkan ke tempat sampah dan tetap bisa dipulihkan selama belum dihapus permanen.'
                                : 'Tag akan dihapus dan relasi pada artikel dibersihkan otomatis.' }}
                    </p>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button @click="closeDelete" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white">
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
                class="fixed inset-0 z-[85] flex items-center justify-center bg-gray-900/60 px-4 py-8 backdrop-blur-sm">
                <div class="w-full max-w-5xl rounded-3xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-500 dark:text-brand-300">Preview aman</p>
                            <h3 class="mt-1 font-lora text-2xl font-semibold text-gray-900 dark:text-white">{{ postEditor.preview.title }}</h3>
                        </div>
                        <button @click="closePostPreview" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            Tutup
                        </button>
                    </div>

                    <div class="max-h-[75vh] overflow-y-auto px-6 py-6">
                        <div v-if="postEditor.preview.loading" class="space-y-4">
                            <div class="h-8 animate-pulse rounded-2xl bg-gray-50 dark:bg-white/5"></div>
                            <div class="h-64 animate-pulse rounded-3xl bg-gray-50 dark:bg-white/5"></div>
                        </div>

                        <article v-else class="prose prose-neutral max-w-none dark:prose-invert" v-html="postEditor.preview.html"></article>
                    </div>
                </div>
            </div>

            <div class="pointer-events-none fixed right-4 top-4 z-[90] flex w-full max-w-sm flex-col gap-3 sm:right-6 sm:top-6">
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="pointer-events-auto relative overflow-hidden rounded-2xl border p-4 transition duration-200 ease-out"
                    :class="(toastTypes[toast.type] || toastTypes.info).card">
                    <div class="absolute inset-y-0 left-0 w-1.5 rounded-l-2xl" :class="(toastTypes[toast.type] || toastTypes.info).accent"></div>

                    <div class="flex items-start gap-3 pl-2">
                        <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl" :class="(toastTypes[toast.type] || toastTypes.info).iconWrap">
                            <svg v-if="(toastTypes[toast.type] || toastTypes.info).icon === 'check'" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-8.25 8.32a1 1 0 0 1-1.42 0l-3.75-3.783a1 1 0 1 1 1.42-1.408l3.04 3.066 7.54-7.604a1 1 0 0 1 1.414-.005Z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else-if="(toastTypes[toast.type] || toastTypes.info).icon === 'x'" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 0 1 1.414 0L10 8.586l4.293-4.293a1 1 0 1 1 1.414 1.414L11.414 10l4.293 4.293a1 1 0 0 1-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 0 1-1.414-1.414L8.586 10 4.293 5.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-7-4a1 1 0 1 0-2 0 1 1 0 0 0 2 0ZM9 9a1 1 0 0 0 0 2v3a1 1 0 1 0 2 0v-3a1 1 0 1 0-2 0Z" clip-rule="evenodd" />
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ toast.title }}</p>
                                    <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">{{ toast.message }}</p>
                                </div>

                                <button @click="removeToast(toast.id)" type="button" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">
                                    <span class="sr-only">Tutup notifikasi</span>
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 0 1 1.414 0L10 8.586l4.293-4.293a1 1 0 1 1 1.414 1.414L11.414 10l4.293 4.293a1 1 0 0 1-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 0 1-1.414-1.414L8.586 10 4.293 5.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
});

const adminRoot = document.getElementById('ngopi-dulur-admin-app');

if (adminRoot) {
    adminSpa.mount(adminRoot);
}

const dashboardRoot = document.getElementById('ngopi-dulur-blog-dashboard');

if (dashboardRoot) {
    createApp({
        components: {
            BlogDashboardPanel,
        },
        data() {
            return {
                endpoint: dashboardRoot.dataset.endpoint || '/admin/api/dashboard',
            };
        },
        template: '<blog-dashboard-panel :endpoint="endpoint" />',
    }).mount(dashboardRoot);
}
