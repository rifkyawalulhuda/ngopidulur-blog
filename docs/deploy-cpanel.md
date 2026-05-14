# Panduan Deploy ke cPanel

Panduan ini khusus untuk proyek `ngopidulur-blog` dan mengikuti alur deploy yang paling aman untuk shared hosting cPanel dengan akses SSH.

## 1. Persiapan Sebelum Upload

Pastikan hal berikut sudah siap:

- domain `ngopidulur.my.id` sudah aktif di cPanel
- akses SSH aktif
- database MySQL sudah dibuat
- user MySQL sudah dibuat dan diberi akses ke database
- PHP versi hosting kompatibel dengan Laravel 13 (PHP 8.2+)
- folder project Laravel sudah disiapkan di server

Struktur yang disarankan:

- folder aplikasi: `/home/USERNAME/ngopidulur`
- document root domain: `/home/USERNAME/ngopidulur/public`

### Jika cPanel tidak mengizinkan custom document root

Banyak shared hosting hanya mengizinkan document root ke `public_html`. Jika itu kasusnya, gunakan strategi berikut:

1. Taruh source code di luar public_html:
   ```text
   /home/USERNAME/ngopidulur/       ← source code Laravel
   /home/USERNAME/public_html/      ← document root domain
   ```

2. Pindahkan isi folder `public/` ke `public_html/`:
   ```bash
   cp -r ~/ngopidulur/public/* ~/public_html/
   ```

3. Edit `public_html/index.php`, ubah path bootstrap:
   ```php
   require __DIR__.'/../ngopidulur/vendor/autoload.php';
   $app = require_once __DIR__.'/../ngopidulur/bootstrap/app.php';
   ```

4. Pastikan `.htaccess` dari folder `public/` juga ada di `public_html/`.

## 2. File `.env` Produksi

Buat file `.env` di folder aplikasi server, lalu isi dengan nilai berikut:

```env
APP_NAME="Ngopi Dulur"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ngopidulur.my.id

APP_LOCALE=id
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database_cpanel
DB_USERNAME=nama_user_cpanel
DB_PASSWORD=password_database_cpanel

SESSION_DRIVER=file
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
- `SESSION_DRIVER=file` paling stabil untuk shared hosting (tidak butuh tabel tambahan)
- jika nilai `.env` berubah, jalankan ulang cache config setelah update

## 3. Upload Project ke Server

Upload seluruh source code Laravel ke folder aplikasi:

```text
/home/USERNAME/ngopidulur
```

Jangan upload `node_modules` dan jangan mengandalkan file `public/hot` di production.

Jika source code berasal dari Git, jalankan di server:

```bash
cd ~/ngopidulur
git pull
```

Jika source code di-upload manual (via File Manager atau FTP), pastikan folder berikut ikut terbawa:

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/`
- `resources/`
- `routes/`
- `storage/`
- `vendor/` (jika tidak mau install composer di server)

## 4. Set Permission

Setelah upload, pastikan permission folder penting sudah benar:

```bash
cd ~/ngopidulur
chmod -R 775 storage bootstrap/cache
```

Jika hosting menggunakan user berbeda untuk web server, tambahkan:

```bash
chown -R USERNAME:USERNAME storage bootstrap/cache
```

Ganti `USERNAME` dengan username cPanel kamu.

## 5. Install Dependensi di Server

Jika server memiliki Composer:

```bash
cd ~/ngopidulur
composer install --no-dev --optimize-autoloader
```

Jika `vendor/` sudah di-upload dari lokal, langkah ini bisa dilewati, tetapi Composer tetap lebih disarankan karena menjamin dependensi sesuai platform server.

## 6. Generate APP_KEY

Jika belum ada `APP_KEY` di `.env`, jalankan:

```bash
cd ~/ngopidulur
php artisan key:generate
```

Jika tidak bisa via SSH, generate di lokal lalu copy nilainya ke `.env` server:

```bash
php artisan key:generate --show
```

## 7. Build Frontend Asset

Karena shared hosting biasanya tidak memakai Node.js di server, build asset dilakukan di lokal:

```bash
npm run build
```

Lalu upload folder:

- `public/build/`

Pastikan isi `public/build` lengkap, termasuk `manifest.json` dan semua file `.js` dan `.css` hasil build.

Jika sebelumnya ada file `public/hot`, hapus file itu di server. File tersebut hanya untuk mode development.

## 8. Jalankan Migrasi Database

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

**Penting:** Segera ganti email dan password admin setelah login pertama melalui menu Profile di dashboard admin.

## 9. Buat Storage Link

Jalankan:

```bash
php artisan storage:link
```

Jika menggunakan strategi `public_html` terpisah, buat symlink manual:

```bash
ln -s /home/USERNAME/ngopidulur/storage/app/public /home/USERNAME/public_html/storage
```

Link ini diperlukan agar gambar featured image, avatar, dan media bisa diakses dari publik.

## 10. Bersihkan dan Cache Konfigurasi

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
- file view dan config lebih cepat dimuat

## 11. Konfigurasi Cron Job

Tambahkan cron job di cPanel (menu Cron Jobs):

```bash
* * * * * cd /home/USERNAME/ngopidulur && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

Sesuaikan:
- `USERNAME` dengan username cPanel
- path PHP (`/usr/local/bin/php`) dengan lokasi PHP di hosting (cek dengan `which php`)

Cron ini menjalankan Laravel scheduler setiap menit. Jika aplikasi menggunakan queue, tambahkan juga:

```bash
* * * * * cd /home/USERNAME/ngopidulur && /usr/local/bin/php artisan queue:work --stop-when-empty --tries=3 >> /dev/null 2>&1
```

## 12. Konfigurasi Sitemap dan Robots

Pastikan setelah deploy, route berikut bisa diakses:

- `https://ngopidulur.my.id/sitemap.xml`
- `https://ngopidulur.my.id/robots.txt`

Kedua route tersebut auto-generate dari Laravel, tidak perlu file fisik.

## 13. Checklist Setelah Deploy

Setelah website dibuka di domain production, cek hal berikut:

- [ ] halaman `/` (Beranda) bisa dibuka
- [ ] halaman `/articles` bisa dibuka
- [ ] halaman `/categories` bisa dibuka
- [ ] halaman `/tentang` bisa dibuka
- [ ] halaman `/search` bisa dibuka
- [ ] halaman detail tulisan bisa dibuka
- [ ] gambar/logo tampil normal
- [ ] CSS dan JavaScript tidak 404
- [ ] mobile view responsive
- [ ] admin login bisa dibuka di `/admin/login`
- [ ] dashboard admin bisa dimuat
- [ ] create/edit tulisan berfungsi
- [ ] upload featured image berfungsi
- [ ] halaman Resume admin bisa diedit
- [ ] global search di header admin berfungsi

Jika muncul layar putih atau asset tidak tampil:

- cek apakah `public/hot` masih ada (hapus jika ada)
- cek apakah `public/build` lengkap
- cek `storage/logs/laravel.log`
- cek koneksi database di `.env`
- cek permission `storage/` dan `bootstrap/cache/`

## 14. Perintah Maintenance Saat Update

Kalau ada perubahan aplikasi setelah website live, alur amannya:

```bash
cd ~/ngopidulur
git pull
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika ada perubahan frontend:

```bash
# Di lokal
npm run build
```

Lalu upload ulang `public/build/` ke server.

Jika ada perubahan database:

```bash
php artisan migrate --force
```

## 15. Troubleshooting Cepat

### Tampilan putih / 500 error

Biasanya karena salah satu dari ini:

- permission `storage/` atau `bootstrap/cache/` kurang (jalankan `chmod -R 775`)
- asset `public/build` belum lengkap
- `public/hot` masih ada
- document root belum mengarah ke `public`
- ada error di `storage/logs/laravel.log`

### Error database

Periksa:

- nama database (harus format `cpaneluser_namadb`)
- username database (harus format `cpaneluser_namauser`)
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
- cek browser console untuk error JavaScript

### Sitemap atau robots 404

Periksa:

- `php artisan route:clear` lalu `php artisan route:cache`
- pastikan tidak ada `.htaccess` yang block route tersebut
- pastikan tidak ada file fisik `sitemap.xml` atau `robots.txt` di `public/`

## 16. Ringkasan Urutan Deploy

Untuk deploy paling aman, ikuti urutan ini:

1. Upload source code Laravel ke folder project
2. Arahkan domain ke folder `public` (atau setup `public_html`)
3. Set permission: `chmod -R 775 storage bootstrap/cache`
4. Isi `.env` produksi
5. Jalankan `composer install --no-dev --optimize-autoloader`
6. Jalankan `php artisan key:generate`
7. Jalankan `php artisan migrate --force`
8. Jalankan `php artisan storage:link`
9. Build frontend di lokal lalu upload `public/build/`
10. Cache config: `php artisan config:cache && php artisan route:cache && php artisan view:cache`
11. Set cron job untuk scheduler
12. Test semua halaman

Kalau semua langkah di atas beres, website production sudah siap dipakai.
