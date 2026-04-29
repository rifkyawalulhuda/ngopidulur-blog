# PRD — Ngopi Dulur Personal Blog CMS

> **Versi revisi:** PRD ini telah diperbarui dengan keputusan tambahan terkait dua tema MVP, penyimpanan konten Rich Text/Markdown, pemisahan Admin SPA dan Admin API, secure preview, Media MVP, testing wajib, image handling dengan automatic WebP conversion, pagination default, robots/noindex policy, error pages, slug collision, featured article, aturan delete category/tag, audit TailAdmin, dan Development Agent Requirements.

## 1. TL;DR

**Ngopi Dulur** adalah website blog pribadi bergaya WordPress sederhana yang memungkinkan pemilik blog mengelola artikel dari dashboard admin.

Pada versi MVP, Ngopi Dulur fokus sebagai **personal blog** dengan satu pemilik/admin utama. Admin dapat membuat, mengedit, menyimpan draft, mempublikasikan, mengarsipkan, dan menghapus artikel.

Namun secara arsitektur, sistem akan disiapkan agar di masa depan dapat berkembang menjadi **CMS untuk banyak penulis**, dengan dukungan user, role, author ownership, dan editorial workflow.

Stack yang digunakan:

- Laravel 13
- Eloquent ORM
- MySQL
- Vue 3
- Vite 8
- Tailwind CSS / TailAdmin-style admin layout

Arah visual produk:

> **Warm Coffee Meets Modern Tech**

Ngopi Dulur harus terasa hangat seperti warung kopi, tetapi tetap bersih, cepat, dan modern seperti produk teknologi.

Prinsip produk:

> Mulai sederhana sebagai blog pribadi, tapi jangan mengunci sistem hanya untuk satu penulis.

---

## 2. Problem Statement

Pemilik blog membutuhkan sistem publishing pribadi yang lebih ringan dan terkontrol dibandingkan CMS besar seperti WordPress. Sistem harus memungkinkan admin menulis dan mengelola artikel tanpa kompleksitas plugin, tema berat, atau dashboard yang terlalu ramai.

Ngopi Dulur harus menyelesaikan kebutuhan utama: menulis, mengelola, dan mempublikasikan artikel dengan cepat. Namun, ada kemungkinan produk ini berkembang menjadi platform konten dengan banyak penulis di masa depan. Karena itu, struktur database, model, dan arsitektur perlu disiapkan sejak awal agar ekspansi ke multi-author CMS tidak membutuhkan rewrite besar.

Masalah utama yang ingin diselesaikan:

1. Admin butuh dashboard sederhana untuk mengelola artikel.
2. Artikel harus mudah dibuat, diedit, dan dipublikasikan.
3. Website publik harus cepat, rapi, responsif, dan nyaman dibaca.
4. Sistem harus punya fondasi teknis yang siap dikembangkan menjadi CMS multi-penulis.
5. MVP tidak boleh terlalu kompleks hanya karena ada rencana ekspansi di masa depan.

---

## 3. Product Positioning

Ngopi Dulur adalah:

- Personal blog CMS.
- Ringan dan fokus pada publishing.
- Dibangun custom dengan Laravel dan Vue.
- Lebih sederhana dari WordPress.
- Memiliki identitas visual hangat seperti warung kopi.
- Tetap modern, clean, dan nyaman digunakan.
- Siap berkembang menjadi CMS multi-author.

Ngopi Dulur bukan:

- Clone penuh WordPress.
- Page builder drag-and-drop.
- Platform komunitas besar sejak MVP.
- Sistem editorial kompleks sejak awal.
- Marketplace tema/plugin.
- CMS dengan puluhan menu yang membingungkan.

---

## 4. Goals

### 4.1 Business Goals

- Membuat blog pribadi yang bisa dikelola mandiri melalui dashboard admin.
- Mengurangi ketergantungan pada WordPress atau CMS eksternal.
- Membangun fondasi teknis yang siap dikembangkan.
- Mempercepat proses menulis dan mempublikasikan artikel.
- Menciptakan website blog yang ringan, bersih, hangat, dan mudah dirawat.
- Membangun identitas brand Ngopi Dulur sebagai ruang baca personal yang akrab dan modern.

### 4.2 User Goals

Sebagai pemilik blog, saya ingin:

- Login ke dashboard admin.
- Membuat artikel baru.
- Menyimpan artikel sebagai draft.
- Mempublikasikan artikel.
- Mengedit artikel yang sudah ada.
- Mengarsipkan artikel agar tidak tampil publik.
- Menghapus artikel yang tidak diperlukan.
- Mengatur kategori dan tag.
- Mengunggah featured image.
- Melihat preview artikel sebelum publish.
- Mengatur SEO dasar untuk setiap artikel.
- Mengelola blog melalui UI yang bersih dan tidak membingungkan.

Sebagai pembaca, saya ingin:

- Melihat artikel terbaru.
- Membaca artikel dengan tampilan nyaman.
- Menjelajahi artikel berdasarkan kategori atau tag.
- Mencari artikel.
- Membuka artikel dengan URL yang rapi dan mudah dibagikan.
- Merasakan nuansa blog yang hangat, personal, dan enak dibaca sambil santai.

### 4.3 Future Goals

Di masa depan, Ngopi Dulur dapat dikembangkan menjadi CMS multi-penulis dengan kemampuan:

- Banyak user.
- Role admin, editor, writer.
- Artikel memiliki author berbeda.
- Writer bisa submit artikel.
- Editor bisa review dan publish.
- Author profile page.
- Activity log.
- Editorial feedback.
- Revision history.

Namun semua itu **bukan scope MVP**.

---

## 5. Non-Goals untuk MVP

Hal-hal berikut tidak masuk MVP:

- Multi-author workflow aktif.
- Role editor/writer aktif.
- Approval artikel.
- Inline editorial comments.
- Revision history.
- Plugin system.
- Page builder.
- Komentar pembaca.
- Newsletter.
- Membership.
- Payment.
- Multi-site.
- Theme marketplace.
- AI writing assistant.

Opini produk: jangan bangun fitur masa depan terlalu dini. Yang perlu disiapkan adalah **struktur teknisnya**, bukan seluruh UI dan workflow-nya.

---

## 6. Users & Roles

### 6.1 MVP Role

#### Admin / Blog Owner

Admin adalah pemilik blog pribadi.

Permissions:

- Login ke dashboard.
- Mengelola semua artikel.
- Mengelola kategori.
- Mengelola tag.
- Mengunggah gambar.
- Mengatur metadata SEO.
- Publish dan archive artikel.
- Mengubah pengaturan dasar website.

Pada MVP, hanya ada satu tipe user aktif: **Admin**.

### 6.2 Future Roles

Sistem disiapkan agar nanti bisa mendukung role tambahan.

#### Super Admin

Pemilik sistem tertinggi.

Future permissions:

- Mengelola semua user.
- Mengelola semua post.
- Mengatur role.
- Mengelola website settings.

#### Editor

Future permissions:

- Melihat semua artikel.
- Mengedit artikel writer.
- Memberi feedback.
- Approve dan publish artikel.

#### Writer

Future permissions:

- Membuat artikel sendiri.
- Menyimpan draft.
- Submit artikel untuk review.
- Melihat feedback editor.

MVP tidak perlu menampilkan role-role ini di UI, tapi database user sebaiknya sudah memiliki field `role`.

---

## 7. MVP Scope

MVP mencakup:

1. Admin authentication.
2. Admin dashboard.
3. CRUD post.
4. CRUD kategori.
5. CRUD tag.
6. Featured image upload.
7. Status artikel: draft, published, archived.
8. Auto slug dari title.
9. Public homepage.
10. Public post detail page.
11. Public category page.
12. Public tag page.
13. Full-text search artikel.
14. Basic SEO metadata.
15. Sitemap XML.
16. Admin settings untuk mengubah nama blog, tagline, logo, favicon, hero copy, footer, social links, default SEO, dan default theme.
17. Public blog memiliki 2 tema: terang dan dark espresso.
18. Content editor mendukung Rich Text dan Markdown.
19. Kategori wajib untuk setiap artikel.
20. Featured image wajib untuk artikel published.
21. Responsive public layout.
22. Responsive admin layout.
23. Public pages dibuat dengan Blade; komponen interaktif dapat menggunakan Vue-rendered components.
24. Dashboard admin dibuat sebagai full Vue SPA.
25. Bahasa interface full Indonesia.
26. Brand visual warm coffee + modern tech.
27. Struktur database siap multi-author di masa depan.

