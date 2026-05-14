import pathlib

content = r"""
# Panduan Deploy ke cPanel

Panduan ini khusus untuk proyek `ngopidulur-blog` dan mengikuti alur deploy yang paling aman untuk shared hosting cPanel dengan akses SSH.

## 1. Persiapan Sebelum Upload

Pastikan hal berikut sudah siap:

- domain sudah dibuat di cPanel, misalnya `blog.ngopidulur.my.id`
- akses SSH aktif
- database MySQL sudah dibuat
- user MySQL sudah dibuat dan diberi akses ke database
- PHP versi hosting kompatibel dengan Laravel 13 (PHP 8.2+)
- folder project Laravel sudah disiapkan di server

Struktur yang disarankan:

- folder aplikasi: `/home/USERNAME/blog`
- document root domain: `/home/USERNAME/blog/public`

Jika cPanel tidak mengizinkan document root ke folder `public`, gunakan struktur fallback `public_html` sesuai konfigurasi hosting.

## 2. File `.env` Produksi

Buka file `.env` di server, lalu sesuaikan nilai berikut:

```env
APP_NAME="Ngopi Dulur"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://blog.ngopidulur.my.id

APP_LOCALE=id
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database_cpanel
DB_USERNAME=nama_user_cpanel
DB_PASSWORD=password_database_cpanel

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

FILESYSTEM_DISK=public
CACHE_STORE=file
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

Catatan:

- `APP_DEBUG=false` wajib di production
- `APP_URL` harus pakai `https://` jika SSL sudah aktif
- jika nilai `.env` berubah, jalankan ulang cache config setelah update

## 3. Upload Project ke Server

Upload seluruh source code Laravel ke folder aplikasi, misalnya:

```text
/home/USERNAME/blog
```

Jangan upload `node_modules` dan jangan mengandalkan file `public/hot` di production.

Jika source code berasal dari Git, jalankan di server:

```bash
cd ~/blog
git pull
```

Jika source code di-upload manual, pastikan folder berikut ikut terbawa:

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/`
- `resources/`
- `routes/`
- `storage/`
- `vendor/` jika tidak mau install composer di server

## 4. Install Dependensi di Server

Jika server memiliki Composer:

```bash
cd ~/blog
composer install --no-dev --optimize-autoloader
```

Jika `vendor/` sudah di-upload dari lokal, langkah ini bisa dilewati, tetapi Composer tetap lebih disarankan.

## 5. Generate APP_KEY

Jika belum ada `APP_KEY` di `.env`, jalankan:

```bash
cd ~/blog
php artisan key:generate
```

Jika tidak bisa via SSH, generate di lokal lalu copy nilainya ke `.env` server:

```bash
php artisan key:generate --show
```

## 6. Build Frontend Asset

Karena shared hosting biasanya tidak memakai Node.js di server, build asset dilakukan di lokal:

```bash
npm run build
```

Lalu upload folder:

- `public/build/`

Pastikan isi `public/build` lengkap, termasuk `manifest.json` dan file asset hasil build.

Jika sebelumnya ada file `public/hot`, hapus file itu di server. File tersebut hanya untuk mode development.

## 7. Jalankan Migrasi Database

Setelah `.env` benar, jalankan:

```bash
php artisan migrate --force
```

Jika ingin pakai data seed default:

```bash
php artisan db:seed --force
```

Login admin default (jika seeder dijalankan):

- email: `admin@ngopidulur.test`
- password: `password`

Segera ganti password admin setelah login pertama.

## 8. Buat Storage Link

Jalankan:

```bash
php artisan storage:link
```

Jika link sudah ada, itu normal. Tidak perlu dibuat ulang.

Link ini diperlukan agar gambar featured image dan media bisa diakses dari publik.

## 9. Bersihkan dan Cache Konfigurasi

Setelah deploy dan setelah `.env` selesai diisi, jalankan:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Langkah ini penting supaya:

- perubahan `.env` terbaca
- route terbaru aktif
- file view dan config lebih cepat

## 10. Konfigurasi Queue (Opsional)

Aplikasi ini memakai queue database untuk beberapa proses operasional.

Tambahkan cron job di cPanel:

```bash
* * * * * cd /home/USERNAME/blog && /usr/local/bin/php artisan queue:work --stop-when-empty --tries=1 >> /dev/null 2>&1
```

Sesuaikan path `USERNAME` dan lokasi PHP di hosting jika berbeda.

## 11. Konfigurasi Sitemap dan Robots

Pastikan setelah deploy, route berikut bisa diakses:

- `https://blog.ngopidulur.my.id/sitemap.xml`
- `https://blog.ngopidulur.my.id/robots.txt`

Kedua route tersebut auto-generate dari Laravel, tidak perlu file fisik.

## 12. Checklist Setelah Deploy

Setelah website dibuka di domain production, cek hal berikut:

- halaman `/` (Beranda) bisa dibuka
- halaman `/articles` bisa dibuka
- halaman `/categories` bisa dibuka
- halaman `/tentang` bisa dibuka
- halaman `/search` bisa dibuka
- halaman detail tulisan bisa dibuka
- gambar/logo tampil normal
- CSS dan JavaScript tidak 404
- mobile view responsive
- admin login bisa dibuka di `/admin/login`
- dashboard admin bisa dimuat
- create/edit tulisan berfungsi
- upload featured image berfungsi
- halaman Resume admin bisa diedit
- global search di header admin berfungsi
- share buttons di detail tulisan aktif

Jika muncul layar putih atau asset tidak tampil:

- cek apakah `public/hot` masih ada
- cek apakah `public/build` lengkap
- cek `storage/logs/laravel.log`
- cek koneksi database di `.env`

## 13. Perintah Maintenance Saat Update

Kalau ada perubahan aplikasi setelah website live, alur amannya:

```bash
cd ~/blog
git pull
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika ada perubahan frontend:

```bash
npm run build
```

Lalu upload ulang `public/build/`.

Jika ada perubahan database:

```bash
php artisan migrate --force
```

## 14. Troubleshooting Cepat

### Tampilan putih

Biasanya karena salah satu dari ini:

- asset `public/build` belum lengkap
- `public/hot` masih ada
- document root belum mengarah ke `public`
- ada error di `storage/logs/laravel.log`

### Error database

Periksa:

- nama database
- username database
- password database
- `DB_HOST=localhost`

### Gambar tidak tampil

Periksa:

- symlink `public/storage` sudah dibuat lewat `php artisan storage:link`
- permission folder `storage/app/public` minimal 755
- `FILESYSTEM_DISK=public` di `.env`

### Halaman admin kosong

Periksa:

- folder `public/build` lengkap
- Vite manifest ada di `public/build/manifest.json`
- jalankan `php artisan view:clear`

### Sitemap atau robots 404

Periksa:

- `php artisan route:clear`
- pastikan tidak ada `.htaccess` yang block route tersebut

## 15. Rekomendasi Singkat

Untuk deploy paling aman:

1. upload source code Laravel ke folder project
2. arahkan domain ke folder `public`
3. isi `.env` produksi
4. jalankan `composer install`
5. jalankan `php artisan key:generate`
6. jalankan `php artisan migrate --force`
7. jalankan `php artisan storage:link`
8. build frontend di lokal lalu upload `public/build`
9. clear cache Laravel
10. set cron queue jika perlu

Kalau semua langkah di atas beres, website production biasanya sudah siap dipakai.
"""

pathlib.Path(r'D:/Github/ngopidulur-blog/docs/deploy-cpanel.md').write_text(content.lstrip(), encoding='utf-8')
print('Done')
