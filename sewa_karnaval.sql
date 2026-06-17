-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 17 Jun 2026 pada 07.27
-- Versi server: 8.0.30
-- Versi PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sewa_karnaval`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_sewas`
--

CREATE TABLE `detail_sewas` (
  `id` bigint UNSIGNED NOT NULL,
  `sewa_id` bigint UNSIGNED NOT NULL,
  `kostum_id` bigint UNSIGNED NOT NULL,
  `harga` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `subtotal` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kostums`
--

CREATE TABLE `kostums` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_kostum` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_kostum` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `harga` bigint UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kostums`
--

INSERT INTO `kostums` (`id`, `nama_kostum`, `image_kostum`, `kategori`, `catatan`, `harga`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Sengkuni & Durga', '1769569193_Sengkuni & Durga.jpg', 'Ogoh-Ogoh', NULL, 2000000, 0, '2026-01-25 14:10:17', '2026-01-28 17:01:29', NULL),
(2, 'Maskot Dewi Ungu', '1769569214_Maskot Dewi Ungu.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 14:11:36', '2026-01-28 17:01:29', NULL),
(3, 'Maskot Dewi Merah', '1769569237_Maskot Dewi Merah.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 14:12:18', '2026-01-28 17:01:29', NULL),
(4, 'Maskot Dewi Silver', '1769569256_Maskot Dewi Silver.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 14:12:57', '2026-01-28 17:01:29', NULL),
(5, 'Maskot Dewi Biru', '1769569303_Maskot Dewi Biru.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 14:13:22', '2026-01-28 17:01:29', NULL),
(6, 'Maskot Naga', '1769569343_Maskot Naga.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 14:14:03', '2026-01-28 17:01:29', NULL),
(7, 'Maskot Ksatria Wibawa', '1769569469_Maskot Ksatria Wibawa.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 14:15:37', '2026-01-28 17:01:29', NULL),
(8, 'Maskot Ksatria Amarah', '1769569485_Maskot Ksatria Amarah.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 14:16:39', '2026-01-28 17:01:29', NULL),
(9, 'Maskot Iblis Kream', '1769569510_Maskot Iblis Kream.jpg', 'Full Body', NULL, 200000, 0, '2026-01-25 14:21:06', '2026-01-27 13:05:10', NULL),
(10, 'Maskot Iblis Merah', '1769569536_Maskot Iblis Merah.jpg', 'Full Body', NULL, 200000, 0, '2026-01-25 14:22:10', '2026-01-27 13:05:36', NULL),
(11, 'Monster Garuda', '1769569563_Monster Garuda.jpg', 'Full Body', NULL, 500000, 0, '2026-01-25 14:24:03', '2026-01-27 13:06:03', NULL),
(12, 'Monster Burung Hantu', '1769569585_Monster Burung Hantu.jpg', 'Full Body', NULL, 300000, 0, '2026-01-25 14:24:48', '2026-01-27 13:06:25', NULL),
(13, 'Monster Engrang Kream', '1769569613_Monster Engrang Kream.jpg', 'Full Body', NULL, 300000, 0, '2026-01-25 14:26:18', '2026-01-27 13:06:53', NULL),
(14, 'Maskot Engrang Coklat', '1769569669_Maskot Engrang Coklat.jpg', 'Full Body', NULL, 300000, 0, '2026-01-25 14:26:48', '2026-01-27 13:07:49', NULL),
(15, 'Monster Engrang Merah', '1769569687_Monster Engrang Merah.jpg', 'Full Body', NULL, 300000, 0, '2026-01-25 14:27:19', '2026-01-27 13:08:07', NULL),
(16, 'Prajurit Wira', '1769569943_Prajurit Wira.jpeg', 'Kostum', 'Jumlah Kostum Prajurit ada 80 piece', 100000, 0, '2026-01-25 14:29:21', '2026-01-27 13:12:23', NULL),
(17, 'Prajurit Dyah', '1769570210_Prajurit Dyah.jpg', 'Kostum', 'Jumlah Kostum Prajurit ada 80 piece', 100000, 0, '2026-01-25 14:29:52', '2026-01-27 13:16:50', NULL),
(18, 'Bathara Guru', '1769570232_Bathara Guru.jpg', 'Ogoh-Ogoh', NULL, 1500000, 0, '2026-01-25 14:30:32', '2026-01-27 13:17:12', NULL),
(19, 'Prabu Siliwangi', '1769570569_Prabu Siliwangi.jpg', 'Ogoh-Ogoh', NULL, 1000000, 0, '2026-01-25 14:31:07', '2026-01-27 13:22:49', NULL),
(20, 'Watu Kelir', '1769570597_Watu Kelir.jpg', 'Ogoh-Ogoh', NULL, 1000000, 0, '2026-01-25 14:31:40', '2026-01-27 13:23:17', NULL),
(21, 'Gatotkaca', '1769570636_Gatotkaca.jpg', 'Ogoh-Ogoh', NULL, 1000000, 0, '2026-01-25 14:47:31', '2026-01-27 13:23:56', NULL),
(22, 'Ramayana', '1769570705_Ramayana.jpg', 'Ogoh-Ogoh', NULL, 1000000, 0, '2026-01-25 14:49:31', '2026-01-27 13:25:05', NULL),
(23, 'Bhuta Wana Raja', '1769738955_Bhuta Wana Raja.jpg', 'Ogoh-Ogoh', NULL, 1000000, 0, '2026-01-29 12:09:15', '2026-01-29 12:09:15', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_11_13_031344_create_penyewas_table', 1),
(5, '2025_11_13_031358_create_kostums_table', 1),
(6, '2025_11_13_031410_create_sewas_table', 1),
(7, '2025_12_23_045837_add_photo_to_users_table', 1),
(8, '2026_03_16_064844_create_detail_sewas_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penyewas`
--

CREATE TABLE `penyewas` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `no_telp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `penyewas`
--

INSERT INTO `penyewas` (`id`, `user_id`, `no_telp`, `alamat`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, '087695446780', 'Salaman', '2026-06-17 00:20:44', '2026-06-17 00:20:44', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sewas`
--

CREATE TABLE `sewas` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_sewa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penyewa_id` bigint UNSIGNED NOT NULL,
  `tanggal_sewa` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `dp` int NOT NULL DEFAULT '0',
  `sisa_bayar` int NOT NULL DEFAULT '0',
  `total_biaya` bigint UNSIGNED NOT NULL DEFAULT '0',
  `denda` bigint UNSIGNED NOT NULL DEFAULT '0',
  `kondisi` enum('baik','rusak') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '0',
  `status_bayar` enum('pending','dp_paid','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `midtrans_order_id_dp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `midtrans_order_id_pelunasan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `snap_token` text COLLATE utf8mb4_unicode_ci,
  `snap_token_created_at` timestamp NULL DEFAULT NULL,
  `transaction_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','penyewa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'penyewa',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `photo`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, '$2y$12$T4lJinOBTDcIAGs9JV8I8el6CSO3oEyJz3bLLwl8yr0o.fKVv00Dq', 'admin', NULL, '2026-06-17 00:20:43', '2026-06-17 00:20:43', NULL),
(2, 'Nasrul', 'nasrul@gmail.com', NULL, '$2y$12$ohfPtGWgjGfaUKH3zbIrr.xJ0KnAx/eo0FT4sVizvSl9bzlWvBy2m', 'penyewa', NULL, '2026-06-17 00:20:44', '2026-06-17 00:20:44', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `detail_sewas`
--
ALTER TABLE `detail_sewas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_sewas_sewa_id_index` (`sewa_id`),
  ADD KEY `detail_sewas_kostum_id_index` (`kostum_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kostums`
--
ALTER TABLE `kostums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kostums_kategori_index` (`kategori`),
  ADD KEY `kostums_status_index` (`status`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `penyewas`
--
ALTER TABLE `penyewas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `penyewas_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `penyewas_no_telp_unique` (`no_telp`),
  ADD KEY `penyewas_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `sewas`
--
ALTER TABLE `sewas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sewas_kode_sewa_unique` (`kode_sewa`),
  ADD KEY `sewas_penyewa_id_foreign` (`penyewa_id`),
  ADD KEY `sewas_status_index` (`status`),
  ADD KEY `sewas_status_bayar_index` (`status_bayar`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_index` (`role`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_sewas`
--
ALTER TABLE `detail_sewas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kostums`
--
ALTER TABLE `kostums`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `penyewas`
--
ALTER TABLE `penyewas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `sewas`
--
ALTER TABLE `sewas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_sewas`
--
ALTER TABLE `detail_sewas`
  ADD CONSTRAINT `detail_sewas_kostum_id_foreign` FOREIGN KEY (`kostum_id`) REFERENCES `kostums` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_sewas_sewa_id_foreign` FOREIGN KEY (`sewa_id`) REFERENCES `sewas` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penyewas`
--
ALTER TABLE `penyewas`
  ADD CONSTRAINT `penyewas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sewas`
--
ALTER TABLE `sewas`
  ADD CONSTRAINT `sewas_penyewa_id_foreign` FOREIGN KEY (`penyewa_id`) REFERENCES `penyewas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
