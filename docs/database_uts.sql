-- ========================================================
-- DATABASE SCHEMA & SEED DATA UTS PEMROGRAMAN WEB LANJUT
-- Database: PBL_TI_2025_A_RAJA (atau tik_pbl)
-- ========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Table structure for account_type
DROP TABLE IF EXISTS `account_type`;
CREATE TABLE `account_type` (
  `id` CHAR(36) NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table structure for actions
DROP TABLE IF EXISTS `actions`;
CREATE TABLE `actions` (
  `id` CHAR(36) NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table structure for accounts
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` CHAR(36) NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `email` VARCHAR(128) NOT NULL UNIQUE,
  `password` TEXT NOT NULL,
  `account_type_id` CHAR(36) NOT NULL,
  `status` VARCHAR(128) NOT NULL DEFAULT 'Aktif',
  `identification_number` VARCHAR(128) NOT NULL,
  `identification_type` ENUM('NIM', 'NIP') NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_accounts_account_type_idx` (`account_type_id`),
  CONSTRAINT `fk_accounts_account_type` FOREIGN KEY (`account_type_id`)
    REFERENCES `account_type` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- SEED DATA (Sesuai dengan screenshot soal ujian)
-- --------------------------------------------------------

-- Seed account_type
INSERT INTO `account_type` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
('c0a80101-0000-4000-8000-000000000001', 'Admin', 'Pengelola sistem, punya akses penuh', '2026-10-02 10:04:00', '2026-10-02 10:04:00', NULL),
('c0a80101-0000-4000-8000-000000000002', 'Dosen', 'Akun untuk dosen (identitas NIP)', '2026-10-02 10:04:00', '2026-10-02 10:04:00', NULL),
('c0a80101-0000-4000-8000-000000000003', 'Mahasiswa', 'Akun untuk mahasiswa (identitas NIM)', '2026-10-02 10:04:00', '2026-10-02 10:04:00', NULL);

-- Seed actions
INSERT INTO `actions` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
('d0a80101-0000-4000-8000-000000000001', 'Create', 'Menambahkan data baru', '2026-10-02 10:04:00', '2026-10-02 10:04:00', NULL),
('d0a80101-0000-4000-8000-000000000002', 'Delete', 'Menghapus data (soft delete)', '2026-10-02 10:04:00', '2026-10-02 10:04:00', NULL),
('d0a80101-0000-4000-8000-000000000003', 'Read', 'Melihat data', '2026-10-02 10:04:00', '2026-10-02 10:04:00', NULL),
('d0a80101-0000-4000-8000-000000000004', 'Update', 'Mengubah data yang sudah ada', '2026-10-02 10:04:00', '2026-10-02 10:04:00', NULL);

-- Seed accounts (password: admin123 & password123)
INSERT INTO `accounts` (`id`, `name`, `email`, `password`, `account_type_id`, `status`, `identification_number`, `identification_type`, `created_at`, `updated_at`, `deleted_at`) VALUES
('e0a80101-0000-4000-8000-000000000001', 'Administrator', 'admin@pnj.ac.id', '$2y$12$XjKTWpPXnIPXO9sSCQ9Wee/1iyRgH7pP6P393lXKMG3klcAI1McxW', 'c0a80101-0000-4000-8000-000000000001', 'Aktif', '520000000000000746', 'NIP', '2026-10-02 10:04:00', '2026-10-02 10:04:00', NULL),
('e0a80101-0000-4000-8000-000000000002', 'Hi There Im dummy ehe', 'iam@balbalcode.my.id', '$2y$12$5XKhqxRjc7Xgxjq9KWxJYOQaj.9HebkF6q.0.IolgLxtZvZluASd2', 'c0a80101-0000-4000-8000-000000000002', 'Nonaktif', '201012121', 'NIM', '2026-10-02 10:04:00', '2026-10-02 10:04:00', NULL);

SET FOREIGN_KEY_CHECKS = 1;