---

## 8. Functional Requirements

### 8.1 Admin Authentication

Admin dapat login ke dashboard.

Routes:

```txt
GET  /admin/login
POST /admin/login
POST /admin/logout
```

Requirements:

- Login menggunakan email dan password.
- Password disimpan menggunakan hash.
- Admin route dilindungi middleware auth.
- Setelah login sukses, admin diarahkan ke dashboard.
- Setelah logout, admin kembali ke login page.
- Credential salah menampilkan error yang jelas.

Acceptance criteria:

- Admin valid bisa login.
- Credential salah menampilkan error.
- User yang belum login tidak bisa membuka dashboard.
- Logout menghapus session.

---

### 8.2 Admin Dashboard

Dashboard menjadi pusat kontrol blog.

URL:

```txt
/admin/dashboard
```

Dashboard menampilkan:

- Total posts.
- Total published posts.
- Total draft posts.
- Total archived posts.
- Total categories.
- Total tags.
- Latest posts.
- Tombol cepat `Tulis Artikel Baru`.

Sidebar menu:

```txt
Dashboard
Tulisan
Kategori
Tag
Media
Pengaturan
Logout
```

Dashboard greeting:

```txt
Selamat datang kembali ☕
Mau nulis apa hari ini?
```

Acceptance criteria:

- Dashboard hanya bisa diakses admin login.
- Data statistik sesuai database.
- Sidebar tampil rapi dengan TailAdmin-style layout.
- Tombol create post mudah ditemukan.
- Dashboard terasa modern, tapi tetap memiliki aksen brand Ngopi Dulur.

---

### 8.3 Post Management

Admin dapat mengelola artikel.

URL:

```txt
/admin/posts
```

Table columns:

- Judul.
- Slug.
- Kategori.
- Status.
- Tanggal Publish.
- Update Terakhir.
- Aksi.

Actions:

- Preview.
- Edit.
- Terbitkan.
- Arsipkan.
- Hapus.

Filters:

- Search by title.
- Filter by status.
- Filter by category.
- Sort by latest updated.
- Sort by published date.

Acceptance criteria:

- Admin bisa melihat daftar artikel.
- Admin bisa mencari artikel.
- Admin bisa filter artikel berdasarkan status.
- Admin bisa membuka edit page.
- Delete membutuhkan confirmation modal.
- Draft dan archived tidak tampil di public blog.

---

### 8.4 Create/Edit Post

URL:

```txt
/admin/posts/create
/admin/posts/{id}/edit
```

Fields:

- Title.
- Slug.
- Excerpt.
- Content.
- Featured Image — wajib untuk artikel published.
- Category — wajib.
- Tags.
- Status.
- Meta Title.
- Meta Description.
- Published At.

Post status untuk MVP:

```txt
draft
published
archived
```

Behavior:

- Slug otomatis dibuat dari title.
- Slug bisa diedit manual.
- Slug harus unique.
- Artikel draft tidak tampil publik.
- Artikel archived tidak tampil publik.
- Artikel published tampil di halaman publik.
- Jika publish dan `published_at` kosong, sistem mengisi otomatis.
- Admin bisa preview artikel sebelum publish.

Slug collision rules:

- Title `Cerita Kopi Pagi` menghasilkan slug `cerita-kopi-pagi`.
- Jika slug sudah dipakai, sistem otomatis menambahkan suffix angka:
  - `cerita-kopi-pagi-2`
  - `cerita-kopi-pagi-3`
  - dan seterusnya.
- Jika admin mengedit slug manual, sistem tetap memvalidasi uniqueness.
- Saat edit post, slug milik post yang sedang diedit tidak dianggap konflik.
- Slug tidak boleh berubah otomatis dari title jika admin sudah pernah mengedit slug manual.

Primary actions:

- `Simpan Draft`
- `Preview`
- `Terbitkan`

Acceptance criteria:

- Admin bisa membuat artikel baru.
- Admin bisa menyimpan draft.
- Admin bisa publish artikel.
- Admin bisa edit artikel published.
- Admin bisa archive artikel.
- Admin bisa delete artikel.
- Slug unik.
- Validasi error tampil jelas.


### 8.4.1 Secure Preview Policy

Preview artikel wajib aman karena draft dan archived tidak boleh bocor ke public blog.

Requirements:

- Preview hanya boleh diakses oleh admin yang sedang login.
- Alternatif aman: gunakan signed temporary URL dengan expiry singkat jika preview perlu dibuka di tab baru.
- Preview draft dan archived tidak boleh dapat diakses melalui `/posts/{slug}`.
- Preview route tidak boleh masuk sitemap.
- Preview page wajib memakai `noindex, nofollow`.
- Preview route tidak boleh muncul di public search, related posts, category page, tag page, atau homepage.
- Preview harus tetap memakai layout public agar admin dapat melihat tampilan mendekati hasil publish.

Acceptance criteria:

- Admin login dapat membuka preview draft.
- User publik tidak bisa membuka preview tanpa izin.
- Draft/archived tetap 404 di public route.
- Preview tidak masuk sitemap.
- Preview memiliki noindex/nofollow.

UX opinion: pisahkan tombol `Simpan Draft` dan `Terbitkan`. Jangan hanya mengandalkan dropdown status. Ini lebih jelas dan mengurangi kesalahan.

---

### 8.5 Content Editor

Content editor harus mendukung dua mode penulisan:

1. **Rich Text Editor** untuk pengalaman menulis visual seperti CMS modern.
2. **Markdown Editor** untuk admin yang lebih nyaman menulis dengan syntax markdown.

Admin dapat memilih mode editor saat menulis artikel. Sistem harus menyimpan konten dengan format yang konsisten agar aman dirender di halaman publik.

Minimum capabilities Rich Text:

- Heading.
- Paragraph.
- Bold.
- Italic.
- Link.
- Quote.
- Ordered list.
- Unordered list.
- Image optional di dalam konten.
- Code block optional.

Minimum capabilities Markdown:

- Markdown input area.
- Preview hasil markdown.
- Heading, list, link, image, quote, code block.
- Sanitasi HTML hasil render.

Recommendation:

Gunakan dua mode yang jelas: `Mode Visual` dan `Mode Markdown`. Jangan membuat editor terasa berat. Yang paling penting adalah stabil, aman, dan nyaman untuk menulis panjang.

### 8.5.1 Content Storage & Rendering Policy

Karena content editor mendukung **Rich Text** dan **Markdown**, penyimpanan konten harus eksplisit agar aman dan konsisten.

Field yang digunakan pada `posts`:

```txt
content_format
content
rendered_content
reading_time_minutes
```

Rules:

- `content_format` berisi `richtext` atau `markdown`.
- `content` menyimpan source content asli:
  - Rich Text: HTML/editor output yang sudah divalidasi.
  - Markdown: markdown source mentah.
- `rendered_content` menyimpan HTML hasil render yang sudah disanitasi.
- Public page hanya boleh merender `rendered_content`.
- Markdown dan Rich Text tidak boleh langsung dirender ke public tanpa sanitasi.
- `reading_time_minutes` dihitung otomatis dari konten yang sudah diproses.
- Sanitasi harus menjaga tag dasar seperti heading, paragraph, bold, italic, link, quote, list, image, dan code block.
- Sanitasi harus memblokir script, event handler HTML, iframe berbahaya, dan atribut tidak aman.

Acceptance criteria:

- Admin bisa menulis artikel menggunakan Rich Text.
- Admin bisa menulis artikel menggunakan Markdown.
- Admin bisa melihat preview konten.
- Konten tersimpan dengan aman.
- Konten tampil rapi di halaman publik.
- Rendered HTML tidak membuka celah XSS.
- Konten mobile-friendly.
---

### 8.6 Category Management

URL:

```txt
/admin/categories
```

Fields:

- Name.
- Slug.
- Description.
- Status: active/inactive.


Delete rules:

- Category tidak boleh dihapus jika masih digunakan oleh post.
- Admin harus memindahkan post ke category lain sebelum category bisa dihapus.
- Jika delete diblokir, tampilkan pesan Bahasa Indonesia yang jelas.
- Category inactive tetap tersimpan, tetapi tidak tampil sebagai filter public aktif.

Acceptance criteria:

- Admin bisa create category.
- Admin bisa edit category.
- Admin bisa delete category.
- Slug category unique.
- Category aktif dapat digunakan pada post.
- Public category page hanya menampilkan published posts.

---

### 8.7 Tag Management

URL:

```txt
/admin/tags
```

Fields:

- Name.
- Slug.


Delete rules:

- Tag boleh dihapus meskipun sedang digunakan.
- Saat tag dihapus, relasi pada `post_tag` ikut dihapus.
- Post tetap aman tanpa tag.
- Delete tag tetap membutuhkan confirmation modal.

Acceptance criteria:

- Admin bisa create tag.
- Admin bisa edit tag.
- Admin bisa delete tag.
- Slug tag unique.
- Satu post bisa punya banyak tag.
- Satu tag bisa digunakan banyak post.

---

### 8.8 Featured Image Upload

Featured image wajib untuk setiap artikel yang akan diterbitkan.

Requirements:

- Admin bisa upload featured image untuk artikel.
- Format yang didukung: jpg, jpeg, png, webp.
- File size maksimal dikonfigurasi, rekomendasi awal 2MB.
- File disimpan di Laravel storage.
- Featured image tampil di post card dan post detail.
- Artikel tidak bisa dipublish jika featured image belum tersedia.
- Draft boleh disimpan tanpa featured image jika produk ingin memberi fleksibilitas menulis, tetapi publish tetap wajib memiliki featured image.

Image handling minimum:

- Format input yang diterima: jpg, jpeg, png, webp.
- File size maksimal default 2MB dan dapat dikonfigurasi.
- File disimpan di Laravel storage pada disk public.
- Setup wajib menjalankan `php artisan storage:link`.
- Sistem melakukan **automatic WebP conversion** untuk gambar featured image.
- Original file boleh disimpan jika diperlukan untuk fallback, tetapi public page harus memprioritaskan WebP.
- Nama file harus aman dan tidak bergantung pada nama asli upload.
- Featured image harus memiliki alt text:
  - field `featured_image_alt` nullable,
  - fallback otomatis menggunakan title artikel jika alt kosong.
- Public card dan post detail menggunakan URL gambar dari `Storage::url`.
- Jika automatic WebP conversion gagal, upload tidak boleh silent failure; tampilkan error yang jelas atau fallback yang terkontrol sesuai implementasi.

Do Later:

- Responsive image variants.
- Image compression lanjutan.
- Focal point/crop editor.
- Bulk image optimization.
- Full media library.

Acceptance criteria:

- Upload gambar valid berhasil.
- File non-image ditolak.
- File terlalu besar ditolak.
- Error upload mudah dipahami dalam bahasa Indonesia.
- Gambar tampil di halaman publik.
- Publish diblokir jika featured image kosong.

---

### 8.8.1 Media MVP

Menu **Media** pada MVP bukan full media library.

Scope Media MVP:

- Menampilkan daftar featured images yang sudah di-upload dari post.
- Tidak mendukung bulk upload.
- Tidak mendukung folder.
- Tidak mendukung tagging media.
- Tidak mendukung media management kompleks.
- Tidak mendukung editing/crop langsung di Media page.
- Admin dapat melihat gambar, nama post terkait, ukuran file jika tersedia, tanggal upload, dan link ke edit post terkait.
- Penghapusan media dilakukan melalui penghapusan/ganti featured image pada post, bukan dari halaman Media secara terpisah.

URL:

```txt
/admin/media
```

Acceptance criteria:

- Admin dapat membuka halaman Media.
- Media page hanya menampilkan featured images yang terkait dengan post.
- Media page menggunakan TailAdmin-style table/grid.
- Tidak ada fitur bulk upload pada MVP.
- Tidak ada folder/tagging media pada MVP.

Full media library masuk fase Do Later.

---

### 8.9 Public Homepage

URL:

```txt
/
```

Homepage menampilkan:

- Header.
- Hero sederhana.
- Featured article optional.
- List artikel terbaru.
- Category chips.
- Search input.
- Pagination.
- Footer.

Featured article behavior:

- MVP menggunakan field `is_featured` pada posts.
- Jika ada lebih dari satu published post dengan `is_featured = true`, tampilkan yang terbaru berdasarkan `published_at`.
- Jika tidak ada post featured, homepage menggunakan published post terbaru sebagai fallback.
- Draft dan archived tidak boleh menjadi featured article walaupun `is_featured = true`.

Hero copy example:

```txt
Ngopi Dulur

Cerita, catatan, dan pikiran ringan
yang enak dibaca sambil ngopi.

Seduh bacaan terbaru
```

Post card menampilkan:

- Featured image.
- Title.
- Excerpt.
- Category.
- Published date.
- Read more link.

CTA copy:

```txt
Baca selengkapnya
```

Acceptance criteria:

- Hanya published posts yang tampil.
- Draft tidak tampil.
- Archived tidak tampil.
- Artikel diurutkan berdasarkan `published_at`.
- Pagination berjalan.
- Layout responsif.
- Homepage terasa hangat, editorial, dan modern.

---

### 8.10 Public Post Detail

URL:

```txt
/posts/{slug}
```

Post detail menampilkan:

- Title.
- Featured image.
- Category.
- Tags.
- Published date.
- Author name.
- Content.
- Related posts.
- SEO metadata.

Article metadata example:

```txt
Ditulis oleh Admin · 28 April 2026 · 5 menit baca
```

Acceptance criteria:

- Published post bisa dibuka.
- Draft post tidak bisa dibuka publik.
- Archived post tidak bisa dibuka publik.
- Slug tidak ditemukan menampilkan 404.
- Related posts berdasarkan category atau tag.
- Metadata SEO tersedia.

Catatan penting: meskipun MVP adalah blog pribadi, tetap tampilkan `author name`. Ini membuat sistem siap berkembang menjadi multi-author nanti.

---

### 8.11 Category & Tag Pages

Category URL:

```txt
/category/{slug}
```

Tag URL:

```txt
/tag/{slug}
```

Category page menampilkan:

- Nama kategori.
- Deskripsi kategori.
- List published posts dalam kategori.
- Pagination.

Tag page menampilkan:

- Nama tag.
- List published posts dengan tag tersebut.
- Pagination.

Example empty state:

```txt
Belum ada tulisan di kategori ini.
Mungkin kopinya masih diseduh.
```

Acceptance criteria:

- Hanya published posts yang tampil.
- Draft dan archived tidak tampil.
- Slug tidak valid menampilkan 404.
- Empty state tampil jika belum ada artikel.

---

### 8.12 Full-Text Search

URL:

```txt
/search?q=keyword
```

Search menggunakan **MySQL Full-Text Search** untuk hasil yang lebih relevan dibanding pencarian `LIKE` sederhana.

Search mencari berdasarkan:

- Title.
- Excerpt.
- Content.

Requirements:

- Search hanya menampilkan artikel dengan status `published`.
- Search menggunakan full-text index pada kolom yang relevan.
- Search result harus memiliki pagination.
- Empty state tampil jika tidak ada hasil.
- Query kosong tidak membuat error.
- Input aman dari injection.

Acceptance criteria:

- Search hanya mengembalikan published posts.
- Search menggunakan full-text index.
- Query kosong tidak membuat error.
- Jika tidak ada hasil, tampilkan empty state.
- Search input aman dari injection.
- Hasil search memiliki pagination.

---

### 8.13 Site Settings Management

Admin perlu halaman pengaturan untuk mengubah informasi dan komponen editable pada blog.

URL:

```txt
/admin/settings
```

Settings MVP:

- Nama blog.
- Tagline.
- Logo.
- Favicon.
- Default SEO title.
- Default SEO description.
- Default Open Graph image.
- Footer text.
- Social links.
- Homepage hero title.
- Homepage hero subtitle.
- Homepage CTA text.
- Theme default: terang atau dark espresso.

Requirements:

- Semua label menggunakan bahasa Indonesia.
- Admin bisa upload logo dan favicon.
- Admin bisa mengubah copy utama homepage tanpa edit code.
- Setting disimpan di database.
- Public page mengambil nilai dari settings aktif.

Acceptance criteria:

- Admin bisa membuka halaman pengaturan.
- Admin bisa mengubah nama blog.
- Admin bisa mengubah tagline.
- Admin bisa upload atau mengganti logo.
- Admin bisa mengubah hero copy homepage.
- Perubahan tampil di public blog setelah disimpan.
- Validasi error tampil jelas dalam bahasa Indonesia.

