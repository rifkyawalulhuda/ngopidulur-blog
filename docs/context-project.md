# Context Project

Last updated: 2026-05-12
Repo: `D:\Github\ngopidulur-blog`

## 1. Ringkasan Produk

Ngopi Dulur adalah personal blog CMS berbasis Laravel + Vue dengan positioning:

- Warm Coffee Meets Modern Tech
- Simple product, smart foundation, warm personality

MVP fokus pada:

- admin authentication
- admin dashboard
- CRUD tulisan
- CRUD kategori
- CRUD tag
- Media MVP untuk featured image
- pengaturan blog
- public blog dengan homepage, detail tulisan, kategori, tag, search, sitemap, dan robots

Bahasa interface wajib Indonesia.

## 2. Source of Truth

Baca ini sebelum mengubah fitur:

- [PRD.md](D:/Github/ngopidulur-blog/docs/PRD.md)
- [implementation-plan.md](D:/Github/ngopidulur-blog/docs/implementation-plan.md)
- [milestone-status.md](D:/Github/ngopidulur-blog/docs/milestone-status.md)
- [mvp-checklist.md](D:/Github/ngopidulur-blog/docs/mvp-checklist.md)
- [final-verification-report.md](D:/Github/ngopidulur-blog/docs/final-verification-report.md)

Panduan agent internal:

- [.agents/ngopi-dulur-product.md](D:/Github/ngopidulur-blog/.agents/ngopi-dulur-product.md)
- [.agents/frontend-vue-tailadmin.md](D:/Github/ngopidulur-blog/.agents/frontend-vue-tailadmin.md)
- [.agents/laravel-architecture.md](D:/Github/ngopidulur-blog/.agents/laravel-architecture.md)
- [.agents/rtk-command-policy.md](D:/Github/ngopidulur-blog/.agents/rtk-command-policy.md)

## 3. Stack Aktual

- Laravel 13.7.0
- PHP 8.4
- MySQL
- Vue 3.5.33
- Vite 8
- Tailwind CSS 4.2.4
- Alpine.js 3.14.9
- Pest 5
- Laravel Boost MCP tersedia di repo

## 4. Arsitektur Aplikasi

Aplikasi dibagi menjadi 3 surface:

1. Public blog
- Blade-first
- route publik dirender oleh controller Laravel + Blade views
- komponen interaktif kecil boleh memakai JS/Vue bila perlu

2. Admin UI
- shell admin berbasis Blade
- isi admin utama adalah Vue SPA
- route `/admin/*` selain login masuk ke shell admin

3. Admin API
- JSON API di `/admin/api/*`
- dipakai SPA admin untuk semua operasi data

## 5. Route Surface Penting

Public:

- `GET /`
- `GET /articles`
- `GET /categories`
- `GET /posts/{slug}`
- `GET /category/{category}`
- `GET /tag/{tag}`
- `GET /search`
- `GET /sitemap.xml`
- `GET /robots.txt`

Admin UI:

- `GET /admin/login`
- `GET /admin`
- `GET /admin/dashboard`
- `GET /admin/{any?}`

Admin API:

- `POST /admin/api/login`
- `POST /admin/api/logout`
- `GET /admin/api/dashboard`
- CRUD categories
- CRUD tags
- CRUD posts
- `POST /admin/api/posts/{post}/publish`
- `POST /admin/api/posts/{post}/archive`
- `GET /admin/api/posts/{post}/preview`
- `POST /admin/api/posts/images`
- `GET /admin/api/media`
- `GET|POST /admin/api/profile`
- `GET|PUT /admin/api/settings`

## 6. File Map Penting

Backend controller:

- [app/Http/Controllers/PublicHomeController.php](D:/Github/ngopidulur-blog/app/Http/Controllers/PublicHomeController.php)
- [app/Http/Controllers/PublicPostController.php](D:/Github/ngopidulur-blog/app/Http/Controllers/PublicPostController.php)
- [app/Http/Controllers/PublicCategoryController.php](D:/Github/ngopidulur-blog/app/Http/Controllers/PublicCategoryController.php)
- [app/Http/Controllers/PublicTagController.php](D:/Github/ngopidulur-blog/app/Http/Controllers/PublicTagController.php)
- [app/Http/Controllers/PublicSearchController.php](D:/Github/ngopidulur-blog/app/Http/Controllers/PublicSearchController.php)
- [app/Http/Controllers/PublicSitemapController.php](D:/Github/ngopidulur-blog/app/Http/Controllers/PublicSitemapController.php)
- [app/Http/Controllers/PublicRobotsController.php](D:/Github/ngopidulur-blog/app/Http/Controllers/PublicRobotsController.php)
- [app/Http/Controllers/AdminShellController.php](D:/Github/ngopidulur-blog/app/Http/Controllers/AdminShellController.php)
- [app/Http/Controllers/AdminAuthController.php](D:/Github/ngopidulur-blog/app/Http/Controllers/AdminAuthController.php)
- [app/Http/Controllers/AdminApi](D:/Github/ngopidulur-blog/app/Http/Controllers/AdminApi)

