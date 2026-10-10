-- ============================================================
-- Skema Database: tokodvd
-- Sistem Informasi Rental DVD Lightning
-- ============================================================

CREATE DATABASE IF NOT EXISTS `tokodvd` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tokodvd`;

-- ------------------------------------------------------------
-- Struktur Tabel `filmlist`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `filmlist` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `judul` VARCHAR(255) NOT NULL,
    `genre` VARCHAR(100) NOT NULL,
    `rating` VARCHAR(10) NOT NULL DEFAULT '1',
    `tahun` VARCHAR(10) NOT NULL,
    `sutradara` VARCHAR(255) NOT NULL,
    `image` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Struktur Tabel `booking`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `booking` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `judul` VARCHAR(255) NOT NULL,
    `genre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `nomor` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Sample Data Awal `filmlist`
-- ------------------------------------------------------------
INSERT INTO `filmlist` (`id`, `judul`, `genre`, `rating`, `tahun`, `sutradara`, `image`) VALUES
(1, 'Ip Man: The Awakening', 'Action', '5', '2022', 'Gu Guanwen', 'film-ip-man-the-awakening-2022-lk21-d21.jpg'),
(2, 'The Batman', 'Action', '5', '2022', 'Matt Reeves', '62b16827016a5.jpg'),
(3, 'Doctor Strange in the Multiverse of Madness', 'Action', '4', '2022', 'Sam Raimi', '62b167baa1c14.jpg'),
(4, 'Top Gun: Maverick', 'Action', '5', '2022', 'Joseph Kosinski', '62b1669d8b404.jpg')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- ------------------------------------------------------------
-- Sample Data Awal `booking`
-- ------------------------------------------------------------
INSERT INTO `booking` (`id`, `judul`, `genre`, `email`, `nomor`) VALUES
(1, 'Ip Man: The Awakening', 'Action', 'budi@example.com', '081234567890'),
(2, 'Top Gun: Maverick', 'Action', 'siti@example.com', '089876543210')
ON DUPLICATE KEY UPDATE `id` = `id`;
