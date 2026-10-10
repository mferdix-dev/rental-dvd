<?php
/**
 * Data Access Layer & Business Logic
 * Rental DVD Application
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Helper untuk mencegah serangan XSS saat merender teks ke HTML
 */
function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Eksekusi SELECT Query dengan Prepared Statement
 */
function executeQuery(string $sql, array $params = [], string $types = ""): array {
    $conn = getDbConnection();
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("Prepare statement failed: " . mysqli_error($conn) . " | SQL: " . $sql);
        return [];
    }

    if (!empty($params)) {
        if (empty($types)) {
            $types = str_repeat('s', count($params));
        }
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (!mysqli_stmt_execute($stmt)) {
        error_log("Execute statement failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return [];
    }

    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_free_result($result);
    }
    mysqli_stmt_close($stmt);
    return $rows;
}

/**
 * Eksekusi INSERT/UPDATE/DELETE Query dengan Prepared Statement
 * Mengembalikan jumlah baris yang terpengaruh, atau 1 jika query berhasil tanpa perubahan nilai (UPDATE idempotent)
 */
function executeNonQuery(string $sql, array $params = [], string $types = ""): int {
    $conn = getDbConnection();
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("Prepare statement failed: " . mysqli_error($conn) . " | SQL: " . $sql);
        return -1;
    }

    if (!empty($params)) {
        if (empty($types)) {
            $types = str_repeat('s', count($params));
        }
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    $executed = mysqli_stmt_execute($stmt);
    if (!$executed) {
        error_log("Execute statement failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return -1;
    }

    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    // Jika berhasil dieksekusi tetapi nilai yang diupdate sama persis, kembalikan 1 agar validasi > 0 tetap lolos
    return ($affected >= 0) ? max(1, $affected) : -1;
}

/**
 * Backward compatibility functions
 */
function queryTableBooking(string $query): array {
    return executeQuery($query);
}

function queryFilmList(string $query): array {
    return executeQuery($query);
}

/* ==========================================================================
   FILM SERVICES
   ========================================================================== */

function getAllFilms(): array {
    return executeQuery("SELECT * FROM `filmlist` ORDER BY `id` DESC");
}

function searchFilms(string $keyword): array {
    $keyword = trim($keyword);
    if ($keyword === '') {
        return getAllFilms();
    }
    $pattern = '%' . $keyword . '%';
    $sql = "SELECT * FROM `filmlist` 
            WHERE `judul` LIKE ? 
               OR `genre` LIKE ? 
               OR `rating` LIKE ? 
               OR `tahun` LIKE ? 
               OR `sutradara` LIKE ? 
            ORDER BY `id` DESC";
    return executeQuery($sql, [$pattern, $pattern, $pattern, $pattern, $pattern], "sssss");
}

function getFilmById(int $id): ?array {
    $rows = executeQuery("SELECT * FROM `filmlist` WHERE `id` = ? LIMIT 1", [$id], "i");
    return $rows[0] ?? null;
}

function tambahFilm(array $data): int {
    $judul = trim($data["judul"] ?? '');
    $genre = trim($data["genre"] ?? '');
    $rating = trim($data["rating"] ?? '1');
    $tahun = trim($data["tahun"] ?? '');
    $sutradara = trim($data["sutradara"] ?? '');

    if ($judul === '') {
        return 0;
    }

    $image = "";
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $uploaded = upload();
        if ($uploaded === false) {
            return -1; // Upload gagal / file tidak valid
        }
        $image = $uploaded;
    }

    $sql = "INSERT INTO `filmlist` (`judul`, `genre`, `rating`, `tahun`, `sutradara`, `image`) VALUES (?, ?, ?, ?, ?, ?)";
    return executeNonQuery($sql, [$judul, $genre, $rating, $tahun, $sutradara, $image], "ssssss");
}

function updateFilmList(array $data): int {
    $id = (int)($data["id"] ?? 0);
    $judul = trim($data["judul"] ?? '');
    $genre = trim($data["genre"] ?? '');
    $rating = trim($data["rating"] ?? '1');
    $tahun = trim($data["tahun"] ?? '');
    $sutradara = trim($data["sutradara"] ?? '');
    $gambarLama = trim($data["gambarLama"] ?? '');

    if ($id <= 0 || $judul === '') {
        return 0;
    }

    $image = $gambarLama;

    // Cek apakah ada file baru yang diunggah
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $uploaded = upload();
        if ($uploaded === false) {
            return -1; // File upload tidak valid
        }
        $image = $uploaded;

        // Bersihkan cover lama jika diganti
        if ($gambarLama !== '' && $gambarLama !== $image) {
            deleteCoverFile($gambarLama);
        }
    }

    $sql = "UPDATE `filmlist` 
            SET `judul` = ?, `genre` = ?, `rating` = ?, `tahun` = ?, `sutradara` = ?, `image` = ? 
            WHERE `id` = ?";
    return executeNonQuery($sql, [$judul, $genre, $rating, $tahun, $sutradara, $image, $id], "ssssssi");
}