Routing:

- [routes/web.php](D:/Github/ngopidulur-blog/routes/web.php)

Admin SPA:

- [resources/js/admin.js](D:/Github/ngopidulur-blog/resources/js/admin.js)

Public layout/views:

- [resources/views/layouts/public.blade.php](D:/Github/ngopidulur-blog/resources/views/layouts/public.blade.php)
- [resources/views/public/home.blade.php](D:/Github/ngopidulur-blog/resources/views/public/home.blade.php)
- [resources/views/public/articles.blade.php](D:/Github/ngopidulur-blog/resources/views/public/articles.blade.php)
- [resources/views/public/categories.blade.php](D:/Github/ngopidulur-blog/resources/views/public/categories.blade.php)
- [resources/views/public/post.blade.php](D:/Github/ngopidulur-blog/resources/views/public/post.blade.php)
- [resources/views/public/search.blade.php](D:/Github/ngopidulur-blog/resources/views/public/search.blade.php)
- [resources/views/public/partials/search-results.blade.php](D:/Github/ngopidulur-blog/resources/views/public/partials/search-results.blade.php)
- [resources/views/public/partials/search-suggestions.blade.php](D:/Github/ngopidulur-blog/resources/views/public/partials/search-suggestions.blade.php)

Admin shell/layout:

- [resources/views/layouts/app.blade.php](D:/Github/ngopidulur-blog/resources/views/layouts/app.blade.php)
- [resources/views/layouts/sidebar.blade.php](D:/Github/ngopidulur-blog/resources/views/layouts/sidebar.blade.php)
- [resources/views/layouts/app-header.blade.php](D:/Github/ngopidulur-blog/resources/views/layouts/app-header.blade.php)
- [resources/views/layouts/fullscreen-layout.blade.php](D:/Github/ngopidulur-blog/resources/views/layouts/fullscreen-layout.blade.php)
- [resources/views/admin/login.blade.php](D:/Github/ngopidulur-blog/resources/views/admin/login.blade.php)
- [resources/views/admin/dashboard.blade.php](D:/Github/ngopidulur-blog/resources/views/admin/dashboard.blade.php)
- [resources/views/pages/dashboard/ecommerce.blade.php](D:/Github/ngopidulur-blog/resources/views/pages/dashboard/ecommerce.blade.php)
- [resources/views/pages/dashboard/blog-dashboard.blade.php](D:/Github/ngopidulur-blog/resources/views/pages/dashboard/blog-dashboard.blade.php)

Styling/build:

- [resources/css/app.css](D:/Github/ngopidulur-blog/resources/css/app.css)
- [resources/js/app.js](D:/Github/ngopidulur-blog/resources/js/app.js)
- [vite.config.js](D:/Github/ngopidulur-blog/vite.config.js)

## 7. Keputusan Implementasi Saat Ini

### Admin

- shell admin sudah dikembalikan ke basis TailAdmin
- dashboard `/admin` dan `/admin/dashboard` sekarang memakai konteks blog dashboard, tetap di dalam shell/default layout TailAdmin
- endpoint `GET /admin/api/dashboard` menyuplai stats, target bulanan, chart traffic 30 hari, chart kategori, recent posts, top posts, dan aktivitas
- `app/Http/Controllers/AdminApi/DashboardController.php` sudah diberi guard `Schema::hasTable('post_views')` supaya dashboard tidak error bila tabel analytics belum ada
- tabel analytics yang dipakai dashboard adalah `post_views`; migration terkait sudah perlu ada/terdeploy di environment target
- admin SPA lain tetap hidup dari `resources/js/admin.js`
- `resources/js/admin.js` sudah dipecah ke beberapa lazy-loaded module dengan `defineAsyncComponent`, jadi lebih ringan daripada state awal yang sepenuhnya eager
- seluruh warna admin SPA sudah digeser dari palet coffee/custom ke palet TailAdmin gray + brand
- login admin juga sudah diselaraskan ke nuansa TailAdmin light/dark
- halaman admin `Media` sekarang berfungsi sebagai galeri `Gambar`, berisi gambar dari featured image dan konten tulisan dengan judul yang membuka tulisan terkait
- header admin TailAdmin memiliki dropdown `Profile` yang membuka modal pengaturan foto profil, nama, email/username login, dan password login

### Public blog

