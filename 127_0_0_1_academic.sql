-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2020 at 04:53 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `academic`
--
CREATE DATABASE IF NOT EXISTS `academic` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `academic`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at`, `username`, `roles`, `avatar`, `status`) VALUES
(1, 'Saad Abdurrazaq', 'seadclark@gmail.com', NULL, '$2y$10$LnPjmJANC1NFhqV8a0.eQO.RbkufnV9FqxBoulzux3X4GCxdOwZ.u', NULL, 0, NULL, NULL, NULL, NULL, '2020-05-01 08:12:08', 'saadabdurrazaq', '[\"ADMIN\"]', 'avatars/aHbIVODoQHHUeTKC88AqD4eyhRrzsTSfCJSMVtyt.png', 'ACTIVE'),
(2, 'John Doe', 'john@doe.com', NULL, '$2y$10$LnPjmJANC1NFhqV8a0.eQO.RbkufnV9FqxBoulzux3X4GCxdOwZ.u', NULL, 0, NULL, NULL, NULL, NULL, '2020-04-29 22:23:33', 'johndoe', '[\"ADMIN\"]', 'avatars/JDQA1VqUJhpN7eqKuZl4LQY9jKvY20P9u3zWZ5ig.jpeg', 'INACTIVE'),
(3, 'John Smith', 'john@smith.com', NULL, '$2y$10$LnPjmJANC1NFhqV8a0.eQO.RbkufnV9FqxBoulzux3X4GCxdOwZ.u', NULL, 0, NULL, NULL, NULL, NULL, '2020-04-29 22:23:33', 'johnsmith', '[\"ADMIN\"]', 'avatars/LPrdp29HJP7ymd3M44IGFiuuuNhQzLmSsNwUc3GF.jpeg', 'INACTIVE'),
(4, 'Mark Miller', 'mark@miller.com', NULL, '$2y$10$LnPjmJANC1NFhqV8a0.eQO.RbkufnV9FqxBoulzux3X4GCxdOwZ.u', NULL, 0, NULL, NULL, '2020-05-04 15:50:00', NULL, '2020-05-04 15:50:00', 'markmiller', '[\"ADMIN\"]', 'avatars/bCalVSrtLFebS19j5SfIDDVPi3X3mjB4L6oBPRbU.jpeg', 'INACTIVE'),
(5, 'Jane Kendler', 'jane@kendler.com', NULL, '$2y$10$LnPjmJANC1NFhqV8a0.eQO.RbkufnV9FqxBoulzux3X4GCxdOwZ.u', NULL, 0, NULL, NULL, NULL, NULL, '2020-04-29 22:23:33', 'janekendler', '[\"ADMIN\"]', 'avatars/kry0qbRVGW4ladTmUolM3IwE7pfnNsgM3pKO1nQQ.jpeg', 'INACTIVE'),
(6, 'Kate Shine', 'kate@shine.com', NULL, '$2y$10$LnPjmJANC1NFhqV8a0.eQO.RbkufnV9FqxBoulzux3X4GCxdOwZ.u', NULL, 0, NULL, NULL, NULL, NULL, '2020-04-29 05:43:27', 'kateshine', '[\"ADMIN\"]', 'avatars/tLogrZMl8OVoowcEkosz19PDl1hJfr6W7ZcIvOVv.jpeg', 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(10) UNSIGNED NOT NULL,
  `original_name` text COLLATE utf8_unicode_ci NOT NULL,
  `filename` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `original_name`, `filename`, `created_at`, `updated_at`) VALUES
(813, 'image02.jpeg', 'image02-1d106.jpeg', '2020-04-11 22:22:49', '2020-04-11 22:22:49'),
(814, 'image03.jpeg', 'image03-563db.jpeg', '2020-04-11 22:22:49', '2020-04-11 22:22:49'),
(815, 'image01.jpeg', 'image01-eb699.jpeg', '2020-04-11 22:22:49', '2020-04-11 22:22:49'),
(816, 'image02.jpeg', 'image02-b51d4.jpeg', '2020-04-11 22:25:49', '2020-04-11 22:25:49'),
(817, 'image03.jpeg', 'image03-cba82.jpeg', '2020-04-11 22:25:49', '2020-04-11 22:25:49'),
(818, 'image01.jpeg', 'image01-cbd33.jpeg', '2020-04-11 22:25:49', '2020-04-11 22:25:49'),
(819, 'image02.jpeg', 'image02-dec14.jpeg', '2020-04-11 22:34:22', '2020-04-11 22:34:22'),
(820, 'image01.jpeg', 'image01-0c26f.jpeg', '2020-04-11 22:34:22', '2020-04-11 22:34:22'),
(821, 'image03.jpeg', 'image03-8783e.jpeg', '2020-04-11 22:34:22', '2020-04-11 22:34:22'),
(822, 'image03.jpeg', 'image03-ebafb.jpeg', '2020-04-11 22:44:08', '2020-04-11 22:44:08'),
(823, 'image01.jpeg', 'image01-4681f.jpeg', '2020-04-11 22:44:08', '2020-04-11 22:44:08'),
(824, 'image02.jpeg', 'image02-9ea0a.jpeg', '2020-04-11 22:44:08', '2020-04-11 22:44:08'),
(825, 'icon-rent.png', 'icon-rent.png', '2020-04-11 22:49:39', '2020-04-11 22:49:39'),
(826, 'hero_image.jpeg', 'heroimage.jpeg', '2020-04-11 22:49:39', '2020-04-11 22:49:39'),
(827, 'image02.jpeg', 'image02-e0689.jpeg', '2020-04-12 04:40:56', '2020-04-12 04:40:56'),
(828, 'image01.jpeg', 'image01-bb76c.jpeg', '2020-04-12 04:40:56', '2020-04-12 04:40:56'),
(829, 'image03.jpeg', 'image03-1be14.jpeg', '2020-04-12 04:40:56', '2020-04-12 04:40:56'),
(830, 'image03.jpeg', 'image03-e29cb.jpeg', '2020-04-12 05:00:31', '2020-04-12 05:00:31'),
(831, 'image01.jpeg', 'image01-3ab7b.jpeg', '2020-04-12 05:00:31', '2020-04-12 05:00:31'),
(832, 'image02.jpeg', 'image02-8075f.jpeg', '2020-04-12 05:00:31', '2020-04-12 05:00:31'),
(833, 'image01.jpeg', 'image01-751b0.jpeg', '2020-04-12 05:03:55', '2020-04-12 05:03:55'),
(834, 'image03.jpeg', 'image03-17715.jpeg', '2020-04-12 05:03:55', '2020-04-12 05:03:55'),
(835, 'image02.jpeg', 'image02-685bd.jpeg', '2020-04-12 05:03:55', '2020-04-12 05:03:55'),
(836, 'image03.jpeg', 'image03-4cd5b.jpeg', '2020-04-12 05:05:27', '2020-04-12 05:05:27'),
(837, 'image02.jpeg', 'image02-f4d44.jpeg', '2020-04-12 05:05:27', '2020-04-12 05:05:27'),
(838, 'image01.jpeg', 'image01-4585b.jpeg', '2020-04-12 05:05:27', '2020-04-12 05:05:27'),
(839, 'image02.jpeg', 'image02-ced51.jpeg', '2020-04-12 05:26:44', '2020-04-12 05:26:44'),
(840, 'image01.jpeg', 'image01-4f4bb.jpeg', '2020-04-12 05:26:44', '2020-04-12 05:26:44'),
(841, 'image03.jpeg', 'image03-1df5a.jpeg', '2020-04-12 05:26:45', '2020-04-12 05:26:45'),
(842, 'image02.jpeg', 'image02-695b2.jpeg', '2020-04-12 05:28:17', '2020-04-12 05:28:17'),
(843, 'image03.jpeg', 'image03-b14ab.jpeg', '2020-04-12 05:28:17', '2020-04-12 05:28:17'),
(844, 'image01.jpeg', 'image01-3944c.jpeg', '2020-04-12 05:28:17', '2020-04-12 05:28:17'),
(845, 'image03.jpeg', 'image03-ca1bb.jpeg', '2020-04-12 05:46:14', '2020-04-12 05:46:14'),
(846, 'image01.jpeg', 'image01-51f30.jpeg', '2020-04-12 05:46:14', '2020-04-12 05:46:14'),
(847, 'image02.jpeg', 'image02-e5f10.jpeg', '2020-04-12 05:46:14', '2020-04-12 05:46:14'),
(848, 'image01.jpeg', 'image01-dcb11.jpeg', '2020-04-12 05:47:21', '2020-04-12 05:47:21'),
(849, 'image03.jpeg', 'image03-be4e0.jpeg', '2020-04-12 05:47:21', '2020-04-12 05:47:21'),
(850, 'image02.jpeg', 'image02-7f909.jpeg', '2020-04-12 05:47:21', '2020-04-12 05:47:21'),
(851, 'image02.jpeg', 'image02-90a32.jpeg', '2020-04-12 06:09:44', '2020-04-12 06:09:44'),
(852, 'image03.jpeg', 'image03-0fbef.jpeg', '2020-04-12 06:09:44', '2020-04-12 06:09:44'),
(853, 'image01.jpeg', 'image01-86d71.jpeg', '2020-04-12 06:09:44', '2020-04-12 06:09:44'),
(854, 'image02.jpeg', 'image02-ef68a.jpeg', '2020-04-12 06:13:14', '2020-04-12 06:13:14'),
(855, 'image01.jpeg', 'image01-d302a.jpeg', '2020-04-12 06:13:14', '2020-04-12 06:13:14'),
(856, 'image03.jpeg', 'image03-c1ff9.jpeg', '2020-04-12 06:13:14', '2020-04-12 06:13:14'),
(857, 'image02.jpeg', 'image02-87a60.jpeg', '2020-04-12 06:22:19', '2020-04-12 06:22:19'),
(858, 'image03.jpeg', 'image03-d44c8.jpeg', '2020-04-12 06:22:19', '2020-04-12 06:22:19'),
(859, 'image01.jpeg', 'image01-1d1ee.jpeg', '2020-04-12 06:22:19', '2020-04-12 06:22:19'),
(860, 'image01.jpeg', 'image01-d20ba.jpeg', '2020-04-12 08:02:15', '2020-04-12 08:02:15'),
(861, 'image02.jpeg', 'image02-1199c.jpeg', '2020-04-12 08:02:15', '2020-04-12 08:02:15'),
(862, 'image03.jpeg', 'image03-5f38f.jpeg', '2020-04-12 08:02:15', '2020-04-12 08:02:15'),
(863, 'image02.jpeg', 'image02-eb0b0.jpeg', '2020-04-12 08:08:51', '2020-04-12 08:08:51'),
(864, 'image01.jpeg', 'image01-5e1f4.jpeg', '2020-04-12 08:08:51', '2020-04-12 08:08:51'),
(865, 'image03.jpeg', 'image03-3bd47.jpeg', '2020-04-12 08:08:51', '2020-04-12 08:08:51'),
(866, 'image02.jpeg', 'image02-b6691.jpeg', '2020-04-12 20:16:33', '2020-04-12 20:16:33'),
(867, 'image03.jpeg', 'image03-27b26.jpeg', '2020-04-12 20:16:33', '2020-04-12 20:16:33'),
(868, 'image01.jpeg', 'image01-4d201.jpeg', '2020-04-12 20:16:33', '2020-04-12 20:16:33'),
(869, 'image02.jpeg', 'image02-8c0c1.jpeg', '2020-04-12 20:17:18', '2020-04-12 20:17:18'),
(870, 'image01.jpeg', 'image01-b4a23.jpeg', '2020-04-12 20:17:18', '2020-04-12 20:17:18'),
(871, 'image03.jpeg', 'image03-3b3c0.jpeg', '2020-04-12 20:17:18', '2020-04-12 20:17:18'),
(872, 'image01.jpeg', 'image01-eed31.jpeg', '2020-04-12 20:48:46', '2020-04-12 20:48:46'),
(873, 'image02.jpeg', 'image02-abb55.jpeg', '2020-04-12 20:48:46', '2020-04-12 20:48:46'),
(874, 'image03.jpeg', 'image03-803bc.jpeg', '2020-04-12 20:48:46', '2020-04-12 20:48:46'),
(875, 'image02.jpeg', 'image02-2403a.jpeg', '2020-04-12 20:56:52', '2020-04-12 20:56:52'),
(876, 'image03.jpeg', 'image03-5aca3.jpeg', '2020-04-12 20:56:52', '2020-04-12 20:56:52'),
(877, 'image01.jpeg', 'image01-02071.jpeg', '2020-04-12 20:56:52', '2020-04-12 20:56:52'),
(878, 'image01.jpeg', 'image01-3cfe7.jpeg', '2020-04-12 21:09:49', '2020-04-12 21:09:49'),
(879, 'image03.jpeg', 'image03-2f886.jpeg', '2020-04-12 21:09:49', '2020-04-12 21:09:49'),
(880, 'image02.jpeg', 'image02-aa7dd.jpeg', '2020-04-12 21:09:49', '2020-04-12 21:09:49'),
(881, 'image03.jpeg', 'image03-595f7.jpeg', '2020-04-12 21:10:36', '2020-04-12 21:10:36'),
(882, 'image01.jpeg', 'image01-7247d.jpeg', '2020-04-12 21:10:36', '2020-04-12 21:10:36'),
(883, 'image02.jpeg', 'image02-f9a25.jpeg', '2020-04-12 21:10:36', '2020-04-12 21:10:36'),
(884, 'image01.jpeg', 'image01-887dd.jpeg', '2020-04-12 21:13:52', '2020-04-12 21:13:52'),
(885, 'image02.jpeg', 'image02-778de.jpeg', '2020-04-12 21:13:52', '2020-04-12 21:13:52'),
(886, 'image03.jpeg', 'image03-a3d56.jpeg', '2020-04-12 21:13:52', '2020-04-12 21:13:52'),
(887, 'image02.jpeg', 'image02-2af08.jpeg', '2020-04-12 21:16:50', '2020-04-12 21:16:50'),
(888, 'image03.jpeg', 'image03-f0dd3.jpeg', '2020-04-12 21:16:50', '2020-04-12 21:16:50'),
(889, 'image01.jpeg', 'image01-ac9bd.jpeg', '2020-04-12 21:16:50', '2020-04-12 21:16:50'),
(890, 'image03.jpeg', 'image03-91db9.jpeg', '2020-04-13 01:06:47', '2020-04-13 01:06:47'),
(891, 'image01.jpeg', 'image01-f2e08.jpeg', '2020-04-13 01:06:47', '2020-04-13 01:06:47'),
(892, 'image02.jpeg', 'image02-e3303.jpeg', '2020-04-13 01:06:47', '2020-04-13 01:06:47'),
(893, 'image01.jpeg', 'image01-7e475.jpeg', '2020-04-13 01:07:32', '2020-04-13 01:07:32'),
(894, 'image01.jpeg', 'image01-613d0.jpeg', '2020-04-13 01:10:59', '2020-04-13 01:10:59'),
(895, 'image03.jpeg', 'image03-147c3.jpeg', '2020-04-13 01:29:34', '2020-04-13 01:29:34'),
(896, 'image01.jpeg', 'image01-e5dbd.jpeg', '2020-04-13 01:29:34', '2020-04-13 01:29:34'),
(897, 'image02.jpeg', 'image02-cd2b5.jpeg', '2020-04-13 01:29:34', '2020-04-13 01:29:34'),
(898, 'image01.jpeg', 'image01-ba087.jpeg', '2020-04-13 01:33:13', '2020-04-13 01:33:13'),
(899, 'image02.jpeg', 'image02-36d27.jpeg', '2020-04-13 01:33:13', '2020-04-13 01:33:13'),
(900, 'image03.jpeg', 'image03-f9261.jpeg', '2020-04-13 01:33:13', '2020-04-13 01:33:13'),
(901, 'image03.jpeg', 'image03-76d12.jpeg', '2020-04-13 01:34:58', '2020-04-13 01:34:58'),
(902, 'image02.jpeg', 'image02-03d6a.jpeg', '2020-04-13 01:34:58', '2020-04-13 01:34:58'),
(903, 'image01.jpeg', 'image01-9045b.jpeg', '2020-04-13 01:34:58', '2020-04-13 01:34:58'),
(904, 'image03.jpeg', 'image03-b86d6.jpeg', '2020-04-13 01:36:26', '2020-04-13 01:36:26'),
(905, 'image02.jpeg', 'image02-879ac.jpeg', '2020-04-13 01:36:26', '2020-04-13 01:36:26'),
(906, 'image01.jpeg', 'image01-e9347.jpeg', '2020-04-13 01:36:26', '2020-04-13 01:36:26'),
(907, 'image02.jpeg', 'image02-ccd37.jpeg', '2020-04-13 01:42:03', '2020-04-13 01:42:03'),
(908, 'image03.jpeg', 'image03-1bf55.jpeg', '2020-04-13 01:42:03', '2020-04-13 01:42:03'),
(909, 'image01.jpeg', 'image01-307a4.jpeg', '2020-04-13 01:42:03', '2020-04-13 01:42:03'),
(910, 'image02.jpeg', 'image02-d9257.jpeg', '2020-04-13 02:08:20', '2020-04-13 02:08:20'),
(911, 'image01.jpeg', 'image01-c8d8a.jpeg', '2020-04-13 02:08:20', '2020-04-13 02:08:20'),
(912, 'image03.jpeg', 'image03-f8411.jpeg', '2020-04-13 02:08:20', '2020-04-13 02:08:20'),
(913, 'image01.jpeg', 'image01-29486.jpeg', '2020-04-13 02:09:12', '2020-04-13 02:09:12'),
(914, 'image03.jpeg', 'image03-9d56b.jpeg', '2020-04-13 02:09:12', '2020-04-13 02:09:12'),
(915, 'image02.jpeg', 'image02-400d3.jpeg', '2020-04-13 02:09:12', '2020-04-13 02:09:12'),
(916, 'icon-rent.png', 'icon-rent-8d5c4.png', '2020-04-13 02:11:04', '2020-04-13 02:11:04'),
(917, 'hero_image.jpeg', 'heroimage-8ecc3.jpeg', '2020-04-13 02:11:04', '2020-04-13 02:11:04'),
(918, 'image01.jpeg', 'image01-cfdd3.jpeg', '2020-04-13 02:35:22', '2020-04-13 02:35:22'),
(919, 'image02.jpeg', 'image02-1cc0c.jpeg', '2020-04-13 02:35:22', '2020-04-13 02:35:22'),
(920, 'image03.jpeg', 'image03-76091.jpeg', '2020-04-13 02:35:22', '2020-04-13 02:35:22'),
(921, 'image03.jpeg', 'image03-6612e.jpeg', '2020-04-13 02:43:07', '2020-04-13 02:43:07'),
(922, 'image02.jpeg', 'image02-165bb.jpeg', '2020-04-13 02:43:07', '2020-04-13 02:43:07'),
(923, 'image01.jpeg', 'image01-c0399.jpeg', '2020-04-13 02:43:07', '2020-04-13 02:43:07'),
(924, 'image01.jpeg', 'image01-5b41f.jpeg', '2020-04-14 06:28:46', '2020-04-14 06:28:46'),
(925, 'image03.jpeg', 'image03-92b65.jpeg', '2020-04-14 06:28:46', '2020-04-14 06:28:46'),
(926, 'image02.jpeg', 'image02-169c5.jpeg', '2020-04-14 06:28:46', '2020-04-14 06:28:46'),
(927, 'image03.jpeg', 'image03-cecf7.jpeg', '2020-04-14 16:18:36', '2020-04-14 16:18:36'),
(928, 'image01.jpeg', 'image01-d9738.jpeg', '2020-04-14 16:18:36', '2020-04-14 16:18:36'),
(929, 'image02.jpeg', 'image02-7bb61.jpeg', '2020-04-14 16:18:36', '2020-04-14 16:18:36'),
(930, 'image03.jpeg', 'image03-c2604.jpeg', '2020-04-14 16:39:54', '2020-04-14 16:39:54'),
(931, 'image02.jpeg', 'image02-679a6.jpeg', '2020-04-14 16:39:54', '2020-04-14 16:39:54'),
(932, 'image01.jpeg', 'image01-687a1.jpeg', '2020-04-14 16:39:54', '2020-04-14 16:39:54');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_07_20_034721_create_permission_tables', 1),
(4, '2019_07_20_034826_create_products_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\User', 2),
(2, 'App\\User', 1),
(2, 'App\\User', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'View Roles', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57'),
(2, 'Create Roles', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57'),
(3, 'Edit Roles', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57'),
(4, 'Delete Roles', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57'),
(5, 'View Users', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57'),
(6, 'Create Users', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57'),
(7, 'Edit User', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57'),
(8, 'Delete Users', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57'),
(9, 'Show User', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57'),
(10, 'Trash Users', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57'),
(11, 'Restore Users', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57'),
(12, 'Activate Users', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57'),
(13, 'Deactivate Users', 'web', '2020-04-15 21:33:57', '2020-04-15 21:33:57');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `detail`, `created_at`, `updated_at`) VALUES
(1, 'Baby Shark', 'dsfdsfdsfdsdsvdsvdsv', '2020-04-15 21:36:10', '2020-04-15 21:36:10');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2020-04-15 21:34:10', '2020-04-15 21:34:10'),
(2, 'Customer', 'web', '2020-04-15 21:37:14', '2020-04-15 21:37:14');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
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
(5, 2),
(6, 2);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `username`, `roles`, `address`, `phone`, `avatar`, `status`) VALUES
(5, 'Bejo Bajuri', 'comfoodtableofficial@gmail.com', NULL, '$2y$10$0LhTGOVCE8ArtBIfNihOweNN8TCmtfLYRMkqTISw8Y9ysWpFW5TQm', 'erK8b1jV0wVItqh1QKrFC1cWyFZzu018HGDn68DjTMEcUIjDbf1fJy9obF9k', '2020-04-03 19:34:44', '2020-04-03 19:34:44', 'bejobajuri', '[\"PELAJAR\"]', 'sdfsnefjkhewfhguewgfuyewgfegfygefgehfgjgafhjghjfgajfghj', '097253729912', 'avatars/ik4XGta4r2NkFQwV6VJ0lOgM2BfYvXPyNJMFDDkM.jpeg', 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Hardik Savani', 'admin@gmail.com', NULL, '$2y$10$0xuL2omfShrYy/YdoNUbs.xsvkK/nNNi3NrhHbBkclQGRbI4QtOFK', NULL, '2020-04-15 21:34:10', '2020-04-15 21:34:10'),
(2, 'Saad Abdurrazaq', 'seadclark@gmail.com', NULL, '$2y$10$5HNbQgF9TCaIZNjegnWPkeQsXAVCq0lRdVyRpFutXxBWO3Web8fEW', NULL, '2020-04-15 21:36:35', '2020-04-15 21:36:35'),
(3, 'Comfoodtable Official', 'comfoodtableofficial@gmail.com', NULL, '$2y$10$O.UEOajkhvzEvdGyGETSJOIasCpalAdWgSPiQH6pe6ajqb8OKGQAi', NULL, '2020-04-16 01:14:05', '2020-04-16 01:14:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD KEY `model_has_permissions_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD KEY `model_has_roles_role_id_foreign` (`role_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD KEY `role_has_permissions_permission_id_foreign` (`permission_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
