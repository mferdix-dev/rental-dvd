# Rental DVD Lightning

Aplikasi Web Manajemen Rental & Booking DVD berbasis PHP & MySQL dengan template Light Bootstrap Dashboard.

---

## 🚀 Fitur Utama
1. **Dashboard Metrik & Ringkasan Real-Time**:
   - Menampilkan total koleksi film dan total penyewa aktif.
   - Tabel 5 transaksi booking terbaru dan daftar film terbaru.
2. **Koleksi Film (CRUD & Upload Poster)**:
   - Tambah film dengan upload gambar cover/poster (validasi ketat MIME type, ukuran file max 2MB).
   - Pencarian film secara instan (Prepared Statements - Kebal SQL Injection).
   - Edit film dengan preview gambar dan penggantian poster otomatis (membersihkan file lama).
   - Hapus film dengan penghapusan file poster fisik di server (mencegah orphan files).
3. **Penyewaan / Booking (CRUD)**:
   - Tambah booking baru terintegrasi dengan daftar koleksi film yang tersedia.
   - Pencarian booking berdasarkan judul, email, genre, atau nomor telepon.
   - Edit & update data penyewa.
   - Direct link chat WhatsApp ke penyewa.

---

## 🛠️ Tech Stack & Persyaratan Sistem
- **Bahasa Pemrograman**: PHP 7.4 / PHP 8.0 / PHP 8.1 / PHP 8.2 / PHP 8.3+
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Ekstensi PHP**: `mysqli`, `fileinfo`, `gd` (opsional)
- **Frontend**: Bootstrap 3, Light Bootstrap Dashboard, FontAwesome, Pe-icon-7-stroke

---

## 📦 Panduan Instalasi & Menjalankan Project

### 1. Clone / Tarik Repository
```bash
git clone https://github.com/mferdix-dev/rental-dvd.git
cd rental-dvd
```

### 2. Import Database
1. Buka MySQL Client / phpMyAdmin.
2. Buat database baru bernama `tokodvd` (atau jalankan script `database.sql`):
```bash
mysql -u root -p < database.sql
```

### 3. Konfigurasi Database (Opsional)
Pengaturan default menggunakan host `localhost`, user `root`, password kosong, dan database `tokodvd`.
Jika menggunakan konfigurasi lain, Anda dapat mengatur Environment Variable atau mengubah `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tokodvd');
```

### 4. Jalankan Local PHP Server
Jalankan perintah berikut di terminal:
```bash
php -S localhost:8000
```
Buka browser dan akses: `http://localhost:8000`

---

## 🛡️ Catatan Keamanan & Arsitektur (Engineering Highlights)
- **Prepared Statements**: Seluruh query SQL menggunakan parameter binding via prepared statements untuk mengeliminasi kerentanan SQL Injection.
- **XSS Protection**: Output encoding menggunakan `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` terpusat via helper `e()`.
- **Secure File Upload**: Pengecekan whitelist ekstensi, verifikasi MIME type sebenarnya via `mime_content_type`, verifikasi integritas gambar via `getimagesize`, dan pembuatan nama file acak aman menggunakan `bin2hex(random_bytes())`.
- **Clean File Lifecycle**: Penghapusan file cover lama saat update atau delete film untuk mencegah kebocoran penyimpanan (disk leak).
