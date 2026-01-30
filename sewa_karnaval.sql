-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 30 Jan 2026 pada 02.23
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
  `harga` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kostums`
--

INSERT INTO `kostums` (`id`, `nama_kostum`, `image_kostum`, `kategori`, `catatan`, `harga`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Sengkuni & Durga', '1769569193_Sengkuni & Durga.jpg', 'Ogoh-Ogoh', NULL, 2000000, 0, '2026-01-25 21:10:17', '2026-01-29 00:01:29', NULL),
(2, 'Maskot Dewi Ungu', '1769569214_Maskot Dewi Ungu.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 21:11:36', '2026-01-29 00:01:29', NULL),
(3, 'Maskot Dewi Merah', '1769569237_Maskot Dewi Merah.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 21:12:18', '2026-01-29 00:01:29', NULL),
(4, 'Maskot Dewi Silver', '1769569256_Maskot Dewi Silver.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 21:12:57', '2026-01-29 00:01:29', NULL),
(5, 'Maskot Dewi Biru', '1769569303_Maskot Dewi Biru.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 21:13:22', '2026-01-29 00:01:29', NULL),
(6, 'Maskot Naga', '1769569343_Maskot Naga.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 21:14:03', '2026-01-29 00:01:29', NULL),
(7, 'Maskot Ksatria Wibawa', '1769569469_Maskot Ksatria Wibawa.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 21:15:37', '2026-01-29 00:01:29', NULL),
(8, 'Maskot Ksatria Amarah', '1769569485_Maskot Ksatria Amarah.jpg', 'Kostum', NULL, 100000, 0, '2026-01-25 21:16:39', '2026-01-29 00:01:29', NULL),
(9, 'Maskot Iblis Kream', '1769569510_Maskot Iblis Kream.jpg', 'Full Body', NULL, 200000, 0, '2026-01-25 21:21:06', '2026-01-27 20:05:10', NULL),
(10, 'Maskot Iblis Merah', '1769569536_Maskot Iblis Merah.jpg', 'Full Body', NULL, 200000, 0, '2026-01-25 21:22:10', '2026-01-27 20:05:36', NULL),
(11, 'Monster Garuda', '1769569563_Monster Garuda.jpg', 'Full Body', NULL, 500000, 0, '2026-01-25 21:24:03', '2026-01-27 20:06:03', NULL),
(12, 'Monster Burung Hantu', '1769569585_Monster Burung Hantu.jpg', 'Full Body', NULL, 300000, 0, '2026-01-25 21:24:48', '2026-01-27 20:06:25', NULL),
(13, 'Monster Engrang Kream', '1769569613_Monster Engrang Kream.jpg', 'Full Body', NULL, 300000, 0, '2026-01-25 21:26:18', '2026-01-27 20:06:53', NULL),
(14, 'Maskot Engrang Coklat', '1769569669_Maskot Engrang Coklat.jpg', 'Full Body', NULL, 300000, 0, '2026-01-25 21:26:48', '2026-01-27 20:07:49', NULL),
(15, 'Monster Engrang Merah', '1769569687_Monster Engrang Merah.jpg', 'Full Body', NULL, 300000, 0, '2026-01-25 21:27:19', '2026-01-27 20:08:07', NULL),
(16, 'Prajurit Wira', '1769569943_Prajurit Wira.jpeg', 'Kostum', 'Jumlah Kostum Prajurit ada 80 piece', 50000, 0, '2026-01-25 21:29:21', '2026-01-27 20:12:23', NULL),
(17, 'Prajurit Dyah', '1769570210_Prajurit Dyah.jpg', 'Kostum', 'Jumlah Kostum Prajurit ada 80 piece', 50000, 0, '2026-01-25 21:29:52', '2026-01-27 20:16:50', NULL),
(18, 'Bathara Guru', '1769570232_Bathara Guru.jpg', 'Ogoh-Ogoh', NULL, 1500000, 0, '2026-01-25 21:30:32', '2026-01-27 20:17:12', NULL),
(19, 'Prabu Siliwangi', '1769570569_Prabu Siliwangi.jpg', 'Ogoh-Ogoh', NULL, 1000000, 0, '2026-01-25 21:31:07', '2026-01-27 20:22:49', NULL),
(20, 'Watu Kelir', '1769570597_Watu Kelir.jpg', 'Ogoh-Ogoh', NULL, 1000000, 0, '2026-01-25 21:31:40', '2026-01-27 20:23:17', NULL),
(21, 'Gatotkaca', '1769570636_Gatotkaca.jpg', 'Ogoh-Ogoh', NULL, 1000000, 0, '2026-01-25 21:47:31', '2026-01-27 20:23:56', NULL),
(22, 'Ramayana', '1769570705_Ramayana.jpg', 'Ogoh-Ogoh', NULL, 1000000, 0, '2026-01-25 21:49:31', '2026-01-27 20:25:05', NULL),
(23, 'Bhuta Wana Raja', '1769738955_Bhuta Wana Raja.jpg', 'Ogoh-Ogoh', NULL, 1000000, 0, '2026-01-29 19:09:15', '2026-01-29 19:09:15', NULL);

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
(7, '2025_11_20_074247_add_pembayaran_to_sewas_table', 1),
(8, '2025_12_23_045837_add_photo_to_users_table', 1);

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
(1, 2, '087695446780', 'Salaman', '2026-01-27 01:57:29', '2026-01-27 01:57:29', NULL);

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

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('JCQWE838MSMvJLCuF6kYOANUjujggjDn2xCLEkZj', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM1Fud1pEaU5mbllvWUtwR3JHTEFZQXA2Zk1rY0RqSHRBckV5aENsRCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wZW55ZXdhYW4vcGlsaWgta29zdHVtIjtzOjU6InJvdXRlIjtzOjE2OiJwZW55ZXdhYW4uc2VsZWN0Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1769739542),
('KdSlaCTbCVq83AUAAkNF7JSMUnaIFW8wKwKpOebm', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZUV1azBpS0FheGUwS1FhUTlQcUNQdWM2SnA3VWxxUHB4VTZzbVE1NyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1769739793);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sewas`
--