---

### 8.14 Sitemap XML

Ngopi Dulur harus menyediakan sitemap XML untuk membantu indexing search engine.

URL:

```txt
/sitemap.xml
```

Sitemap mencakup:

- Homepage.
- Published post detail pages.
- Active category pages.
- Tag pages yang memiliki published posts.

Sitemap tidak mencakup:

- Draft posts.
- Archived posts.
- Admin routes.
- Preview routes.

Acceptance criteria:

- `/sitemap.xml` dapat diakses publik.
- Sitemap hanya berisi URL publik yang valid.
- Draft dan archived posts tidak masuk sitemap.
- Published post baru masuk sitemap.
- Archived post keluar dari sitemap.

---

### 8.15 Public Theme: Terang dan Dark Espresso

Public blog langsung mendukung dua tema:

1. **Tema Terang** — cream, latte, coffee brown, caramel.
2. **Tema Dark Espresso** — espresso, mocha, cream text, caramel accent.

Requirements:

- Theme dibangun dengan Tailwind design tokens.
- Struktur class rapi dan mudah dirawat.
- Public page mendukung switching tema.
- Default theme bisa diatur dari Admin Settings.
- Preferensi theme user dapat disimpan di browser jika diperlukan.

Acceptance criteria:

- Public blog memiliki tema terang.
- Public blog memiliki tema dark espresso.
- Theme switch tidak merusak readability.
- Semua komponen utama mendukung dua tema: header, card, badge, article detail, footer, search.
- Pengaturan default theme dapat dikontrol dari admin settings.
---

### 8.16 Public Error Pages

Public blog harus memiliki error page yang sesuai identitas Ngopi Dulur.

Scope MVP:

- 404 page untuk slug tidak ditemukan, draft, archived, category/tag tidak valid, dan route publik tidak valid.
- 500 page sederhana jika diperlukan oleh framework.
- Error page menggunakan visual warm coffee + modern tech.
- Error page tidak membocorkan informasi teknis.

404 copy example:

```txt
Tulisan ini belum ketemu.
Mungkin kopinya belum diseduh.
```

Acceptance criteria:

- Draft post yang diakses lewat `/posts/{slug}` menampilkan 404.
- Archived post yang diakses lewat `/posts/{slug}` menampilkan 404.
- Slug post/category/tag tidak valid menampilkan 404.
- 404 page responsif dan sesuai brand.
- Tidak ada stack trace atau detail internal tampil di production.


---

## 9. User Experience Flow

### 9.1 Admin Membuat Artikel Baru

1. Admin login.
2. Admin masuk dashboard.
3. Admin klik `Tulis Artikel Baru`.
4. Admin mengisi title.
5. Sistem membuat slug otomatis.
6. Admin menulis excerpt.
7. Admin menulis konten.
8. Admin memilih category.
9. Admin menambahkan tags.
10. Admin upload featured image.
11. Admin klik `Simpan Draft` atau `Terbitkan`.
12. Sistem menyimpan artikel.
13. Jika published, artikel tampil di halaman publik.

UX opinion: admin harus bisa publish artikel tanpa berpindah halaman terlalu banyak. Form harus fokus dan tidak terlalu ramai.

---

### 9.2 Admin Mengedit Artikel

1. Admin membuka menu Tulisan.
2. Admin mencari artikel.
3. Admin klik Edit.
4. Admin mengubah konten.
5. Admin klik Save Changes.
6. Sistem menyimpan perubahan.
7. Jika artikel sudah published, perubahan langsung tampil publik.

UX opinion: status artikel harus terlihat jelas di halaman edit. Gunakan badge besar seperti `Draft`, `Published`, atau `Archived`.

---

### 9.3 Pembaca Membaca Artikel

1. Pembaca membuka homepage.
2. Pembaca melihat daftar artikel terbaru.
3. Pembaca klik artikel.
4. Pembaca membaca artikel.
5. Pembaca melihat related posts.
6. Pembaca bisa lanjut ke category/tag lain.

UX opinion: halaman baca harus bersih. Fokus utama adalah judul, gambar, dan konten. Jangan terlalu ramai.

---

## 10. Brand & Visual Direction

### 10.1 Visual Concept

Ngopi Dulur menggunakan konsep:

> **Warm Coffee Meets Modern Tech**

Website harus terasa hangat, santai, dan personal seperti ngobrol di warung kopi, tetapi tetap memiliki UI yang bersih, cepat, dan profesional seperti produk digital modern.

Ngopi Dulur tidak boleh terlihat terlalu corporate, tapi juga jangan terlalu tradisional. Rasanya harus seperti:

- Blog pribadi yang ramah.
- Tempat membaca yang nyaman.
- Dashboard admin yang rapi.
- Produk modern yang mudah digunakan.
- Ada sentuhan kopi, komunitas, dan kedekatan.

---

### 10.2 Brand Personality

Ngopi Dulur harus terasa:

- **Hangat** — seperti tempat ngobrol santai.
- **Akrab** — tidak kaku, tidak terlalu formal.
- **Bersih** — layout modern, whitespace cukup.
- **Fokus** — pengalaman membaca tidak terganggu.
- **Produktif** — admin dashboard cepat untuk menulis dan publish.
- **Sedikit playful** — boleh ada microcopy yang manusiawi.

Contoh tone tulisan:

```txt
Selamat datang kembali ☕
Lanjutkan tulisanmu hari ini.

Belum ada artikel.
Yuk mulai seduh tulisan pertama.

Artikel berhasil dipublikasikan.
Saatnya dibaca dulur-dulur.
```

---

### 10.3 Design Principles

#### Warm but Clean

Gunakan warna hangat seperti kopi, cream, caramel, dan coklat tua. Tapi layout tetap clean, tidak terlalu ramai.

Jangan membuat website terlalu vintage. Ngopi Dulur harus tetap terasa modern.

#### Reading First

Halaman artikel harus mengutamakan kenyamanan membaca.

Yang penting:

- Font mudah dibaca.
- Line height lega.
- Konten tidak terlalu melebar.
- Kontras cukup.
- Tidak banyak elemen mengganggu.
- Mobile harus nyaman.

#### Admin Should Feel Fast

Dashboard admin harus terasa seperti tool kerja, bukan halaman dekoratif.

Yang penting:

- Sidebar jelas.
- Tombol utama terlihat.
- Form post nyaman untuk menulis.
- Status artikel mudah dipahami.
- Table mudah discan.

#### Coffee Warmth in Details

Nuansa warung kopi tidak perlu berlebihan. Cukup hadir lewat:

- Warna.
- Ilustrasi kecil.
- Icon kopi.
- Empty state copy.
- Greeting dashboard.
- Subtle texture/pattern.
- Nama section yang terasa personal.

---

## 11. Color Palette

### 11.1 Primary Palette

| Token | Warna | Hex | Penggunaan |
|---|---:|---|---|
| Coffee Brown | Coklat kopi | `#5C3A21` | Primary button, heading accent |
| Espresso | Coklat gelap | `#2B1B12` | Text utama, footer, sidebar |
| Cream | Krem hangat | `#FFF7EA` | Background utama |
| Latte | Krem muda | `#F4E3C1` | Card background, section soft |
| Caramel | Oranye caramel | `#C87941` | CTA, highlight, active state |
| Mocha | Coklat medium | `#8B5E3C` | Badge, border accent |

### 11.2 Neutral Palette

| Token | Warna | Hex | Penggunaan |
|---|---:|---|---|
| Charcoal | Abu gelap | `#1F2933` | Text modern |
| Slate | Abu medium | `#64748B` | Secondary text |
| Soft Border | Border halus | `#E7D8C5` | Card/table border |
| White | Putih | `#FFFFFF` | Card, form, admin surface |

### 11.3 Status Colors

| Status | Warna | Hex |
|---|---:|---|
| Draft | Abu biru | `#64748B` |
| Published | Hijau | `#16A34A` |
| Archived | Merah lembut | `#DC2626` |
| Warning | Amber | `#D97706` |

---

## 12. Typography Direction

### 12.1 Public Blog

Gunakan kombinasi font yang terasa editorial tapi tetap modern.

Rekomendasi:

```txt
Heading: Lora
Body: Plus Jakarta Sans
```

Alasan:

- **Lora** memberi rasa hangat dan editorial.
- **Plus Jakarta Sans** terasa modern, clean, dan cocok untuk bahasa Indonesia.

### 12.2 Admin Dashboard

Untuk dashboard admin, gunakan sans-serif penuh.

```txt
Font: Plus Jakarta Sans atau Inter
```

Dashboard tidak perlu font serif. Admin harus terasa cepat dan praktis.

---

## 13. UI Requirements

### 13.1 Public Website UI Direction

#### Homepage

Homepage harus terasa seperti halaman depan blog personal yang hangat.

Sections:

1. Header.
2. Hero.
3. Featured article.
4. Latest articles.
5. Category chips.
6. Search.
7. Footer.

Homepage visual style:

- Background cream.
- Hero dengan gradient lembut cream ke latte.
- Card artikel putih/krem dengan shadow halus.
- Featured image rounded.
- Category chip warna caramel/mocha.
- CTA button coffee brown.

#### Article Card

Article card harus clean dan mudah discan.

Card content:

- Featured image.
- Category badge.
- Title.
- Excerpt.
- Published date.
- Read more link.

Style:

```txt
Card background: white
Border: soft border
Radius: 16px / 20px
Shadow: subtle
Hover: sedikit naik + shadow lebih jelas
```

#### Post Detail Page

Halaman artikel adalah bagian paling penting.

Layout:

- Max width konten: 720px–800px.
- Featured image besar.
- Title besar dan kuat.
- Metadata kecil.
- Content line height lega.
- Related posts di bawah artikel.

Style artikel:

```txt
Background: cream
Content surface: white atau transparent
Heading: espresso
Body text: charcoal
Links: caramel
Blockquote: latte background + coffee border
Code block: dark espresso style
```

---

### 13.2 Admin Dashboard UI Direction

Dashboard menggunakan **TailAdmin-style layout**:

- Left sidebar.
- Topbar.
- Main content area.
- Card stats.
- Table.
- Form panel.
- Modal.
- Toast notification.

Namun visualnya tetap diberi identitas Ngopi Dulur.

Admin background:

```txt
#FFF7EA atau #FAF7F2
```

Sidebar:

```txt
Background: #2B1B12
Text: #F4E3C1
Active menu: #C87941
```

Primary button:

```txt
Background: #5C3A21
Hover: #8B5E3C
Text: white
```

#### TailAdmin Non-Generic Audit

Admin dashboard wajib menggunakan TailAdmin Laravel sebagai dasar, tetapi tidak boleh terlihat seperti template generic tanpa identitas Ngopi Dulur.

Minimum customization:

- Sidebar menggunakan warna espresso `#2B1B12`.
- Text sidebar menggunakan latte/cream.
- Active menu menggunakan caramel `#C87941`.
- Primary action menggunakan coffee brown `#5C3A21`.
- Dashboard greeting memakai microcopy Ngopi Dulur.
- Empty state memakai tone brand.
- Toast memakai copy Bahasa Indonesia yang hangat.
- Card stats memakai aksen coffee/caramel secukupnya.
- Jangan mengubah struktur utama TailAdmin.
- Jangan mengganti TailAdmin dengan UI kit lain.

Acceptance criteria audit:

- Admin dashboard masih jelas sebagai TailAdmin-style layout.
- Admin dashboard memiliki identitas Ngopi Dulur.
- Tidak ada warna default template yang dominan tanpa penyesuaian brand.
- Menu dan form tetap mudah dipakai, tidak terlalu dekoratif.

#### Post Editor Layout

```txt
Main column:
- Title
- Slug
- Content editor

Right sidebar:
- Status
- Publish actions
- Category
- Tags
- Featured image
- SEO metadata
```

UX opinion: jangan sembunyikan action penting dalam dropdown. Untuk blog pribadi, admin harus bisa menulis cepat dan publish tanpa mikir.

---

## 14. Component Style Guide

### 14.1 Buttons

Primary:

```txt
Coffee Brown background
White text
Rounded-xl
Medium weight
```

Secondary:

```txt
White background
Coffee border
Coffee text
```

Danger:

```txt
Soft red background
Red text
```

### 14.2 Cards

```txt
Background: white
Border: #E7D8C5
Radius: 20px
Shadow: soft
Padding: 24px
```

### 14.3 Inputs

```txt
Background: white
Border: soft border
Focus ring: caramel
Radius: 12px
```

### 14.4 Badges

Category badge:

```txt
Latte background
Coffee brown text
```

Tag badge:

```txt
Cream background
Mocha text
```

### 14.5 Toasts & Confirmation

Success:

```txt
Artikel berhasil diterbitkan ☕
```

Draft saved:

```txt
Draft aman tersimpan.
```

Delete confirmation:

```txt
Yakin ingin menghapus tulisan ini?
Aksi ini tidak bisa dibatalkan.
```

---

## 15. Tailwind Theme Recommendation

Tambahkan design token seperti ini di konfigurasi Tailwind.

```js
theme: {
  extend: {
    colors: {
      coffee: {
        50: '#FFF7EA',
        100: '#F4E3C1',
        300: '#C87941',
        500: '#8B5E3C',
        700: '#5C3A21',
        900: '#2B1B12',
      },
      neutralwarm: {
        50: '#FAF7F2',
        100: '#E7D8C5',
        500: '#64748B',
        900: '#1F2933',
      },
    },
    borderRadius: {
      xl2: '1.25rem',
    },
    boxShadow: {
      soft: '0 10px 30px rgba(43, 27, 18, 0.08)',
    },
    fontFamily: {
      heading: ['Lora', 'serif'],
      sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
    },
  },
}
```

---

## 16. Future Expansion Path: Multi-Author CMS

Bagian ini bukan MVP, tapi harus memengaruhi desain teknis dari awal.

### 16.1 Database Harus Siap Multi-User

Walaupun MVP hanya satu admin, tabel `posts` tetap harus memiliki:

```txt
user_id
```

Artinya setiap artikel punya author.

Tabel `users` juga sebaiknya memiliki:

```txt
role
status
bio
avatar
```

Walaupun belum semua field dipakai di MVP, field ini membuat ekspansi lebih mudah.

---

### 16.2 Future Post Status

MVP status:

```txt
draft
published
archived
```

Future status:

```txt
draft
in_review
changes_requested
approved
scheduled
published
archived
```

Saran implementasi: gunakan enum/string yang mudah diperluas. Jangan hardcode status terlalu kaku di banyak tempat.

---

### 16.3 Future Admin Navigation

MVP sidebar:

```txt
Dashboard
Tulisan
Kategori
Tag
Media
Pengaturan
Logout
```

Future sidebar:

```txt
Dashboard
My Posts
All Posts
Review Queue
Categories
Tags
Media
Users
Activity Log
Settings
Logout
```

MVP tidak perlu menampilkan menu future ini.

---

### 16.4 Future Editorial Workflow

Nanti jika menjadi CMS multi-penulis:

1. Writer membuat draft.
2. Writer submit artikel.
3. Artikel masuk review queue.
4. Editor review artikel.
5. Editor request changes atau approve.
6. Editor publish artikel.
7. Activity log mencatat perubahan.

Untuk MVP, workflow ini belum dibuat.

---

## 17. Technical Considerations

### 17.1 Architecture Recommendation

Gunakan Laravel sebagai aplikasi utama dengan pemisahan pengalaman frontend sebagai berikut:

- **Public blog:** Blade-rendered pages untuk performa, SEO, dan kesederhanaan routing.
- **Public interactive components:** Vue-rendered components untuk bagian interaktif seperti theme switcher, search enhancement, atau komponen dinamis lain.
- **Admin dashboard:** full Vue SPA untuk pengalaman dashboard yang cepat dan modern.
- **Backend:** Laravel menangani routing, auth, controller, API endpoint untuk admin SPA, validation, storage, sitemap, dan rendering public pages.
- **Database:** MySQL menyimpan data utama.
- **ORM:** Eloquent ORM digunakan untuk model dan relationship.
- **Build tool:** Vite 8 digunakan untuk bundling Vue dan Tailwind assets.
- **Styling:** Tailwind CSS dengan design tokens untuk tema terang dan dark espresso.

Opini teknis: ini hybrid architecture yang tepat. Public pages pakai Blade agar SEO dan server-rendered content aman. Admin dashboard pakai Vue SPA karena pengalaman mengelola konten memang butuh UI yang lebih interaktif.
---

### 17.2 Suggested Database Schema

