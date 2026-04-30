# MVP Checklist - Ngopi Dulur

Last updated: 2026-04-30

## Scope

Checklist ini merangkum acceptance MVP yang sudah diverifikasi lewat test otomatis dan audit kode ringan pada Milestone 07. Fokusnya adalah kualitas perilaku, hardening, performa dasar, dan polish tanpa menambah fitur baru di luar PRD.

## Test Coverage

### Auth

- [x] Admin valid bisa login
- [x] Kredensial salah tidak bisa login
- [x] Dashboard admin terlindungi
- [x] Logout menghapus session

Covered by:
- `tests/Feature/AdminAuthTest.php`
- `tests/Feature/MvpHardeningTest.php`

### Post Visibility

- [x] Published post tampil di homepage
- [x] Draft post tidak tampil di homepage
- [x] Archived post tidak tampil di homepage
- [x] Draft post tidak bisa dibuka via `/posts/{slug}`
- [x] Archived post tidak bisa dibuka via `/posts/{slug}`
- [x] Draft/archived tidak tampil di category page
- [x] Draft/archived tidak tampil di tag page
- [x] Draft/archived tidak tampil di search
- [x] Draft/archived tidak tampil di related posts
- [x] Draft/archived tidak tampil di sitemap

Covered by:
- `tests/Feature/PublicBlogTest.php`
- `tests/Feature/AdminSettingsSeoTest.php`
- `tests/Feature/MvpHardeningTest.php`

### Post Validation

- [x] Category wajib
- [x] Featured image wajib saat publish
- [x] Featured image opsional untuk draft
- [x] Slug unik
- [x] `published_at` otomatis diisi saat publish bila kosong
- [x] `content_format` wajib dan valid
- [x] `rendered_content` dihasilkan dengan sanitasi aman

Covered by:
- `tests/Feature/AdminPostApiTest.php`
- `tests/Feature/MvpHardeningTest.php`

### Upload

- [x] Valid image berhasil
- [x] Non-image ditolak
- [x] Oversized image ditolak
- [x] WebP conversion berhasil atau gagal dengan respons aman

Covered by:
- `tests/Feature/AdminPostApiTest.php`
- `tests/Feature/MvpHardeningTest.php`

### Category and Tag

- [x] Category tidak bisa dihapus jika masih dipakai post
- [x] Tag bisa dihapus dan pivot ikut bersih
- [x] Post tetap valid setelah tag dihapus

Covered by:
- `tests/Feature/AdminCatalogApiTest.php`
- `tests/Feature/MvpHardeningTest.php`

### SEO

- [x] Sitemap mengecualikan draft/archived
- [x] `robots.txt` tersedia
- [x] Preview dan search mengikuti noindex policy

Covered by:
- `tests/Feature/AdminPostApiTest.php`
- `tests/Feature/AdminSettingsSeoTest.php`

## Hardening Audit

### Security

- [x] CSRF tetap aktif pada surface web admin
  - Audit: route admin tetap memakai middleware web default Laravel, form logout memakai `@csrf`, dan tidak ada pengecualian CSRF custom di bootstrap.
- [x] Admin routes protected
  - Audit + test: group `/admin` dan `/admin/api/*` dibungkus middleware `auth`.
- [x] Mass assignment controlled
  - Audit + test: `User`, `Post`, `Category`, `Tag`, dan `SiteSetting` memakai `$fillable` eksplisit.
- [x] Upload MIME dan size validation aktif
  - Audit + test: `app/Http/Requests/Admin/PostRequest.php` dan `app/Http/Requests/Admin/SettingsUpdateRequest.php`.
- [x] Rendered content sanitized
  - Audit + test: `app/Services/PostPublishingService.php`.

### Public Query Safety

- [x] Public queries memakai `published` scope
  - Audit: `PublicHomeController`, `PublicPostController`, `PublicCategoryController`, `PublicTagController`, `PublicSearchController`, dan `PublicSitemapController` semua memfilter published-only.
- [x] Public draft access tidak terbuka
  - Test: visibility suite memastikan draft/archived tidak bocor ke detail, list, search, related, maupun sitemap.

### Performance

- [x] Pagination diterapkan
  - Audit + test: public list memakai `paginate(9)`, admin posts API memakai `paginate(10)`.
- [x] No N+1 obvious issues
  - Audit: public dan admin post queries memakai eager loading `withPublicRelations()` atau `with([...])`.
- [x] Sitemap query dipoles
  - Audit: `PublicSitemapController` sekarang memakai `withMax(...)` untuk last modified kategori/tag, jadi tidak lagi menembak query per item.

### TailAdmin Non-Generic Audit

- [x] TailAdmin tetap jadi base admin UI
- [x] Sidebar memakai palette espresso/coffeewarm, bukan default generik
- [x] Header, brand text, dan menu memakai identitas Ngopi Dulur
- [x] CTA dan state utama konsisten dengan coffee/carameled admin theme

Audit references:
- `resources/views/layouts/sidebar.blade.php`
- `resources/views/layouts/app-header.blade.php`
- `resources/js/admin.js`
- `resources/css/app.css`

## Residual Notes

- Test suite masih memunculkan warning pembacaan `.env` di environment checkout ini, tetapi seluruh suite lulus.
- Workspace lokal masih memakai SQLite untuk test/dev, sementara full-text production path tetap disiapkan untuk MySQL/MariaDB.
- Warning chunk besar TailAdmin saat build sudah dibersihkan lewat lazy-loading dan cleanup entry shell; build kini lulus tanpa warning chunk >500 kB.
