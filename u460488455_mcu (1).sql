-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 16, 2025 at 01:32 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u460488455_mcu`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent`, `created_at`, `updated_at`) VALUES
(1, 'Komponen Darah Lengkap', NULL, '2025-11-03 10:52:46', '2025-11-03 10:52:46'),
(2, 'Faal Hati', 'Kimia Darah', '2025-11-03 10:53:11', '2025-11-03 10:53:11'),
(3, 'Profil Gula', 'Kimia Darah', '2025-11-03 10:53:27', '2025-11-03 10:53:27'),
(4, 'Profil Lemak', 'Kimia Darah', '2025-11-03 10:53:40', '2025-11-03 10:53:40'),
(5, 'Faal Ginjal', 'Kimia Darah', '2025-11-03 10:54:02', '2025-11-03 10:54:02'),
(6, 'Urin Rutin', NULL, '2025-11-03 10:54:43', '2025-11-03 10:54:43'),
(7, 'Kesimpulan & Saran', NULL, '2025-11-03 10:55:00', '2025-11-03 10:55:00'),
(8, 'Jenis Pemeriksaan Penunjang Diagnostik', NULL, '2025-11-03 10:55:22', '2025-11-03 10:55:22'),
(9, 'Annual Tanda Vital', NULL, '2025-11-03 10:55:38', '2025-11-03 10:55:38');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2025_08_04_000001_create_categories_table', 1),
(7, '2025_08_04_000002_create_sub_categories_table', 1),
(8, '2025_08_04_000003_create_values_table', 1),
(9, '2025_08_04_009001_add_foreigns_to_sub_categories_table', 1),
(10, '2025_08_04_009002_add_foreigns_to_values_table', 1),
(11, '2025_08_15_071017_create_permission_tables', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(2, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 3),
(1, 'App\\Models\\User', 7),
(2, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 7),
(1, 'App\\Models\\User', 8);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'list categories', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(2, 'view categories', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(3, 'create categories', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(4, 'update categories', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(5, 'delete categories', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(6, 'list subcategories', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(7, 'view subcategories', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(8, 'create subcategories', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(9, 'update subcategories', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(10, 'delete subcategories', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(11, 'list values', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(12, 'view values', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(13, 'create values', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(14, 'update values', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(15, 'delete values', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(16, 'list roles', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(17, 'view roles', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(18, 'create roles', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(19, 'update roles', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(20, 'delete roles', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(21, 'list permissions', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(22, 'view permissions', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(23, 'create permissions', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(24, 'update permissions', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(25, 'delete permissions', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(26, 'list users', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(27, 'view users', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(28, 'create users', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(29, 'update users', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(30, 'delete users', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'user', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(2, 'super-admin', 'web', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(3, 'Dokter', 'web', '2025-11-03 10:52:09', '2025-11-03 10:52:09');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(11, 3),
(12, 3),
(13, 3),
(14, 3),
(15, 3);

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `category_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hemoglobin', '2025-11-03 10:58:52', '2025-11-03 10:58:52'),
(2, 1, 'Hematokrit', '2025-11-03 10:59:05', '2025-11-03 10:59:05'),
(3, 1, 'Eritrosit (Hematologi)', '2025-11-03 10:59:20', '2025-11-03 10:59:20'),
(4, 1, 'MCV', '2025-11-03 10:59:28', '2025-11-03 10:59:28'),
(5, 1, 'MCH', '2025-11-03 10:59:38', '2025-11-03 10:59:38'),
(6, 1, 'MCHC', '2025-11-03 10:59:50', '2025-11-03 10:59:50'),
(7, 1, 'Trombosit', '2025-11-03 11:00:00', '2025-11-03 11:00:00'),
(8, 1, 'Leukosit (Hematologi)', '2025-11-03 11:00:12', '2025-11-03 11:00:12'),
(9, 1, 'Basofil', '2025-11-03 11:00:52', '2025-11-03 11:00:52'),
(10, 1, 'Eosinofil', '2025-11-03 11:01:00', '2025-11-03 11:01:00'),
(11, 1, 'Neutrofil', '2025-11-03 11:01:16', '2025-11-03 11:01:16'),
(12, 1, 'Limfosit', '2025-11-03 11:01:25', '2025-11-03 11:01:25'),
(13, 1, 'Monosit', '2025-11-03 11:01:32', '2025-11-03 11:01:32'),
(14, 1, 'LED', '2025-11-03 11:01:42', '2025-11-03 11:01:42'),
(15, 6, 'Warna', '2025-11-03 11:02:18', '2025-11-03 11:02:18'),
(16, 6, 'Kejernihan', '2025-11-03 11:02:29', '2025-11-03 11:02:29'),
(17, 6, 'Berat Jenis', '2025-11-03 11:02:42', '2025-11-03 11:02:42'),
(18, 6, 'pH', '2025-11-03 11:02:59', '2025-11-03 11:02:59'),
(19, 6, 'Nitrit', '2025-11-03 11:03:07', '2025-11-03 11:03:07'),
(20, 6, 'Albumin', '2025-11-03 11:04:22', '2025-11-03 11:04:22'),
(21, 6, 'Glukosa', '2025-11-03 11:04:33', '2025-11-03 11:04:33'),
(22, 6, 'Keton', '2025-11-03 11:04:44', '2025-11-03 11:04:44'),
(23, 6, 'Urobilinogen', '2025-11-03 11:04:53', '2025-11-03 11:04:53'),
(24, 6, 'Bilirubin', '2025-11-03 11:05:05', '2025-11-03 11:05:05'),
(25, 6, 'Darah (Blood)', '2025-11-03 11:05:21', '2025-11-03 11:05:21'),
(26, 6, 'Leukosit (Urine)', '2025-11-03 11:05:34', '2025-11-03 11:05:34'),
(27, 6, 'Silinder Hyalin', '2025-11-03 11:05:50', '2025-11-03 11:05:50'),
(28, 6, 'Bakteri', '2025-11-03 11:06:01', '2025-11-03 11:06:01'),
(29, 6, 'Kristal Abnormal', '2025-11-03 11:06:11', '2025-11-03 11:06:11'),
(30, 6, 'Silinder Lain-lain', '2025-11-03 11:06:21', '2025-11-03 11:06:21'),
(31, 6, 'Epithel Gepeng', '2025-11-03 11:06:30', '2025-11-03 11:06:30'),
(32, 6, 'Epithel Transitional', '2025-11-03 11:06:45', '2025-11-03 11:06:45'),
(33, 6, 'Epithel Tubulus Ginjal', '2025-11-03 11:06:55', '2025-11-03 11:06:55'),
(34, 6, 'Kristal Normal', '2025-11-03 11:07:12', '2025-11-03 11:07:12'),
(35, 6, 'Lain-lain', '2025-11-03 11:07:21', '2025-11-03 11:07:21'),
(36, 6, 'Leukosit Esterase', '2025-11-03 11:07:37', '2025-11-03 11:07:37'),
(37, 6, 'Eritrosit (Urine)', '2025-11-03 11:07:47', '2025-11-03 11:07:47'),
(38, 7, 'Kesimpulan', '2025-11-03 11:08:09', '2025-11-03 11:08:09'),
(39, 7, 'Saran', '2025-11-03 11:08:16', '2025-11-03 11:08:16'),
(40, 8, 'ECG', '2025-11-03 11:13:56', '2025-11-03 11:13:56'),
(41, 8, 'Treadmill', '2025-11-03 11:14:05', '2025-11-03 11:14:05'),
(42, 8, 'PSA', '2025-11-03 11:14:13', '2025-11-03 11:14:13'),
(43, 8, 'APO-B', '2025-11-03 11:14:22', '2025-11-03 11:14:22'),
(44, 8, 'Spirometri', '2025-11-03 11:14:31', '2025-11-03 11:14:31'),
(45, 8, 'Foto Thorax', '2025-11-03 11:14:38', '2025-11-03 11:14:38'),
(46, 8, 'Echo', '2025-11-03 11:14:46', '2025-11-03 11:14:46'),
(47, 8, 'Audiometri', '2025-11-03 11:14:55', '2025-11-03 11:14:55'),
(48, 9, 'Tekanan darah Sistolik (mmHg)', '2025-11-03 11:16:29', '2025-11-03 11:16:29'),
(49, 9, 'Tekanan darah Diastolik (mmHg)', '2025-11-03 11:16:41', '2025-11-03 11:16:41'),
(50, 9, 'IMT (kg/m2)', '2025-11-03 11:16:51', '2025-11-03 11:16:51'),
(51, 9, 'Nadi (kali/menit)', '2025-11-03 11:17:01', '2025-11-03 11:17:01'),
(52, 9, 'Pernafasan (kali/menit)', '2025-11-03 11:17:10', '2025-11-03 11:17:10'),
(53, 9, 'Tinggi Badan (cm)', '2025-11-03 11:17:21', '2025-11-03 11:17:21'),
(54, 9, 'Berat Badan (kg)', '2025-11-03 11:17:31', '2025-11-03 11:17:31'),
(55, 9, 'Lingkar Perut (cm)', '2025-11-03 11:17:41', '2025-11-03 11:17:41'),
(56, 9, 'Kategori', '2025-11-03 11:18:39', '2025-11-03 11:18:39'),
(57, 2, 'GOT', '2025-11-03 13:28:26', '2025-11-03 13:28:26'),
(58, 2, 'GPT', '2025-11-03 13:28:33', '2025-11-03 13:28:33'),
(59, 3, 'Glukosa Puasa', '2025-11-03 13:32:59', '2025-11-03 13:32:59'),
(60, 3, 'Glukosa 2 Jam PP', '2025-11-03 13:33:12', '2025-11-03 13:33:12'),
(61, 3, 'HbA1c (NGSP)', '2025-11-03 13:33:30', '2025-11-03 13:33:30'),
(62, 4, 'Chol. Total', '2025-11-03 13:33:47', '2025-11-03 13:33:47'),
(63, 4, 'Chol. LDL Direk', '2025-11-03 13:33:57', '2025-11-03 13:33:57'),
(64, 4, 'Chol. HDL', '2025-11-03 13:34:05', '2025-11-03 13:34:05'),
(65, 4, 'Trigliserida', '2025-11-03 13:34:18', '2025-11-03 13:34:18'),
(66, 4, 'Ratio', '2025-11-03 13:34:27', '2025-11-03 13:34:27'),
(67, 5, 'Ureum', '2025-11-03 13:34:41', '2025-11-03 13:34:41'),
(68, 5, 'Kreatinin', '2025-11-03 13:34:52', '2025-11-03 13:34:52'),
(69, 5, 'Asam Urat', '2025-11-03 13:35:01', '2025-11-03 13:35:01'),
(70, 5, 'eLFG (CKD-EPI)', '2025-11-03 13:35:10', '2025-11-03 13:35:10'),
(71, 5, 'Urea N', '2025-11-03 13:35:20', '2025-11-03 13:35:20'),
(72, 9, 'Kode MCU', '2025-11-03 13:50:00', '2025-11-03 13:50:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `peran` smallint(6) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `kopeg` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `divisi` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `peran`, `dob`, `kopeg`, `alamat`, `divisi`, `gender`, `created_at`, `updated_at`) VALUES
(1, 'Darsirah Haryanto', 'admin@admin.com', '$2y$12$wnlGZNYaK9FjR4CvSRljaekNXUnX8JQSqGKXqDTAQXPWD4/rEaw26', 'a6Xu3vKAFY', 11395, '2004-04-18', 'Officia tenetur vel mollitia. Veniam totam animi hic. Incidunt voluptatem adipisci quae dolor sunt voluptas dolor omnis. Adipisci laborum voluptatem et officiis magnam. Enim voluptatem officia amet sint. Aut cupiditate qui nesciunt autem cumque.', 'Magni nemo beatae sit tempore exercitationem. Voluptatem cupiditate quas et labore rerum. Harum ad autem voluptatem aut at possimus magni.', 'Debitis officiis fuga nisi. Accusamus asperiores illo dolore vitae a dolores sit. Et qui nesciunt accusantium saepe est. Expedita minima et voluptate animi exercitationem sit ut.', 'Aspernatur nemo non voluptatem quia laudantium rerum. Et tempore libero est. Laboriosam et possimus consequuntur et ut voluptatem. Quia quod illum eum unde autem non.', '2025-11-03 10:27:57', '2025-11-03 10:27:57'),
(3, 'Admin DTIK', 'dti@jasatirta1.com', '$2y$12$Ek5y/1DA5AF22IxUWqmVe.2X2SV/Q.M.p9NBuGcLZME7BSM1f3oMC', NULL, 1, NULL, 'ADMINDTIK', NULL, 'Divisi Teknologi Informasi & Komunikasi', NULL, '2025-11-03 10:41:21', '2025-11-03 10:41:21'),
(4, 'keluarga admin 1', NULL, NULL, NULL, 2, NULL, 'ADMINDTIK', NULL, NULL, 'm', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(5, 'keluarga admin 2', NULL, NULL, NULL, 2, NULL, 'ADMINDTIK', NULL, NULL, 'f', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(6, 'keluarga admin 3', NULL, NULL, NULL, 2, NULL, 'ADMINDTIK', NULL, NULL, 'm', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(7, 'dr. Donny Septian Wibisono', 'donnyseptianwibisono@gmail.com', '$2y$12$TGtm0zDFROuHn0wdw7EwfeiPnEEaFr9jpsGWj0Y1t4O79.cQ0CmqG', NULL, 1, NULL, '9009210201', 'Jl. Wolter Monginsidi No. 17 Balikpapan Barat, Balikpapan', 'Divisi Human Capital', 'L', '2025-12-03 08:53:54', '2025-12-03 08:53:54'),
(8, 'M. Ainur Rofiq', 'aiinurofiq@jasatirta1.net', '$2y$12$lscYlVNpRXQVdpd1MjzzM.bi.QtXcRqWKJFWgmn9fdjZTUuTQRYxu', NULL, 1, NULL, '9708250102', 'Bronggalan Sawah 4-B/27-B RT 009 RW 008 Pacar Kembang, Tambak Sari, Surabaya', 'Divisi Teknologi Informasi & Komunikasi', 'L', '2025-12-09 15:30:29', '2025-12-09 15:30:29');

-- --------------------------------------------------------

--
-- Table structure for table `values`
--

CREATE TABLE `values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `sub_category_id` bigint(20) UNSIGNED NOT NULL,
  `nilai` longtext DEFAULT NULL,
  `tahun` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `values`
--

INSERT INTO `values` (`id`, `user_id`, `sub_category_id`, `nilai`, `tahun`, `created_at`, `updated_at`) VALUES
(1, 4, 1, '12.5', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(2, 4, 2, '38.3', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(3, 4, 3, '4.29', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(4, 4, 4, '89.3', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(5, 4, 5, '29.1', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(6, 4, 6, '32.6', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(7, 4, 7, '299', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(8, 4, 8, '5.6', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(9, 4, 9, '0.2', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(10, 4, 10, '0.9', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(11, 4, 11, '73.59999999999999', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(12, 4, 12, '19.4', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(13, 4, 13, '5.9', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(14, 4, 14, '26', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(15, 4, 57, '15', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(16, 4, 58, '4', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(17, 4, 59, '93', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(18, 4, 60, '93', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(19, 4, 61, '4.8', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(20, 4, 62, '152', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(21, 4, 63, '99', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(22, 4, 64, '49', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(23, 4, 65, '45', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(24, 4, 43, NULL, '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(25, 4, 71, '7.6', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(26, 4, 67, '16', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(27, 4, 68, '0.66', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(28, 4, 70, '114', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(29, 4, 69, '5', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(30, 4, 42, NULL, '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(31, 4, 15, 'Kuning muda', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(32, 4, 16, 'Jernih', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(33, 4, 17, '1.010', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(34, 4, 18, '6.0', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(35, 4, 36, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(36, 4, 19, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(37, 4, 20, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(38, 4, 21, 'Negatif (Normal)', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(39, 4, 22, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(40, 4, 23, 'Normal', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(41, 4, 24, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(42, 4, 25, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(43, 4, 37, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(44, 4, 26, '0 - 1', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(45, 4, 27, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(46, 4, 30, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(47, 4, 31, '0 - 1', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(48, 4, 32, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(49, 4, 33, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(50, 4, 28, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(51, 4, 34, 'Negatif ', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(52, 4, 29, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(53, 4, 35, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(54, 4, 40, 'Normal', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(55, 4, 45, 'Foto Thorax PA normal.', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(56, 4, 41, NULL, '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(57, 4, 46, 'Tidak Ada', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(58, 4, 38, '1. Serumen di telinga kiri', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(59, 4, 39, '1. Bersihkan serumen telinga secara teratur. Bila ada keluhan pendengaran, konsultasi ke dokter /spesialis THT.', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(60, 4, 47, 'Tidak Ada', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(61, 4, 44, 'Dalam batas normal', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(62, 4, 51, '74', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(63, 4, 52, '22', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(64, 4, 48, '110', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(65, 4, 49, '70', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(66, 4, 53, '166', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(67, 4, 54, '61.0', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(68, 4, 50, '22.1', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(69, 4, 55, '80', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(70, 4, 56, 'M1A', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(71, 4, 72, 'A7', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(72, 5, 1, '14.8', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(73, 5, 2, '45.9', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(74, 5, 3, '5.06', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(75, 5, 4, '90.7', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(76, 5, 5, '29.2', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(77, 5, 6, '32.2', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(78, 5, 7, '296', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(79, 5, 8, '4.9', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(80, 5, 9, '0.4', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(81, 5, 10, '4.3', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(82, 5, 11, '48.2', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(83, 5, 12, '38.9', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(84, 5, 13, '8.2', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(85, 5, 14, '31', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(86, 5, 57, '29', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(87, 5, 58, '26', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(88, 5, 59, '87', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(89, 5, 60, '121', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(90, 5, 61, '5.7', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(91, 5, 62, '207', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(92, 5, 63, '155', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(93, 5, 64, '48', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(94, 5, 65, '70', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(95, 5, 43, '107', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(96, 5, 71, '10.7', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(97, 5, 67, '23', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(98, 5, 68, '0.68', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(99, 5, 70, '101', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(100, 5, 69, '5.8', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(101, 5, 42, NULL, '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(102, 5, 15, 'Kuning', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(103, 5, 16, 'Agak keruh', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(104, 5, 17, '1.020', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(105, 5, 18, '6.0', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(106, 5, 36, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(107, 5, 19, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(108, 5, 20, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(109, 5, 21, 'Negatif (Normal)', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(110, 5, 22, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(111, 5, 23, 'Normal', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(112, 5, 24, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(113, 5, 25, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(114, 5, 37, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(115, 5, 26, '0 - 2', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(116, 5, 27, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(117, 5, 30, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(118, 5, 31, '5 - 10', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(119, 5, 32, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(120, 5, 33, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(121, 5, 28, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(122, 5, 34, 'Negatif ', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(123, 5, 29, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(124, 5, 35, 'Benang mukosa (+)', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(125, 5, 40, 'Normal', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(126, 5, 45, 'Foto Thorax PA normal.', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(127, 5, 41, NULL, '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(128, 5, 46, 'Tidak Ada', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(129, 5, 38, '1. Kadar kolesterol batas tinggi\n2. Obesitas sentral \n3. Myopia ODS, Presbiopia ODS \n4. Hipertensi grade 1 \n5. Caries gigi, sisa akar gigi ', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(130, 5, 39, '1. Diet rendah lemak dan konsultasi ke dokter\n2. Konsultasi ke dokter\n3. Konsultasi ke dokter perusahaan/dokter mata, untuk memastikan ukuran kacamata yang tepat.\n4. Konsultasi ke dokter perusahaan/ dokter keluarga untuk memastikan status tekanan darah dan penatalaksanaan selanjutnya\n5. Konsultasi ke dokter gigi ', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(131, 5, 47, 'Tidak Ada', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(132, 5, 44, 'Dalam batas normal', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(133, 5, 51, '80', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(134, 5, 52, '22', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(135, 5, 48, '140', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(136, 5, 49, '100', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(137, 5, 53, '152', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(138, 5, 54, '63', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(139, 5, 50, '27.3', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(140, 5, 55, '88', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(141, 5, 56, 'M3A', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(142, 5, 72, 'A5', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(143, 6, 1, '14.5', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(144, 6, 2, '43.8', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(145, 6, 3, '5.02', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(146, 6, 4, '87.3', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(147, 6, 5, '28.9', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(148, 6, 6, '33.1', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(149, 6, 7, '246', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(150, 6, 8, '6.1', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(151, 6, 9, '1.3', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(152, 6, 10, '6.4', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(153, 6, 11, '38.1', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(154, 6, 12, '47.4', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(155, 6, 13, '6.8', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(156, 6, 14, '5', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(157, 6, 57, '24', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(158, 6, 58, '15', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(159, 6, 59, '146', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(160, 6, 60, '276', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(161, 6, 61, '8.8', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(162, 6, 62, '248', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(163, 6, 63, '204', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(164, 6, 64, '43', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(165, 6, 65, '164', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(166, 6, 43, NULL, '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(167, 6, 71, '8', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(168, 6, 67, '17', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(169, 6, 68, '1.02', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(170, 6, 70, '84', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(171, 6, 69, '4.6', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(172, 6, 42, NULL, '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(173, 6, 15, 'Kuning muda', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(174, 6, 16, 'Jernih', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(175, 6, 17, '1.005', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(176, 6, 18, '7.0', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(177, 6, 36, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(178, 6, 19, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(179, 6, 20, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(180, 6, 21, 'Negatif (Normal)', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(181, 6, 22, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(182, 6, 23, 'Normal', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(183, 6, 24, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(184, 6, 25, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(185, 6, 37, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(186, 6, 26, '0 - 2', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(187, 6, 27, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(188, 6, 30, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(189, 6, 31, '0 - 2', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(190, 6, 32, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(191, 6, 33, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(192, 6, 28, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(193, 6, 34, 'Negatif ', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(194, 6, 29, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(195, 6, 35, 'Negatif', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(196, 6, 40, 'Normal', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(197, 6, 45, 'Foto Thorax PA normal.', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(198, 6, 41, NULL, '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(199, 6, 46, 'Tidak Ada', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(200, 6, 38, '1. Diabates Melitus, Obesitas sentral\n2. Hiperlipidemia\n3. Myopia+Presbiopia ODS\n', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(201, 6, 39, '1. Konsultasi ke dokter\n2. Diet rendah lemak dan konsultasi ke dokter\n3. Konsultasi ke dokter perusahaan/dokter mata, untuk memastikan perlu tidaknya penggunaan kacamata', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(202, 6, 47, 'Tidak Ada', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(203, 6, 44, 'Dalam batas normal', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(204, 6, 51, '76', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(205, 6, 52, '22', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(206, 6, 48, '110', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(207, 6, 49, '70', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(208, 6, 53, '171', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(209, 6, 54, '76.0', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(210, 6, 50, '26.0', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(211, 6, 55, '91', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(212, 6, 56, 'M3B', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18'),
(213, 6, 72, 'A7', '2025', '2025-11-03 13:50:18', '2025-11-03 13:50:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_categories_category_id_foreign` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `values`
--
ALTER TABLE `values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `values_user_id_foreign` (`user_id`),
  ADD KEY `values_sub_category_id_foreign` (`sub_category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `values`
--
ALTER TABLE `values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=214;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD CONSTRAINT `sub_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `values`
--
ALTER TABLE `values`
  ADD CONSTRAINT `values_sub_category_id_foreign` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `values_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