#### users

| Field | Type | Notes |
|---|---|---|
| id | bigint | primary key |
| name | varchar | required |
| email | varchar | unique |
| password | varchar | hashed |
| role | varchar | default `admin` |
| status | varchar | default `active` |
| bio | text | nullable |
| avatar | varchar | nullable |
| created_at | timestamp |  |
| updated_at | timestamp |  |

Catatan: `role`, `bio`, dan `avatar` disiapkan untuk future multi-author.

#### posts

| Field | Type | Notes |
|---|---|---|
| id | bigint | primary key |
| user_id | bigint | author |
| category_id | bigint | required |
| title | varchar | required |
| slug | varchar | unique |
| excerpt | text | nullable |
| content_format | varchar | `richtext` atau `markdown` |
| content | longtext | source content asli |
| rendered_content | longtext | HTML yang sudah dirender dan disanitasi |
| reading_time_minutes | integer | nullable, dihitung otomatis |
| featured_image | varchar | required for published posts, public output prioritizes WebP |
| featured_image_original | varchar | nullable, original upload jika disimpan |
| featured_image_alt | varchar | nullable, fallback ke title |
| is_featured | boolean | default false |
| status | varchar | draft, published, archived |
| meta_title | varchar | nullable |
| meta_description | text | nullable |
| published_at | timestamp | nullable |
| created_at | timestamp |  |
| updated_at | timestamp |  |
| deleted_at | timestamp | optional soft delete |

#### categories

| Field | Type | Notes |
|---|---|---|
| id | bigint | primary key |
| name | varchar | required |
| slug | varchar | unique |
| description | text | nullable |
| is_active | boolean | default true |
| created_at | timestamp |  |
| updated_at | timestamp |  |

#### tags

| Field | Type | Notes |
|---|---|---|
| id | bigint | primary key |
| name | varchar | required |
| slug | varchar | unique |
| created_at | timestamp |  |
| updated_at | timestamp |  |

#### post_tag

| Field | Type | Notes |
|---|---|---|
| post_id | bigint | foreign key |
| tag_id | bigint | foreign key |

#### site_settings

| Field | Type | Notes |
|---|---|---|
| id | bigint | primary key |
| key | varchar | unique |
| value | longtext/json | setting value |
| type | varchar | text, image, json, boolean |
| group | varchar | general, seo, homepage, theme, social |
| created_at | timestamp |  |
| updated_at | timestamp |  |

Settings awal yang perlu disimpan:

- `site_name`
- `site_tagline`
- `site_logo`
- `site_favicon`
- `default_meta_title`
- `default_meta_description`
- `default_og_image`
- `homepage_hero_title`
- `homepage_hero_subtitle`
- `homepage_cta_text`
- `footer_text`
- `social_links`
- `default_theme`

---

### 17.3 Future Tables

Belum dibuat di MVP, tapi bisa ditambahkan nanti:

#### activity_logs

Untuk mencatat aktivitas editorial.

#### post_revisions

Untuk menyimpan riwayat perubahan artikel.

#### editorial_comments

Untuk feedback editor ke writer.

#### media

Untuk media library yang lebih lengkap.

---

### 17.4 Eloquent Relationships

#### User

```php
public function posts()
{
    return $this->hasMany(Post::class);
}
```

#### Post

```php
public function author()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function category()
{
    return $this->belongsTo(Category::class);
}

public function tags()
{
    return $this->belongsToMany(Tag::class);
}
```

#### Category

```php
public function posts()
{
    return $this->hasMany(Post::class);
}
```

#### Tag

```php
public function posts()
{
    return $this->belongsToMany(Post::class);
}
```

---

### 17.5 Routes

Karena admin dashboard adalah **full Vue SPA**, route admin dipisahkan menjadi:

1. Admin UI routes untuk menampilkan SPA.
2. Admin API routes untuk operasi data JSON.
3. Public routes untuk blog publik berbasis Blade.

#### Admin UI Routes

```txt
GET /admin/login
GET /admin
GET /admin/dashboard
GET /admin/posts
GET /admin/posts/create
GET /admin/posts/{id}/edit
GET /admin/categories
GET /admin/tags
GET /admin/media
GET /admin/settings
GET /admin/{any?}
```

Rules:

- Semua Admin UI routes selain login wajib protected auth.
- `/admin/{any?}` dapat diarahkan ke root Admin Vue SPA untuk mendukung client-side routing.
- Admin UI tidak boleh dibuat sebagai banyak halaman Blade terpisah kecuali shell/root SPA.
- Admin dashboard tetap menggunakan TailAdmin Laravel template.

#### Admin API Routes

Disarankan menggunakan prefix:

```txt
/admin/api
```

Routes:

```txt
POST   /admin/api/login
POST   /admin/api/logout

GET    /admin/api/dashboard

GET    /admin/api/posts
POST   /admin/api/posts
GET    /admin/api/posts/{post}
PUT    /admin/api/posts/{post}
DELETE /admin/api/posts/{post}
POST   /admin/api/posts/{post}/publish
POST   /admin/api/posts/{post}/archive
GET    /admin/api/posts/{post}/preview

GET    /admin/api/categories
POST   /admin/api/categories
GET    /admin/api/categories/{category}
PUT    /admin/api/categories/{category}
DELETE /admin/api/categories/{category}

GET    /admin/api/tags
POST   /admin/api/tags
GET    /admin/api/tags/{tag}
PUT    /admin/api/tags/{tag}
DELETE /admin/api/tags/{tag}

GET    /admin/api/media

GET    /admin/api/settings
PUT    /admin/api/settings
```

Rules:

- Semua Admin API routes wajib protected auth, kecuali login.
- Semua response API menggunakan format JSON konsisten.
- Semua validasi backend menggunakan Form Request atau validator Laravel yang setara.
- Error message untuk admin ditampilkan dalam Bahasa Indonesia.
- CSRF protection tetap aktif untuk session-based admin.

#### Public Routes

```txt
GET /
GET /posts/{slug}
GET /category/{slug}
GET /tag/{slug}
GET /search
GET /sitemap.xml
GET /robots.txt
```

Rules:

- Public routes menggunakan Blade.
- Public query post wajib filter `status = published`.
- Draft dan archived harus dianggap tidak ada pada public route.
- Search result page default noindex pada MVP.

#### Future Routes

```txt
GET /author/{username}

GET /admin/users
GET /admin/review-queue
GET /admin/activity-log
```

Future routes tidak perlu dibuat pada MVP.

---

## 18. SEO Requirements

Setiap post published harus mendukung:

- Meta title.
- Meta description.
- Canonical URL.
- Open Graph title.
- Open Graph description.
- Open Graph image.

Default behavior:

- Jika `meta_title` kosong, gunakan title.
- Jika `meta_description` kosong, gunakan excerpt.
- Jika featured image kosong, gunakan default site image.


### 18.1 Robots.txt & Noindex Policy

Ngopi Dulur harus menyediakan `robots.txt` dan aturan noindex untuk halaman yang tidak boleh diindeks.

Requirements:

- Sediakan route publik:
  ```txt
  GET /robots.txt
  ```
- Admin routes harus disallow.
- Preview routes harus noindex/nofollow.
- Draft dan archived tidak memiliki canonical public URL.
- Draft dan archived tidak masuk sitemap.
- Search result page default `noindex, follow` pada MVP.
- Halaman login admin tidak boleh diindeks.
- Sitemap harus direferensikan dari robots.txt.

Example robots.txt:

```txt
User-agent: *
Disallow: /admin/
Disallow: /admin/api/
Disallow: /*preview*

Sitemap: https://example.com/sitemap.xml
```

Acceptance criteria:

- `/robots.txt` bisa diakses.
- Admin route disallow.
- Preview page memiliki noindex/nofollow.
- Search page memiliki noindex pada MVP.
- Sitemap URL tersedia di robots.txt.

SEO acceptance criteria:

- Post detail memiliki title metadata.
- Post detail memiliki description metadata.
- URL artikel menggunakan slug.
- Draft dan archived tidak masuk sitemap.
- Open Graph image menggunakan featured image jika tersedia.

---

## 19. Security Requirements

Security requirements:

- Admin routes dilindungi auth middleware.
- Password di-hash.
- CSRF protection aktif.
- Semua form divalidasi di backend.
- Upload file divalidasi berdasarkan MIME type dan size.
- Render HTML content harus aman.
- Slug unique di database.
- Public query hanya mengambil post dengan status `published`.
- Mass assignment dibatasi dengan `$fillable`.

