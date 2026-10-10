<?php
/**
 * Konfigurasi Sistem Rental DVD
 * Mendukung Environment Variables untuk fleksibilitas deployment (Docker / Cloud / Local)
 */

declare(strict_types=1);

// Konfigurasi Database
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? (string)getenv('DB_PASS') : '');
define('DB_NAME', getenv('DB_NAME') ?: 'tokodvd');
define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));

// Konfigurasi Upload Gambar
define('UPLOAD_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'dvd-cover' . DIRECTORY_SEPARATOR);
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // Maksimal 2 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// Inisialisasi Koneksi Database (MySQLi Singleton)
function getDbConnection(): mysqli {
    static $conn = null;
    if ($conn === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            mysqli_set_charset($conn, 'utf8mb4');
        } catch (mysqli_sql_exception $e) {
            error_log('Database Connection Failed: ' . $e->getMessage());
            die(
                '<div style="font-family:Segoe UI,Tahoma,sans-serif;max-width:600px;margin:50px auto;padding:24px;border:1px solid #f5c6cb;border-radius:8px;background:#f8d7da;color:#721c24;">'
                . '<h3 style="margin-top:0;">Koneksi Database Gagal</h3>'
                . '<p>Sistem tidak dapat terhubung ke database <strong>' . htmlspecialchars(DB_NAME) . '</strong>.</p>'
                . '<p>Silakan pastikan MySQL service berjalan dan database sudah dibuat menggunakan <code>database.sql</code>.</p>'
                . '<details style="margin-top:12px;font-size:12px;color:#555;"><summary>Detail Teknis</summary>'
                . '<pre style="white-space:pre-wrap;margin-top:6px;">' . htmlspecialchars($e->getMessage()) . '</pre>'
                . '</details>'
                . '</div>'
            );
        }
    }
    return $conn;
}

// Global connection untuk backward compatibility
$conn = null;
try {
    $conn = getDbConnection();
} catch (\Throwable $e) {
    // Di-handle di dalam getDbConnection()
}
