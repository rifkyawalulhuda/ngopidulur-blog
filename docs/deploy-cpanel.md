# Panduan Deploy ke Shared Hosting cPanel

Last updated: 2026-05-14  
Project: Ngopi Dulur Blog (Laravel 13 + Vue 3)

---

## Prasyarat Hosting

Pastikan shared hosting cPanel memenuhi:

- PHP **8.2 atau lebih tinggi** (cek di MultiPHP Manager)
- MySQL 5.7+ atau MariaDB 10.3+
- SSH access (sangat direkomendasikan)
- Composer tersedia di server
- Ekstensi PHP: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `gd`

Cek ekstensi: cPanel > **Select PHP Version** > **Extensions**.

---

## Langkah 1: Persiapan di Komputer Lokal

### 1.1 Build Asset Frontend

```bash
npm install
npm run build
```

Hasil: folder `public/build/` berisi semua CSS dan JS yang sudah dikompilasi.

### 1.2 Install Composer untuk Production

```bash
composer install --optimize-autoloader --no-dev
```

### 1.3 Siapkan File `.env` Production

Buat file `.env` baru khusus production (jangan edit `.env` lokal):

```env
APP_NAME="Ngopi Dulur"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://domainkamu.com

APP_LOCALE=id
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cpanelusername_ngopidulur
DB_USERNAME=cpanelusername_dbuser
DB_PASSWORD=password_database_kamu

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

FILESYSTEM_DISK=public
CACHE_STORE=file
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=mail.domainkamu.com
MAIL_PORT=587
MAIL_USERNAME=noreply@domainkamu.com
MAIL_PASSWORD=password_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@domainkamu.com"
MAIL_FROM_NAME="Ngopi Dulur"
```

> **Penting:** `APP_DEBUG=false` wajib di production. `APP_KEY` akan di-generate di server.

### 1.4 Buat Zip Project

Zip folder-folder berikut (kecualikan yang tidak perlu):

**Sertakan:**
- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/` (termasuk `public/build/`)
- `resources/`
- `routes/`
- `storage/`
- `vendor/`
- `artisan`
- `composer.json`
- `composer.lock`
- `.env` (file production yang sudah disiapkan)

**Jangan sertakan:**
- `node_modules/`
- `tests/`
- `.git/`
- `temp/`

---

## Langkah 2: Buat Database di cPanel

1. Login ke **cPanel**
2. Buka **MySQL Databases**
3. Buat database baru, contoh: `cpanelusername_ngopidulur`
4. Buat user database baru dengan password yang kuat
5. Tambahkan user ke database dengan **ALL PRIVILEGES**
6. Catat nama database, username, dan password untuk diisi di `.env`

---

## Langkah 3: Upload File ke Server

### Opsi A: File Manager cPanel

1. Buka **File Manager** di cPanel
2. Masuk ke folder `public_html`
3. Upload file zip project
4. Klik kanan zip > **Extract**

### Opsi B: FTP (FileZilla)

1. Buka FileZilla
2. Masukkan host, username, password FTP dari cPanel
3. Upload semua file ke `public_html`

### Opsi C: SSH (Paling Direkomendasikan)

```bash
ssh cpanelusername@domainkamu.com
cd ~/
git clone https://github.com/username/ngopidulur-blog.git ngopidulur
cd ngopidulur
composer install --optimize-autoloader --no-dev
```

---

## Langkah 4: Atur Struktur Folder (Penting untuk Keamanan)

Laravel hanya boleh mengekspos folder `public/` ke web. Struktur yang benar:

```
/home/cpanelusername/
├── ngopidulur/              <- Laravel project (di luar public_html)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── artisan
│   └── .env
└── public_html/             <- Web root (hanya isi folder public/)
    ├── build/
    ├── images/
    ├── storage -> symlink
    ├── .htaccess
    ├── favicon.ico
    └── index.php
```

### 4.1 Pindahkan File via SSH

```bash
# Pindahkan project ke luar public_html
cd ~
mv public_html/ngopidulur-blog ngopidulur

# Pindahkan isi folder public/ ke public_html/
cp -r ngopidulur/public/. public_html/

# Hapus folder public dari project (sudah dipindah)
rm -rf ngopidulur/public
```

### 4.2 Edit `public_html/index.php`

Buka file `public_html/index.php` dan ubah path:

```php
<?php

use Illuminate\Foundation\Application;

// Ubah path ini:
define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../ngopidulur/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../ngopidulur/vendor/autoload.php';

$app = require_once __DIR__.'/../ngopidulur/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
```

---

## Langkah 5: Konfigurasi Laravel di Server

### 5.1 Set Permission Folder

Via SSH:

```bash
cd ~/ngopidulur
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
```

Via File Manager: klik kanan folder `storage` dan `bootstrap/cache` > **Change Permissions** > set **755** (atau **775**).

### 5.2 Generate APP_KEY

Via SSH:

```bash
cd ~/ngopidulur
php artisan key:generate
```

Tanpa SSH, generate di lokal lalu copy ke `.env` server:

```bash
php artisan key:generate --show
# Output: base64:xxxxxxxxxxxxx=
```

Paste ke `.env` server: `APP_KEY=base64:xxxxxxxxxxxxx=`

### 5.3 Jalankan Migration

Via SSH:

```bash
cd ~/ngopidulur
php artisan migrate --force
php artisan db:seed --force
```

Tanpa SSH, export database dari lokal:

```bash
# Di lokal
mysqldump -u root -p nama_database_lokal > backup.sql
```

Lalu import `backup.sql` via **phpMyAdmin** di cPanel.

### 5.4 Buat Storage Symlink

Via SSH:

```bash
cd ~/ngopidulur
php artisan storage:link
```

Tanpa SSH, buat file PHP sementara di `public_html/symlink.php`:

```php
<?php
symlink('/home/cpanelusername/ngopidulur/storage/app/public', __DIR__.'/storage');
echo 'Symlink created!';
```

Akses `https://domainkamu.com/symlink.php` sekali, lalu **hapus file tersebut**.