Poin paling penting: jangan hanya menyembunyikan draft dari frontend. Query backend untuk public blog harus selalu filter `status = published`.

---

## 20. Performance Requirements

- List artikel harus menggunakan pagination.
- Public homepage hanya mengambil data yang diperlukan.
- Gunakan eager loading untuk relasi `category`, `tags`, dan `author` agar menghindari N+1 query.
- Image upload harus memiliki batas ukuran.
- Jika traffic meningkat, category list dan latest posts bisa dicache.
- Index database direkomendasikan untuk:
  - `posts.slug`
  - `posts.status`
  - `posts.published_at`
  - `posts.user_id`
  - `posts.is_featured`
  - full-text index pada `posts.title`, `posts.excerpt`, `posts.content`
  - `categories.slug`
  - `tags.slug`

---

### 20.1 Pagination Defaults

Default pagination:

- Public homepage: 9 posts per page.
- Category page: 9 posts per page.
- Tag page: 9 posts per page.
- Search page: 9 posts per page.
- Admin posts table: 10 atau 15 posts per page.
- Admin categories table: 10 atau 15 categories per page.
- Admin tags table: 10 atau 15 tags per page.
- Admin media page: 12 atau 24 images per page, tergantung layout grid/table.

Rules:

- Semua list publik dan admin harus menggunakan pagination.
- Pagination public harus SEO-friendly.
- Query parameter search/filter harus dipertahankan saat pindah halaman.


## 21. Success Metrics

### 21.1 Admin Metrics

- Admin bisa membuat artikel baru dalam kurang dari 5 menit.
- Admin bisa publish artikel tanpa bantuan developer.
- Admin bisa menemukan artikel lama menggunakan search/filter.
- Tidak ada draft yang tampil di halaman publik.
- Tidak ada archived post yang bisa diakses publik.

### 21.2 Public Blog Metrics

- Homepage menampilkan artikel terbaru dengan benar.
- Post detail bisa dibuka via slug.
- Category dan tag page berjalan.
- Search mengembalikan hasil relevan.
- Tampilan mobile nyaman dibaca.
- Brand terasa hangat dan modern.

### 21.3 Engineering Metrics

- Database schema mendukung author melalui `user_id`.
- Public query konsisten filter published posts.
- Post CRUD memiliki validation yang jelas.
- Slug generation stabil.
- Struktur kode bisa diperluas ke multi-author tanpa rewrite besar.

---

## 22. Milestones & Sequencing

### Milestone 1 — Project Foundation

Estimated effort: XX weeks

Scope:

- Setup Laravel 13.
- Setup Vue 3.
- Setup Vite 8.
- Setup Tailwind CSS.
- Setup MySQL.
- Setup authentication.
- Setup base admin layout.
- Setup base design token Ngopi Dulur.

Deliverables:

- App berjalan lokal.
- Admin bisa login/logout.
- Admin dashboard protected.
- Layout dasar siap.
- Warna dan typography dasar tersedia.

---

### Milestone 2 — Post CRUD

Estimated effort: XX weeks

Scope:

- Create post migration.
- Create post model.
- Create post controller.
- Build post list.
- Build create/edit form.
- Add status draft/published/archived.
- Add slug generation.
- Add validation.

Deliverables:

- Admin bisa create post.
- Admin bisa edit post.
- Admin bisa save draft.
- Admin bisa publish.
- Admin bisa archive.
- Admin bisa delete.

---

### Milestone 3 — Category & Tag

Estimated effort: XX weeks

Scope:

- Category CRUD.
- Tag CRUD.
- Assign category to post.
- Assign tags to post.
- Filter posts by category/status.

Deliverables:

- Admin bisa mengelola category.
- Admin bisa mengelola tag.
- Artikel bisa punya category dan tags.

---

### Milestone 4 — Media Upload

Estimated effort: XX weeks

Scope:

- Featured image upload.
- Storage setup.
- Image validation.
- Automatic WebP conversion.
- Featured image alt fallback.
- Image preview in admin.
- Image render in public blog.

Deliverables:

- Admin bisa upload featured image.
- Featured image otomatis dikonversi ke WebP.
- Featured image tampil di homepage dan post detail.
- Admin Media page menampilkan daftar featured images dari post.

---

### Milestone 5 — Public Blog

Estimated effort: XX weeks

Scope:

- Homepage.
- Post detail.
- Category page.
- Tag page.
- Search page.
- Pagination.
- 404 page.
- Public visual polish sesuai konsep warm coffee + modern tech.

Deliverables:

- Published posts tampil publik.
- Draft dan archived hidden.
- Pembaca bisa browse artikel.
- Public blog terasa hangat, modern, dan nyaman dibaca.

---

### Milestone 6 — SEO, Polish, and Future-Ready Cleanup

Estimated effort: XX weeks

Scope:

- Meta title.
- Meta description.
- Open Graph metadata.
- Empty states.
- Loading states.
- Responsive polish.
- Basic tests.
- Review schema agar siap multi-author.

Deliverables:

- MVP siap launch.
- UI admin lebih rapi.
- Public blog nyaman dibaca.
- Struktur teknis siap berkembang.

---

## 23. MVP Acceptance Criteria

MVP selesai jika:

- Admin bisa login/logout.
- Admin bisa melihat dashboard.
- Admin bisa membuat artikel.
- Admin bisa menyimpan draft.
- Admin bisa publish artikel.
- Admin bisa archive artikel.
- Admin bisa delete artikel.
- Admin bisa upload featured image.
- Admin bisa mengelola kategori.
- Admin bisa mengelola tag.
- Homepage menampilkan published posts.
- Post detail bisa dibuka via slug.
- Category page berjalan.
- Tag page berjalan.
- Search berjalan.
- Draft dan archived tidak tampil publik.
- SEO metadata dasar tersedia.
- Layout responsif.
- Visual brand warm coffee + modern tech terasa di public blog.
- Admin dashboard clean dan tidak terlihat seperti template generic.
- Database sudah punya `user_id` pada posts untuk future multi-author.
- Required MVP Tests untuk auth, visibility, validation, upload, SEO, category/tag delete berjalan.
- Draft dan archived tidak bocor ke homepage, detail, category, tag, search, related posts, preview publik, robots, atau sitemap.

---


### 23.1 Required MVP Tests

Testing visibility draft/archived adalah requirement utama, bukan polish.

#### Auth Tests

- Admin valid bisa login.
- Credential salah gagal login.
- Dashboard admin protected.
- Logout menghapus session.

#### Post Visibility Tests

- Published post tampil di homepage.
- Draft post tidak tampil di homepage.
- Archived post tidak tampil di homepage.
- Draft post tidak bisa dibuka melalui `/posts/{slug}`.
- Archived post tidak bisa dibuka melalui `/posts/{slug}`.
- Draft dan archived tidak tampil di category page.
- Draft dan archived tidak tampil di tag page.
- Draft dan archived tidak tampil di search result.
- Draft dan archived tidak tampil di related posts.
- Draft dan archived tidak masuk sitemap.
- Preview draft hanya bisa dibuka oleh admin login atau signed URL valid.

#### Post Validation Tests

- Category wajib.
- Featured image wajib saat publish.
- Featured image tidak wajib saat draft.
- Slug unique.
- Slug collision menambahkan suffix angka.
- `published_at` otomatis terisi saat publish jika kosong.
- `content_format` wajib bernilai `richtext` atau `markdown`.
- `rendered_content` tersimpan sebagai HTML yang sudah disanitasi.

#### Upload Tests

- Image valid berhasil di-upload.
- Non-image ditolak.
- File terlalu besar ditolak.
- Automatic WebP conversion berjalan.
- Jika WebP conversion gagal, error/fallback terkontrol.

#### Category & Tag Tests

- Category tidak bisa dihapus jika masih digunakan post.
- Category bisa dihapus jika tidak digunakan.
- Tag bisa dihapus walaupun sedang digunakan.
- Relasi `post_tag` bersih setelah tag dihapus.

#### SEO Tests

- Sitemap hanya berisi URL publik valid.
- Draft dan archived tidak masuk sitemap.
- Robots.txt tersedia.
- Admin/preview/search noindex/disallow sesuai policy.

Acceptance criteria:

- Test prioritas tinggi di atas tersedia sebelum MVP dianggap selesai.
- Test dapat dijalankan di local/CI sesuai setup repo.
- Kegagalan test visibility draft/archived harus dianggap blocker launch.


## 24. Risks & Mitigations

### Risk: Scope melebar menjadi WordPress clone

Mitigation:

- Fokus MVP pada publishing flow.
- Jangan bangun plugin, page builder, komentar, atau multi-author workflow dulu.

### Risk: Multi-author terlalu cepat dibangun

Mitigation:

- Siapkan struktur teknisnya.
- Jangan aktifkan UI dan workflow editorial sebelum dibutuhkan.

### Risk: Draft atau archived post tampil publik

Mitigation:

- Semua public query wajib filter `status = published`.
- Tambahkan test untuk visibility post.

### Risk: Editor konten terlalu kompleks

Mitigation:

- Gunakan rich text editor ringan.
- Mulai dari capability dasar.
- Pastikan output aman dan mudah dirender.

### Risk: Visual terlalu tradisional atau terlalu tech

Mitigation:

- Gunakan warna coffee/cream untuk warmth.
- Gunakan layout modern, spacing rapi, dan typography clean.
- Jangan gunakan ornamen kopi berlebihan.

### Risk: Gambar terlalu besar membuat website lambat

Mitigation:

- Batasi ukuran file.
- Gunakan validasi upload.
- Pertimbangkan image compression pada fase berikutnya.

---

## 25. Closed Questions / Final Product Decisions

Semua open questions utama sudah dijawab dan menjadi keputusan produk final untuk MVP.

| No | Decision | Final Answer |
|---:|---|---|
| 1 | Bahasa interface | Full Indonesia |
| 2 | Public theme | Langsung mendukung tema terang dan dark espresso |
| 3 | Content editor | Mendukung Rich Text dan Markdown |
| 4 | Kategori | Wajib untuk setiap artikel |
| 5 | Search | Menggunakan Full-Text Search |
| 6 | Featured image | Wajib untuk setiap artikel published |
| 7 | Admin settings | Perlu halaman settings untuk nama blog, tagline, logo, dan komponen editable lain |
| 8 | Sitemap XML | Wajib ada |
| 9 | Public pages | Dibuat dengan Blade; komponen interaktif dapat menggunakan Vue-rendered components |
| 10 | Admin dashboard | Full Vue SPA |

Implikasi produk:

- MVP menjadi sedikit lebih kuat dari blog sederhana, tetapi masih masuk akal karena semua keputusan mendukung kualitas publishing.
- Full Indonesia akan membuat brand Ngopi Dulur lebih terasa lokal dan akrab.
- Dua tema sejak awal harus dibuat rapi dengan design tokens, bukan class acak.
- Rich Text + Markdown perlu dirancang hati-hati agar penyimpanan dan rendering konten tetap aman.
- Admin settings membuat blog lebih fleksibel tanpa perlu deploy ulang untuk mengubah copy/logo.

---

## 26. Product Recommendation

Keputusan produk terbaik untuk Ngopi Dulur:

### Build Now

- Personal blog CMS.
- Satu admin.
- Post CRUD.
- Category/tag.
- Featured image.
- Basic SEO.
- Sitemap XML.
- Full-text search.
- Public blog berbasis Blade.
- Komponen interaktif public dengan Vue bila dibutuhkan.
- Admin dashboard full Vue SPA.
- Rich Text + Markdown editor.
- Light theme dan dark espresso theme sejak MVP.
- Admin settings untuk editable blog components.
- Media MVP berupa daftar featured images dari post tanpa bulk upload/folder/tagging.
- Secure preview policy dengan auth/signed URL dan noindex.
- Robots.txt dan noindex policy.
- Author field tetap disiapkan.
- Visual warm coffee + modern tech.

### Prepare Now, Activate Later

- `users.role`
- `users.bio`
- `users.avatar`
- `posts.user_id`
- Struktur permission sederhana.
- Author relationship di model.
- Admin UI yang bisa berkembang.

### Do Later

- Multi-writer login.
- Editor role.
- Review queue.
- Submit for review.
- Request changes.
- Activity log.
- Author page.
- Revision history.
- Full media library.
- Advanced theme customization seperti custom palette, theme marketplace, atau multiple custom themes.

Ngopi Dulur harus mulai ramping, cepat, dan fokus. MVP yang bagus bukan yang punya fitur paling banyak, tapi yang membuat admin bisa menulis, menyimpan, dan menerbitkan artikel dengan nyaman.

Final product principle:

> **Simple product, smart foundation, warm personality.**


---

## 27. Development Agent Requirements

Bagian ini mengatur cara Codex/agent mengerjakan proyek agar implementasi konsisten dengan PRD.

### 27.1 Wajib Membaca `.agents`

Agent wajib membaca semua file guideline di folder:

```txt
/.agents
```

Minimum file yang direkomendasikan:

```txt
/.agents/ngopi-dulur-product.md
/.agents/laravel-architecture.md
/.agents/frontend-vue-tailadmin.md
/.agents/rtk-command-policy.md
```

Jika folder atau file belum ada, agent boleh membuatnya berdasarkan PRD ini sebelum implementasi fitur besar.

### 27.2 Context7 Requirement

Agent wajib menggunakan Context7 untuk mengambil dokumentasi terbaru sebelum membuat keputusan yang bergantung pada API/package/framework, terutama:

- Laravel 13
- Vue 3
- Vite 8
- Tailwind CSS
- TailAdmin Laravel
- Package editor Rich Text/Markdown
- Package sanitasi HTML
- Package image/WebP conversion
- Package sitemap/SEO jika digunakan

Context7 digunakan untuk mencegah implementasi memakai dokumentasi usang.

### 27.3 Laravel Boost MCP Requirement

Agent wajib menggunakan MCP Server Laravel Boost jika tersedia.

Jika Laravel Boost belum ada di repo, agent harus menginstall dan mengupdate repo:

```bash
composer require laravel/boost --dev
php artisan boost:install
```

Laravel Boost MCP digunakan untuk:

- Application info.
- Package versions.
- Route list.
- Database schema.
- Logs.
- Docs search.
- Tinker/debugging bila diperlukan.

Laravel Boost tidak menggantikan test suite atau code review.

### 27.4 RTK / Rust Token Killer Requirement

Agent wajib menggunakan RTK / Rust Token Killer untuk command terminal yang menghasilkan output panjang agar context tetap hemat dan bersih.

Setup check:

```bash
rtk --version
```

Jika belum ada, install sesuai environment:

```bash
brew install rtk
```

atau:

```bash
curl -fsSL https://raw.githubusercontent.com/rtk-ai/rtk/refs/heads/master/install.sh | sh
```

atau fallback:

```bash
cargo install --git https://github.com/rtk-ai/rtk
```

Aktifkan hook Codex:

```bash
rtk init -g --codex
```

Gunakan RTK untuk:

```bash
rtk ls
rtk find
rtk grep
rtk read
rtk git status
rtk git diff
rtk git log
```

Akhiri milestone dengan:

```bash
rtk gain
```

RTK bukan pengganti Laravel Boost MCP atau Context7. RTK hanya mengurangi noise terminal.

### 27.5 Implementation Guardrails untuk Agent

Agent tidak boleh:

- Mengganti TailAdmin Laravel dengan UI kit lain.
- Mengubah stack tanpa alasan kuat.
- Membuat multi-author workflow aktif pada MVP.
- Membuat plugin system, page builder, komentar, newsletter, membership, payment, atau theme marketplace pada MVP.
- Membuat full media library pada MVP.
- Membuat public query post tanpa filter `status = published`.
- Merender raw Markdown/Rich Text langsung ke public tanpa sanitasi.
- Membuat preview draft dapat diakses publik tanpa auth/signed URL.
- Membiarkan admin dashboard terlihat seperti template generic tanpa brand Ngopi Dulur.

Agent wajib:

- Membaca `.agents`.
- Menggunakan Context7.
- Menggunakan Laravel Boost MCP jika tersedia.
- Menggunakan RTK untuk output terminal panjang.
- Menjaga Admin Dashboard tetap TailAdmin Laravel.
- Menjaga Public Blog tetap Blade-first.
- Menjaga Admin Dashboard sebagai full Vue SPA.
- Menulis label/error UI dalam Bahasa Indonesia.
- Menambahkan test untuk visibility draft/archived.