- public blog tetap Blade-first
- homepage sudah direstyle mengikuti mockup visual user
- "artikel terbaru" sekarang menampilkan seluruh artikel published, termasuk yang featured
- menu `Artikel` sekarang diarahkan ke halaman `/articles`
- halaman `/articles` menampilkan seluruh artikel published dengan filter kategori via query `?category=slug`
- pada desktop, filter kategori di `/articles` tampil sebagai side navbar kiri yang sticky
- grid listing artikel publik sudah dirapikan untuk mobile dan desktop
- halaman detail tulisan memiliki sidebar "artikel terkait" dengan fallback ke tulisan published terbaru bila related by tag/category kosong
- halaman detail tulisan memiliki reading progress bar dan tombol scroll-to-top yang muncul saat pembaca turun ke bawah
- code block dark mode di artikel publik sudah diperbaiki di CSS
- halaman search `/search` sudah mendukung realtime search tanpa submit manual
- query pendek seperti `AI` harus tetap bisa ditemukan; backend search akan fallback ke `LIKE` bila token pendek membuat full-text tidak cocok
- homepage hero search sekarang juga punya realtime suggestion dropdown via AJAX ke endpoint `/search?ajax=1&context=hero`
- dropdown suggestion pada hero search homepage harus tampil di atas kartu hero dan section berikutnya; stacking context form/section sudah disetel untuk mencegah popup ketutup komponen lain
- menu `Kategori` pada navigasi public desktop memiliki dropdown kategori aktif yang punya artikel published; menu mobile menampilkan daftar kategori yang sama dalam accordion default tertutup
- CTA "Lihat semua kategori" di homepage sekarang menuju halaman index kategori `/categories`, bukan terkunci ke satu kategori

## 8. Guardrails Penting

Jangan langgar ini:

- jangan ganti TailAdmin Laravel dengan UI kit lain
- jangan ubah admin menjadi Blade multipage biasa; admin tetap SPA untuk content area
- jangan ubah public menjadi SPA penuh; public tetap Blade-first
- jangan tampilkan draft/archived di surface publik
- jangan render raw markdown/richtext ke publik tanpa sanitasi
- jangan overbuild fitur multi-author pada MVP
- jangan buat full media library di MVP

Wajib jaga ini:

- semua label/error UI admin dan public tetap Bahasa Indonesia
- admin menggunakan route `/admin/api/*` untuk data
- query public harus selalu scoped ke `published`
- dark mode dan light mode admin harus tetap konsisten dengan TailAdmin

## 9. Status Fitur Kritis

Sudah ada:

- auth admin
- dashboard admin
- post list/create/edit flow di SPA admin
- categories
- tags
- media page
- settings page
- homepage public
- article index `/articles` + filter kategori sidebar
- category index `/categories`
- detail tulisan
- category page
- tag page
- search + realtime search page
- realtime hero search suggestion
- reading progress + scroll-to-top di detail tulisan
- sitemap
- robots

Perhatian:

- admin SPA masih memakai satu entry `resources/js/admin.js`, tetapi page-level component utama sudah lazy-loaded
- bila mengerjakan perubahan besar admin, lanjutkan modularisasi per halaman/fitur alih-alih menambah logic besar langsung ke root entry
- jangan refactor besar tanpa alasan karena repo sedang aktif berubah

## 10. Workflow Cepat Untuk Agent Baru

Urutan aman sebelum mulai:

1. baca `docs/PRD.md`
2. baca semua file `.agents/*.md`
3. cek `php artisan route:list`
4. cek `resources/js/admin.js` bila task menyangkut admin SPA
5. cek controller public terkait bila task menyangkut surface publik
6. build ulang dengan `rtk npm run build` setelah ubah frontend
7. clear view cache bila ubah Blade dengan `rtk php artisan view:clear`
8. bila menyentuh dashboard admin, cek juga migration/tabel `post_views`
9. bila menyentuh public search, cek kedua surface: halaman `/search` dan hero search di homepage

Command yang sering relevan:

```powershell
rtk php artisan route:list
rtk php artisan view:clear
rtk npm run build
rtk git diff
rtk php artisan migrate
```

## 11. Area Risiko

- `resources/js/admin.js` masih jadi entry sentral, jadi perubahan bootstrap/import tetap bisa berdampak luas walau page module sudah lazy-loaded
- dark mode admin bergantung pada class body/html di layout admin, bukan hanya class per komponen
- public dan admin memakai arah visual berbeda: public boleh warm editorial, admin harus TailAdmin-like
- host lokal project kadang belum otomatis aktif di Herd; jika visual check gagal, validasi dulu domain/dev server project
- dashboard admin bergantung pada tabel `post_views`; di environment baru wajib sinkronkan migration sebelum menguji dashboard
- public search kini punya dua UI client: halaman `/search` dan suggestion panel di homepage, jadi bug search perlu diuji di keduanya

## 12. Rekomendasi Lanjutan

Kalau agent berikutnya meneruskan pekerjaan admin:

- prioritaskan konsistensi TailAdmin di seluruh halaman SPA
- lanjutkan pemecahan module admin per fitur bila bundle mulai membesar lagi
- tambahkan test untuk visibility draft/archived dan aksi admin penting

Kalau agent berikutnya meneruskan pekerjaan public:

- jaga identitas visual warm coffee
- jangan merusak performa baca
- pertahankan sidebar related posts, progress baca, tombol scroll-to-top, dan styling code block yang sudah dibetulkan
- pastikan perubahan search tidak merusak hasil untuk keyword pendek seperti `AI`