function hapusFilm(int $id): int {
    $film = getFilmById($id);
    if ($film && !empty($film['image'])) {
        deleteCoverFile($film['image']);
    }
    return executeNonQuery("DELETE FROM `filmlist` WHERE `id` = ?", [$id], "i");
}

/* ==========================================================================
   BOOKING SERVICES
   ========================================================================== */

function getAllBookings(): array {
    return executeQuery("SELECT * FROM `booking` ORDER BY `id` DESC");
}

function searchBookings(string $keyword): array {
    $keyword = trim($keyword);
    if ($keyword === '') {
        return getAllBookings();
    }
    $pattern = '%' . $keyword . '%';
    $sql = "SELECT * FROM `booking` 
            WHERE `judul` LIKE ? 
               OR `genre` LIKE ? 
               OR `email` LIKE ? 
               OR `nomor` LIKE ? 
            ORDER BY `id` DESC";
    return executeQuery($sql, [$pattern, $pattern, $pattern, $pattern], "ssss");
}

function getBookingById(int $id): ?array {
    $rows = executeQuery("SELECT * FROM `booking` WHERE `id` = ? LIMIT 1", [$id], "i");
    return $rows[0] ?? null;
}

function tambahBooking(array $data): int {
    $judul = trim($data["judul"] ?? '');
    $genre = trim($data["genre"] ?? '');
    $email = trim($data["email"] ?? '');
    $nomor = trim($data["nomor"] ?? '');

    if ($judul === '' || $email === '') {
        return 0;
    }

    $sql = "INSERT INTO `booking` (`judul`, `genre`, `email`, `nomor`) VALUES (?, ?, ?, ?)";
    return executeNonQuery($sql, [$judul, $genre, $email, $nomor], "ssss");
}

function updateBooking(array $data): int {
    $id = (int)($data["id"] ?? 0);
    $judul = trim($data["judul"] ?? '');
    $genre = trim($data["genre"] ?? '');
    $email = trim($data["email"] ?? '');
    $nomor = trim($data["nomor"] ?? '');

    if ($id <= 0 || $judul === '' || $email === '') {
        return 0;
    }

    $sql = "UPDATE `booking` SET `judul` = ?, `genre` = ?, `email` = ?, `nomor` = ? WHERE `id` = ?";
    return executeNonQuery($sql, [$judul, $genre, $email, $nomor, $id], "ssssi");
}

function hapusBooking(int $id): int {
    return executeNonQuery("DELETE FROM `booking` WHERE `id` = ?", [$id], "i");
}

/* ==========================================================================
   DASHBOARD STATS
   ========================================================================== */

function getDashboardStats(): array {
    $filmCount = (int)(executeQuery("SELECT COUNT(*) as total FROM `filmlist`")[0]['total'] ?? 0);
    $bookingCount = (int)(executeQuery("SELECT COUNT(*) as total FROM `booking`")[0]['total'] ?? 0);
    $latestBookings = executeQuery("SELECT * FROM `booking` ORDER BY `id` DESC LIMIT 5");
    $latestFilms = executeQuery("SELECT * FROM `filmlist` ORDER BY `id` DESC LIMIT 4");

    return [
        'total_films' => $filmCount,
        'total_bookings' => $bookingCount,
        'latest_bookings' => $latestBookings,
        'latest_films' => $latestFilms,
    ];
}

/* ==========================================================================
   SECURE FILE UPLOAD & MANAGEMENT
   ========================================================================== */

function upload(): string|false {
    if (!isset($_FILES["file"]) || $_FILES["file"]["error"] === UPLOAD_ERR_NO_FILE) {
        return "";
    }

    $file = $_FILES["file"];

    // Cek kode error bawaan PHP
    if ($file["error"] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Validasi ukuran file
    if ($file["size"] > MAX_UPLOAD_SIZE) {
        return false;
    }

    // Validasi ekstensi
    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
        return false;
    }

    // Validasi MIME type sebenarnya
    if (function_exists('mime_content_type')) {
        $mime = mime_content_type($file["tmp_name"]);
        if (!in_array($mime, ALLOWED_MIME_TYPES, true)) {
            return false;
        }
    }

    // Validasi integritas gambar (mencegah script terselubung)
    if (!@getimagesize($file["tmp_name"])) {
        return false;
    }

    // Buat direktori jika belum ada
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    // Buat nama file acak yang aman dan unik
    $fileName = bin2hex(random_bytes(12)) . '.' . $ext;
    $targetPath = UPLOAD_DIR . $fileName;

    if (move_uploaded_file($file["tmp_name"], $targetPath)) {
        return $fileName;
    }

    return false;
}

function deleteCoverFile(?string $fileName): void {
    if (empty($fileName)) {
        return;
    }
    // Cegah directory traversal attack dengan basename()
    $safeName = basename($fileName);
    $filePath = UPLOAD_DIR . $safeName;
    if (file_exists($filePath) && is_file($filePath)) {
        @unlink($filePath);
    }
}