### 5.5 Cache untuk Performa

```bash
cd ~/ngopidulur
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## Langkah 6: Konfigurasi .htaccess

Pastikan `public_html/.htaccess` berisi:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Force HTTPS
    RewriteCond %{HTTPS} !=on
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Route ke index.php
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Gzip Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript application/json
</IfModule>

# Browser Cache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## Langkah 7: Aktifkan SSL (HTTPS)

1. cPanel > **SSL/TLS Status**
2. Pilih domain
3. Klik **Run AutoSSL** (gratis, dari Let's Encrypt)
4. Tunggu beberapa menit sampai status hijau
5. Update `.env`: `APP_URL=https://domainkamu.com`
6. Jalankan ulang: `php artisan config:cache`

---

## Langkah 8: Buat Admin User

Via SSH (Tinker):

```bash
cd ~/ngopidulur
php artisan tinker
```

Di dalam Tinker:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@domainkamu.com',
    'password' => Hash::make('password_kuat_disini'),
    'role' => 'admin',
]);
```

Tanpa SSH, insert langsung via **phpMyAdmin** ke tabel `users`.

---

## Langkah 9: Setup Cron Job

1. cPanel > **Cron Jobs**
2. Set **Common Settings**: Every Minute
3. Isi command:

```
/usr/local/bin/php /home/cpanelusername/ngopidulur/artisan schedule:run >> /dev/null 2>&1
```

> Ganti `cpanelusername` dengan username cPanel kamu.

---

## Langkah 10: Test Aplikasi

Buka browser dan test:

- `https://domainkamu.com` - Halaman publik blog
- `https://domainkamu.com/admin/login` - Login admin
- Login dengan akun admin yang sudah dibuat
- Buat artikel baru dan publish
- Cek artikel tampil di halaman publik
- Test upload gambar
- Test halaman Tentang

---

## Troubleshooting

### Error 500

```bash
# Cek log error
tail -f ~/ngopidulur/storage/logs/laravel.log
```

- Pastikan `APP_KEY` sudah di-set
- Pastikan permission `storage/` dan `bootstrap/cache/` adalah 775
- Pastikan PHP version >= 8.2

### CSS/JS Tidak Load

- Pastikan folder `public/build/` sudah di-upload
- Cek `APP_URL` di `.env` sesuai domain
- Jalankan: `php artisan config:cache`

### Database Error

- Gunakan `DB_HOST=localhost` (bukan 127.0.0.1)
- Pastikan user database punya ALL PRIVILEGES
- Cek nama database menggunakan prefix cPanel username

### Gambar Tidak Tampil

- Pastikan symlink storage sudah dibuat
- Cek permission `storage/app/public/` adalah 755
- Pastikan `FILESYSTEM_DISK=public` di `.env`

### Halaman Admin Kosong/Blank

- Jalankan `npm run build` di lokal, upload ulang `public/build/`
- Jalankan `php artisan view:clear` di server

---

## Update Aplikasi Setelah Deploy

```bash
# Di lokal
npm run build
git add .
git commit -m "Update: deskripsi perubahan"
git push

# Di server via SSH
cd ~/ngopidulur
git pull
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika ada perubahan asset frontend, upload juga folder `public/build/` yang baru.

---

## Checklist Pre-Deploy

- [ ] PHP 8.2+ tersedia di hosting
- [ ] Database MySQL sudah dibuat di cPanel
- [ ] User database sudah ditambahkan dengan ALL PRIVILEGES
- [ ] `npm run build` sudah dijalankan di lokal
- [ ] `composer install --optimize-autoloader --no-dev` sudah dijalankan
- [ ] File `.env` production sudah disiapkan
- [ ] `APP_DEBUG=false` di `.env`
- [ ] `APP_KEY` sudah di-generate
- [ ] SSL/HTTPS aktif
- [ ] Storage symlink sudah dibuat
- [ ] Migration sudah dijalankan
- [ ] Admin user sudah dibuat
- [ ] Test login admin berhasil
- [ ] Test publish artikel berhasil
- [ ] Test halaman publik berhasil
- [ ] Cron job sudah di-setup

---

## Catatan Keamanan

- Jangan pernah commit file `.env` ke git
- `APP_DEBUG=false` wajib di production
- Gunakan password yang kuat untuk admin dan database
- Aktifkan SSL untuk semua route
- Update Laravel dan dependencies secara berkala
- Backup database minimal seminggu sekali via cPanel > **Backup**
- Hapus file `symlink.php` setelah digunakan