CREATE TABLE `sewas` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_sewa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penyewa_id` bigint UNSIGNED NOT NULL,
  `kostum_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_sewa` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `total_biaya` int NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `status_bayar` tinyint(1) NOT NULL DEFAULT '0',
  `metode_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_rekening` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `denda` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `nama_bank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_ewallet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_ewallet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sewas`
--

INSERT INTO `sewas` (`id`, `kode_sewa`, `penyewa_id`, `kostum_id`, `tanggal_sewa`, `tanggal_kembali`, `total_biaya`, `catatan`, `status`, `status_bayar`, `metode_pembayaran`, `no_rekening`, `denda`, `created_at`, `updated_at`, `deleted_at`, `nama_bank`, `nama_ewallet`, `nomor_ewallet`) VALUES
(1, 'SEWA-20260127085748', 1, '\"[\\\"1\\\",\\\"2\\\",\\\"3\\\",\\\"4\\\",\\\"5\\\",\\\"6\\\",\\\"7\\\",\\\"8\\\"]\"', '2026-01-28', '2026-01-30', 2700000, NULL, 1, 1, 'tunai', NULL, 0, '2026-01-27 01:57:48', '2026-01-29 00:01:29', NULL, NULL, NULL, NULL);

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
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `photo`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', NULL, '$2y$12$T.IBRas2K5q0qHorxEYeeO.92CZLQ31fViTD9hC5hR8.PByi9T3IS', 'admin', NULL, '2026-01-25 21:09:15', '2026-01-29 00:02:16'),
(2, 'Nasrul', 'nasrul@gmail.com', NULL, '$2y$12$dFh1JWLS09TgfOOmsEQFVuXfxcrH6v67z0w8xd9JhivWU8410U4Z6', 'penyewa', NULL, '2026-01-27 01:57:17', '2026-01-27 01:57:17'),
(3, 'Ilham Nasrul', 'nasrulmuhammad@gmail.com', NULL, '$2y$12$0DOyq7BW4k25gYS/MP4KlOHB7FJxq68aF8iOWH3lvpj.Mo7S1x0nG', 'penyewa', NULL, '2026-01-29 02:02:00', '2026-01-29 02:02:00');

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
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `penyewas_user_id_foreign` (`user_id`);

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
  ADD KEY `sewas_penyewa_id_foreign` (`penyewa_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

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
