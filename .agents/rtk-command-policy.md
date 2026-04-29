# RTK Command Policy

## Tujuan

Dokumen ini adalah aturan penggunaan **RTK / Rust Token Killer** untuk agent yang bekerja di repo proyek ini.

RTK digunakan untuk mengurangi noise dari output terminal agar context Codex/agent tetap ringkas, hemat token, dan mudah diaudit.

RTK **bukan** pengganti:
- Laravel Boost MCP
- Context7
- guideline agent di folder `/.agents`
- test suite
- code review
- dokumentasi proyek

RTK hanya mengatur cara agent menjalankan command terminal, membaca file, mencari kode, melihat diff, dan menangani output yang panjang.

---

## Prinsip Utama

1. **Gunakan RTK untuk output panjang.**  
   Semua command eksplorasi repo, pencarian, pembacaan file besar, git diff, git log, dan hasil test/build yang verbose harus dijalankan melalui RTK jika memungkinkan.

2. **Jangan dump file besar ke context.**  
   Hindari `cat`, `less`, `grep -R`, `find .`, atau command serupa tanpa filtering karena dapat memenuhi context dengan noise.

3. **Ambil informasi secara bertahap.**  
   Mulai dari ringkasan struktur, lalu buka file spesifik yang relevan.

4. **Gunakan Laravel Boost MCP untuk insight Laravel.**  
   RTK membantu terminal output. Laravel Boost MCP tetap digunakan untuk app info, routes, database schema, logs, docs search, dan tools Laravel lain.

5. **Gunakan Context7 untuk dokumentasi terbaru.**  
   RTK tidak menggantikan kebutuhan mengambil dokumentasi versi terbaru untuk Laravel, Vue, Vite, Tailwind, TailAdmin, atau package lain.

---

## Setup Wajib

Sebelum mulai bekerja, agent harus mengecek apakah RTK tersedia.

```bash
rtk --version
```

Jika RTK belum tersedia, install dengan salah satu metode berikut sesuai environment.

### Homebrew

```bash
brew install rtk
```

### Quick Install

```bash
curl -fsSL https://raw.githubusercontent.com/rtk-ai/rtk/refs/heads/master/install.sh | sh
```

### Cargo Fallback

```bash
cargo install --git https://github.com/rtk-ai/rtk
```

Setelah RTK tersedia, aktifkan hook Codex.

```bash
rtk init -g --codex
```

Verifikasi penghematan token setelah bekerja.

```bash
rtk gain
```

---

## Command yang Direkomendasikan

### Eksplorasi Struktur Repo

Gunakan:

```bash
rtk ls
rtk find
```

Hindari:

```bash
ls -R
find .
tree
```

Jika butuh struktur terbatas, gunakan path yang spesifik.

```bash
rtk ls app
rtk ls routes
rtk ls resources
rtk ls database
```

---

### Membaca File

Gunakan:

```bash
rtk read composer.json
rtk read package.json
rtk read routes/web.php
rtk read vite.config.*
rtk read tailwind.config.*
```

Hindari:

```bash
cat composer.json
cat package.json
cat routes/web.php
```

Untuk file besar, baca bagian yang relevan saja jika tool mendukung, atau gunakan pencarian dulu.

---

### Pencarian Kode

Gunakan:

```bash
rtk grep "Route::" routes
rtk grep "auth" app routes
rtk grep "TailAdmin" .
rtk grep "createApp" resources
rtk grep "published" app database routes resources
```

Hindari:

```bash
grep -R "pattern" .
```

Pencarian harus spesifik pada folder yang relevan:
- `app`
- `routes`
- `database`
- `resources`
- `config`
- `tests`

---

### Git Status, Diff, dan Log

Gunakan:

```bash
rtk git status
rtk git diff
rtk git log
```

Hindari:

```bash
git diff
git log --stat
git log -p
```

Untuk diff besar, batasi ke file tertentu.

```bash
rtk git diff routes/web.php
rtk git diff app/Models/Post.php
rtk git diff resources/js
```

---

## Policy untuk Test dan Build

Test dan build sering menghasilkan output panjang. Agent harus menjaga output tetap ringkas.

### Laravel / PHP

Gunakan output ringkas jika tersedia.

```bash
php artisan test
```

Jika output terlalu panjang, fokuskan ke test tertentu.

```bash
php artisan test --filter=PostVisibilityTest
php artisan test tests/Feature/AdminAuthTest.php
```

Jika test gagal:
- tampilkan ringkasan failure
- baca stack trace yang relevan saja
- jangan dump seluruh output bila tidak diperlukan

### Frontend

Gunakan command package manager yang tersedia di repo.

```bash
npm run build
npm run test
npm run lint
```

Jika output terlalu panjang:
- fokus pada error pertama yang relevan
- jangan paste seluruh log build
- gunakan RTK jika command didukung oleh environment

Contoh:

```bash
rtk npm run build
rtk npm run test
```

---

## Workflow Audit Awal Repo

Saat memulai task baru, jalankan urutan ini.

```bash
rtk --version
rtk ls
rtk read composer.json
rtk read package.json
rtk read routes/web.php
rtk grep "createApp" resources
rtk grep "TailAdmin" .
rtk git status
```

Jika proyek Laravel aktif, gunakan juga Laravel Boost MCP untuk:
- application info
- package versions
- route list
- database schema
- logs
- docs search

---

## Workflow Sebelum Mengubah Kode

Sebelum melakukan perubahan:

1. Baca instruksi agent:
   ```bash
   rtk ls .agents
   rtk read .agents/ngopi-dulur-product.md
   rtk read .agents/laravel-architecture.md
   rtk read .agents/frontend-vue-tailadmin.md
   rtk read .agents/rtk-command-policy.md
   ```

2. Cek status repo:
   ```bash
   rtk git status
   ```

3. Cari implementasi terkait:
   ```bash
   rtk grep "keyword-yang-relevan" app routes resources database tests
   ```

4. Baca file yang akan diubah:
   ```bash
   rtk read path/to/file
   ```

5. Baru lakukan perubahan.

---

## Workflow Setelah Mengubah Kode

Setelah melakukan perubahan:

```bash
rtk git status
rtk git diff
```

Lalu jalankan test/build yang relevan.

Contoh:

```bash
php artisan test --filter=PostVisibilityTest
npm run build
```

Jika output panjang, ringkas hanya bagian error atau failure yang relevan.

Akhiri milestone dengan:

```bash
rtk gain
```

---

## Aturan Khusus Proyek Ngopi Dulur

Agent harus menjaga constraint berikut:

1. **Stack tidak boleh berubah tanpa alasan kuat.**
   - Laravel 13
   - Eloquent ORM
   - MySQL
   - Vue 3
   - Vite 8
   - Tailwind CSS
   - TailAdmin Laravel untuk admin dashboard

2. **Admin dashboard tidak boleh keluar dari TailAdmin Laravel.**  
   Boleh memberi aksen Ngopi Dulur, tetapi jangan mengganti template dengan UI kit lain.

3. **Public blog menggunakan Blade.**  
   Vue hanya digunakan untuk komponen interaktif ringan bila diperlukan.

4. **Admin dashboard adalah full Vue SPA.**

5. **Interface menggunakan Bahasa Indonesia.**

6. **Semua public query post wajib filter `status = published`.**  
   Jangan hanya menyembunyikan draft/archived di frontend.

7. **Gunakan Laravel Boost MCP jika tersedia.**  
   Terutama untuk routes, schema, logs, docs search, dan debugging Laravel.

8. **Gunakan Context7 untuk dokumentasi terbaru.**  
   Terutama sebelum memilih package atau API baru.

---

## Anti-Pattern

Jangan lakukan ini:

```bash
cat large-file.php
grep -R "Post" .
find .
tree
git diff
git log -p
npm run build
```

Jika command di atas menghasilkan output panjang, gunakan RTK atau batasi scope.

Contoh yang benar:

```bash
rtk read app/Models/Post.php
rtk grep "Post" app routes database tests
rtk find "Post" app
rtk git diff app/Models/Post.php
rtk git log
rtk npm run build
```

---

## Kapan Boleh Tidak Menggunakan RTK?

RTK boleh tidak digunakan untuk command yang output-nya kecil, jelas, dan aman, misalnya:

```bash
pwd
php -v
node -v
composer --version
npm --version
php artisan --version
```

Namun jika output mulai panjang, ulangi dengan RTK atau batasi command.

---

## Checklist Agent

Sebelum mulai:
- [ ] `rtk --version` sudah dicek.
- [ ] `rtk init -g --codex` sudah dijalankan jika perlu.
- [ ] `.agents` sudah dibaca.
- [ ] Laravel Boost MCP sudah dicek.
- [ ] Context7 digunakan untuk dokumentasi terbaru bila perlu.

Saat bekerja:
- [ ] Eksplorasi repo memakai RTK.
- [ ] Pencarian kode memakai RTK.
- [ ] File besar dibaca memakai RTK.
- [ ] Diff/log memakai RTK.
- [ ] Output test/build tidak didump berlebihan.

Sebelum selesai:
- [ ] `rtk git status` dijalankan.
- [ ] `rtk git diff` dicek.
- [ ] Test/build relevan dijalankan.
- [ ] `rtk gain` dijalankan jika tersedia.
- [ ] Ringkasan perubahan ditulis jelas.

---

## Catatan

RTK membantu agent tetap fokus pada informasi penting. Tujuan akhirnya bukan sekadar menghemat token, tetapi menjaga kualitas keputusan teknis, mengurangi noise, dan membuat implementasi Ngopi Dulur lebih aman untuk dikerjakan secara bertahap.
