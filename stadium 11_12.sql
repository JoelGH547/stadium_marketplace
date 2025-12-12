-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 03:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stadium`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password_hash`, `created_at`, `updated_at`) VALUES
(1, 'master', 'master@gmail.com', '$2y$10$VDOuCjqbHndy3Hzo.GdDGepEsDPD4mx/GmQth2zmoUxHIelkphbKW', '2025-11-11 13:51:09', NULL),
(2, 'Shirohana', 'jaejakeo@gmail.com', '$argon2id$v=19$m=16,t=2,p=1$RE1XTm1IRzV0b01KREJ0SA$PWNEkaHfuDG909BL2SWAPg', NULL, NULL),
(5, 'admin', 'admin@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$bjhxLm1UUU5xcjFQNkhVdQ$Wg7iDMbUtzfJLdGmMyXyDf7HqM9jIzRLDcX4QTgnJtU', '2025-11-18 04:06:26', '2025-11-19 03:38:52');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `stadium_id` int(11) UNSIGNED NOT NULL,
  `field_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) UNSIGNED NOT NULL,
  `booking_type` enum('hourly','daily') DEFAULT 'hourly',
  `booking_start_time` datetime NOT NULL,
  `booking_end_time` datetime NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `slip_image` varchar(255) DEFAULT NULL COMMENT 'รูปสลิปโอนเงิน',
  `is_viewed_by_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_id`, `stadium_id`, `field_id`, `vendor_id`, `booking_type`, `booking_start_time`, `booking_end_time`, `total_price`, `status`, `slip_image`, `is_viewed_by_admin`, `created_at`, `updated_at`) VALUES
(5, 8, 16, NULL, 2, 'hourly', '2025-11-18 11:10:00', '2025-11-18 14:10:00', 300.00, 'cancelled', NULL, 1, '2025-11-17 09:10:21', '2025-11-20 03:47:35'),
(6, 8, 17, NULL, 4, 'hourly', '2025-11-18 17:00:00', '2025-11-18 20:00:00', 120.00, 'cancelled', NULL, 1, '2025-11-18 07:58:41', '2025-11-20 03:47:39'),
(7, 8, 19, NULL, 7, 'hourly', '2025-11-20 12:43:00', '2025-11-20 15:43:00', 180.00, 'cancelled', NULL, 1, '2025-11-20 03:41:34', '2025-11-20 03:47:43'),
(8, 8, 18, NULL, 5, 'hourly', '2025-11-21 10:45:00', '2025-11-21 13:45:00', 240.00, 'paid', '1763610430_a94ebd38fdfaab65c40e.jpg', 1, '2025-11-20 03:45:56', '2025-11-20 03:48:13'),
(9, 10, 15, NULL, 5, 'hourly', '2025-11-22 15:49:00', '2025-11-22 19:49:00', 320.00, 'cancelled', NULL, 1, '2025-11-20 08:46:03', '2025-11-20 08:54:11'),
(10, 10, 15, NULL, 5, 'hourly', '2025-11-20 17:55:00', '2025-11-20 22:55:00', 400.00, 'cancelled', '1763628816_0c0157286f5b67ca9385.jpg', 1, '2025-11-20 08:53:28', '2025-11-24 08:27:12'),
(11, 5, 19, NULL, 7, 'hourly', '2025-11-28 11:34:00', '2025-11-28 14:34:00', 180.00, 'pending', '1763958912_ec7b71f81f2f29744de2.png', 1, '2025-11-24 04:34:23', '2025-11-27 07:29:33'),
(12, 5, 19, NULL, 7, 'hourly', '2025-11-19 18:11:00', '2025-11-19 21:11:00', 180.00, 'cancelled', '1763964821_cc4875292cc776a18108.png', 1, '2025-11-24 06:12:31', '2025-11-24 08:27:04'),
(13, 5, 15, NULL, 5, 'hourly', '2025-11-29 17:59:00', '2025-11-29 20:59:00', 240.00, 'paid', NULL, 1, '2025-11-24 06:55:05', '2025-11-24 09:10:53'),
(14, 5, 15, NULL, 5, 'hourly', '2025-11-28 15:11:00', '2025-11-28 17:11:00', 160.00, 'cancelled', NULL, 1, '2025-11-24 08:11:42', '2025-11-24 09:11:00'),
(15, 5, 15, NULL, 5, 'hourly', '2025-11-28 15:11:00', '2025-11-28 20:11:00', 400.00, 'pending', NULL, 0, '2025-11-24 08:22:18', '2025-11-24 08:22:18'),
(16, 5, 15, NULL, 5, 'hourly', '2025-11-27 19:26:00', '2025-11-27 22:26:00', 240.00, 'paid', '1763972621_9340b4b3281b19e0f8b1.png', 1, '2025-11-24 08:22:48', '2025-11-24 08:27:20'),
(17, 5, 15, NULL, 5, 'hourly', '2025-11-30 16:25:00', '2025-11-30 17:25:00', 80.00, 'cancelled', '1763972702_997925fdfa2087812c8d.png', 1, '2025-11-24 08:24:29', '2025-11-24 08:27:26'),
(18, 5, 15, NULL, 5, 'hourly', '2025-11-27 22:56:00', '2025-11-28 00:56:00', 160.00, 'pending', '1764086145_63a13b917bc5b58ec2f4.png', 0, '2025-11-25 15:54:20', '2025-11-25 15:55:45');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `emoji` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `emoji`, `created_at`, `updated_at`) VALUES
(1, 'บาสเกตบอล', '🏀', '2025-11-25 14:14:17', '2025-11-25 14:14:17'),
(2, 'วอลเลย์บอล', '🏐', '2025-11-25 14:14:17', '2025-11-25 14:14:17'),
(3, 'แบดมินตัน', '🏸', '2025-11-25 14:14:17', '2025-11-25 14:14:17'),
(4, 'สระว่ายน้ำ', '🏊', '2025-11-25 14:14:17', '2025-11-25 14:14:17'),
(5, 'ฟุตบอล', '⚽', '2025-11-25 14:14:17', '2025-11-25 14:14:17'),
(6, 'สนามวิ่ง', '🏃', '2025-11-25 14:14:17', '2025-11-25 14:14:17'),
(7, 'ปิงปอง', '🏓', '2025-11-25 14:14:17', '2025-11-25 14:14:17'),
(9, 'test', '🧪', '2025-12-08 02:52:37', '2025-12-08 02:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `username`, `email`, `password_hash`, `full_name`, `phone_number`, `created_at`, `updated_at`) VALUES
(5, 'custom', 'customer@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$MFRvUnNtYlRmeHJHL0dhWg$AAcgxuIECZ2q+wTvgaZ0nrgBCOSlFFWFlWEOaJd2jfM', 'ลูก ค้า', '0846013258', '2025-11-12 09:35:37', '2025-11-20 03:59:35'),
(6, 'Polranadanai', 'Polranadanai@gmail.com', '$2y$10$6PHB06ToquzAdQ47JgnvWeJO6e1NWx9IOnm.urA1r04Grz3HiUIim', '', '', '2025-11-13 04:00:05', '2025-11-13 04:00:05'),
(8, 'ลูกค้า', 'test3@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$TDhhUEk2N3Y1VzhGbmlyNA$Q1GKvSaqNeQhFBTciYWLaqiUBwlRZbSfPnyBlPOWy7Q', 'เทสส ดีย์', '0800000000', '2025-11-17 06:53:48', '2025-11-18 06:32:32'),
(10, 'cat', 'cat@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$N3lJQndzVFk0dVlrbjQyZQ$v5sfqdr7wCUm5TfseSUPQRiyA4sZ0cN2E8dVF+LFJoU', NULL, NULL, '2025-11-20 08:44:53', '2025-11-20 08:44:53');

-- --------------------------------------------------------

--
-- Table structure for table `facility_types`
--

CREATE TABLE `facility_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'ชื่อหมวดหมู่',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facility_types`
--

INSERT INTO `facility_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'สิ่งอำนวยความสะดวกพื้นฐาน', '2025-11-28 10:46:16', '2025-11-28 10:46:16'),
(2, 'อุปกรณ์กีฬา', '2025-11-28 10:46:16', '2025-11-28 10:46:16'),
(3, 'บริการเสริม', '2025-11-28 10:46:16', '2025-11-28 10:46:16'),
(4, 'อาหารและเครื่องดื่ม', '2025-11-28 10:46:16', '2025-11-28 10:46:16'),
(5, 'อื่นๆ', '2025-11-28 07:28:49', '2025-11-28 07:28:49'),
(7, 'ทดสอบ', '2025-12-08 02:52:18', '2025-12-08 02:52:18');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-11-10-030737', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1762744079, 1),
(2, '2025-11-10-031627', 'App\\Database\\Migrations\\CreateCategoriesTable', 'default', 'App', 1762744630, 2),
(3, '2025-11-10-032641', 'App\\Database\\Migrations\\CreateStadiumsTable', 'default', 'App', 1762745333, 3),
(4, '2025-11-11-030335', 'App\\Database\\Migrations\\CreateAdminsTable', 'default', 'App', 1762830513, 4),
(5, '2025-11-11-030520', 'App\\Database\\Migrations\\CreateVendorsTable', 'default', 'App', 1762830514, 4),
(6, '2025-11-11-030624', 'App\\Database\\Migrations\\CreateCustomersTable', 'default', 'App', 1762830514, 4),
(7, '2025-11-11-074748', 'App\\Database\\Migrations\\AddVendorIdToStadiums', 'default', 'App', 1762847966, 5),
(8, '2025-11-12-030548', 'App\\Database\\Migrations\\CreateBookingsTable', 'default', 'App', 1762916825, 6),
(9, '2025-11-14-064804', 'App\\Database\\Migrations\\AddStatusToVendors', 'default', 'App', 1763355155, 7),
(10, '2025-11-17-023814', 'App\\Database\\Migrations\\AddTrackingToBookings', 'default', 'App', 1764225360, 8),
(11, '2025-11-27-134000', 'App\\Database\\Migrations\\AddDailyBookingColumns', 'default', 'App', 1764225360, 8);

-- --------------------------------------------------------

--
-- Table structure for table `stadiums`
--

CREATE TABLE `stadiums` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) UNSIGNED NOT NULL,
  `vendor_id` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `open_time` time DEFAULT NULL,
  `close_time` time DEFAULT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `lat` varchar(50) DEFAULT NULL,
  `lng` varchar(50) DEFAULT NULL,
  `map_link` text DEFAULT NULL,
  `outside_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`outside_images`)),
  `inside_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`inside_images`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stadiums`
--

INSERT INTO `stadiums` (`id`, `name`, `description`, `category_id`, `vendor_id`, `created_at`, `updated_at`, `open_time`, `close_time`, `contact_email`, `contact_phone`, `province`, `address`, `lat`, `lng`, `map_link`, `outside_images`, `inside_images`) VALUES
(15, 'Jump Arena', 'หนามบาส', 1, 5, '2025-11-17 08:43:26', '2025-11-26 06:54:46', '09:00:00', '20:00:00', 'dorven@gmail.com', '0800000000', 'เชียงใหม่', '', '18.766276', '99.037869', '', '[\"outside_1763951549_1763951549_34470334d94301fd1aa0.jpeg\"]', '[\"inside_1763951549_1763951549_80b8869b272fda6a60ed.jpg\",\"inside_1763951549_1763951549_65a1c9effa78b74faa07.jpg\",\"inside_1763951549_1763951549_521cb65b3f8a00f32286.jpg\"]'),
(16, 'All Star Arena', 'สนามฟุตบอลหญ้าเทียม', 5, 2, '2025-11-17 08:58:39', '2025-12-08 02:32:04', '10:00:00', '23:00:00', 'dorven@gmail.com', '0612695225', 'เชียงใหม่', '133, ตำบอช้างม่อย 20 ถนน รัตนโกสินทร์ ตำบลป่าตัน อำเภอเมืองเชียงใหม่ เชียงใหม่ 50300', '18.801887', '98.996708', '', '[\"outside_1763951519_1763951519_c7221e137042eeccc06d.jpg\"]', '[\"inside_1763951519_1763951519_e438ad381b51f67622d1.jpg\",\"inside_1763951519_1763951519_3e477aeb1171bdc65e0b.jpg\",\"inside_1763951519_1763951519_605b9cbd6765721ab757.jpg\"]'),
(17, 'สนามวอลเลย์บอลสวนดอก', 'หอพักนักศึกษาแพทย์ 14 ชั้น คณะแพทยศาสตร์ มหาวิทยาลัยเชียงใหม่', 2, 4, '2025-11-17 09:04:15', '2025-12-03 02:39:13', '09:00:00', '22:00:00', 'test@gmail.com', '1234567891', 'เชียงใหม่', 'here', '18.791677', '98.969886', '', '[\"outside_1763951580_1763951580_a327944e76c33631226b.webp\"]', '[\"inside_1763951580_1763951580_a15d6f76285e14c46a5e.jpg\"]'),
(18, 'Looper Swimming Pool', 'เหมาะสำหรับ LGBTQ+', 4, 5, '2025-11-17 09:09:27', '2025-12-04 19:24:25', '10:00:00', '21:00:00', 'dorven@gmail.com', '0871772640', 'เชียงใหม่', '144/49 ซ ถนน อยู่เย็น เมือง เชียงใหม่ 50300', '18.813126', '98.965057', '', '[\"outside_1763951415_1763951415_d2c38967145090124070.png\"]', '[\"inside_1763951415_1763951415_5071c6ae4c5608654b6b.jpg\",\"inside_1763951415_1763951415_86ec2470be376d424644.jpg\",\"inside_1763951415_1763951415_dea07f367dc15cd57d67.jpg\"]'),
(19, 'CNX PINGPONG CLUB', 'ชมรมเทเบิลเทนนิสนครเชียงใหม่', 7, 7, '2025-11-18 04:33:45', '2025-12-03 02:40:15', '17:00:00', '20:00:00', 'admin@gmail.com', '0963852741', 'เชียงใหม่', 'RX2Q+484, Si Phum, Mueang Chiang Mai District, Chiang Mai 50200, Thailand', '18.800982', '98.987594', '', '[\"outside_1763951366_1763951366_7d3ed6fe73913590e3b1.jpg\"]', '[\"inside_1763951366_1763951366_9a8407b5d71b7f90fcdb.jpg\",\"inside_1763951366_1763951366_fa1f99b024604aa03756.jpg\",\"inside_1763951366_1763951366_18c63699e6ea4ba27466.jpg\"]'),
(29, 'test', 'test', 9, 4, '2025-12-09 07:50:09', '2025-12-09 07:50:09', '16:52:00', '15:50:00', 'test3@gmail.com', '0871772640', 'เชียงใหม่', '', '14.060390', '100.346634', '', '[\"outside_1765266609_1765266609_a7a4973a5befe5dd2e94.jpg\"]', '[\"inside_1765266609_1765266609_f70c3bcd20e4a5039202.jpg\"]');

-- --------------------------------------------------------

--
-- Table structure for table `stadium_facilities`
--

CREATE TABLE `stadium_facilities` (
  `id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `facility_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stadium_facilities`
--

INSERT INTO `stadium_facilities` (`id`, `field_id`, `facility_type_id`) VALUES
(42, 33, 2),
(37, 33, 3),
(27, 34, 1),
(28, 35, 4),
(45, 41, 4);

-- --------------------------------------------------------

--
-- Table structure for table `stadium_fields`
--

CREATE TABLE `stadium_fields` (
  `id` int(11) NOT NULL,
  `stadium_id` int(11) UNSIGNED NOT NULL COMMENT 'ผูกกับตาราง stadiums',
  `name` varchar(100) NOT NULL COMMENT 'ชื่อสนามย่อย เช่น สนาม 1, Court A',
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `price_daily` decimal(10,2) DEFAULT NULL,
  `status` enum('active','maintenance') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `outside_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'รูปปกสนามย่อย' CHECK (json_valid(`outside_images`)),
  `inside_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `stadium_fields`
--

INSERT INTO `stadium_fields` (`id`, `stadium_id`, `name`, `description`, `price`, `price_daily`, `status`, `created_at`, `updated_at`, `outside_images`, `inside_images`) VALUES
(27, 15, 'สนามย่อย กากี 1', 'สนามหญ้าเทียมคุณภาพดี Quis qui cupiditate recusandae nihil.', 1425.00, 11900.00, 'active', '2025-12-08 15:03:42', '2025-12-08 15:03:42', '[\"outside_1763951366_1763951366_7d3ed6fe73913590e3b1.jpg\"]', '[\"inside_1763951519_1763951519_e438ad381b51f67622d1.jpg\",\"inside_1763951549_1763951549_521cb65b3f8a00f32286.jpg\"]'),
(28, 15, 'สนามย่อย ชมพู 2', 'สนามหญ้าเทียมคุณภาพดี Eum necessitatibus ut est voluptate.', 959.00, 9728.00, 'active', '2025-12-08 15:03:42', '2025-12-08 15:03:42', '[\"outside_1763951549_1763951549_34470334d94301fd1aa0.jpeg\"]', '[\"inside_1763951519_1763951519_605b9cbd6765721ab757.jpg\",\"inside_1763951549_1763951549_521cb65b3f8a00f32286.jpg\",\"inside_1763951415_1763951415_5071c6ae4c5608654b6b.jpg\"]'),
(29, 16, 'สนามย่อย ม่วง 1', 'สนามหญ้าเทียมคุณภาพดี Accusantium et inventore et molestiae sint.', 906.00, 8130.00, 'active', '2025-12-08 15:03:42', '2025-12-08 15:03:42', '[\"outside_1763951549_1763951549_34470334d94301fd1aa0.jpeg\"]', '[\"inside_1763951549_1763951549_521cb65b3f8a00f32286.jpg\",\"inside_1763951519_1763951519_e438ad381b51f67622d1.jpg\"]'),
(30, 16, 'สนามย่อย ขาว 2', 'สนามหญ้าเทียมคุณภาพดี Quae qui alias sunt repellendus aperiam et eos.', 731.00, 9584.00, 'active', '2025-12-08 15:03:43', '2025-12-08 15:03:43', '[\"outside_1764578746_1764578746_082e0fd0d8a0ea9ecc1e.jpg\"]', '[\"inside_1763951415_1763951415_5071c6ae4c5608654b6b.jpg\",\"inside_1763951366_1763951366_18c63699e6ea4ba27466.jpg\",\"inside_1763951366_1763951366_18c63699e6ea4ba27466.jpg\"]'),
(32, 18, 'สนามย่อย น้ำเงิน 1', 'สนามหญ้าเทียมคุณภาพดี Autem doloremque iste atque earum nemo.', 1384.00, 9390.00, 'active', '2025-12-08 15:03:43', '2025-12-08 15:03:43', '[\"outside_1763951549_1763951549_34470334d94301fd1aa0.jpeg\"]', '[\"inside_1763951415_1763951415_5071c6ae4c5608654b6b.jpg\",\"inside_1763951549_1763951549_521cb65b3f8a00f32286.jpg\",\"inside_1763951549_1763951549_521cb65b3f8a00f32286.jpg\"]'),
(33, 19, 'สนามย่อย ชมพู 1', 'สนามหญ้าเทียมคุณภาพดี Aut totam laboriosam accusamus maxime dignissimos quia accusamus.', NULL, NULL, 'active', '2025-12-08 15:03:43', '2025-12-09 07:41:48', '[\"outside_1763951519_1763951519_c7221e137042eeccc06d.jpg\"]', '[\"inside_1763951519_1763951519_605b9cbd6765721ab757.jpg\",\"inside_1763951519_1763951519_e438ad381b51f67622d1.jpg\",\"inside_1763951415_1763951415_5071c6ae4c5608654b6b.jpg\"]'),
(34, 19, 'สนามย่อย เขียวอ่อน 2', 'สนามหญ้าเทียมคุณภาพดี Sunt quos consequatur impedit laborum nihil et.', 1418.00, 6894.00, 'active', '2025-12-08 15:03:43', '2025-12-08 15:03:43', '[\"outside_1763951519_1763951519_c7221e137042eeccc06d.jpg\"]', '[\"inside_1763951415_1763951415_5071c6ae4c5608654b6b.jpg\",\"inside_1763951519_1763951519_605b9cbd6765721ab757.jpg\",\"inside_1763951519_1763951519_e438ad381b51f67622d1.jpg\",\"inside_1763951519_1763951519_e438ad381b51f67622d1.jpg\"]'),
(35, 19, 'สนามย่อย น้ำเงิน 3', 'สนามหญ้าเทียมคุณภาพดี Sapiente dolorum rem molestiae laborum expedita sit.', 883.00, 6540.00, 'active', '2025-12-08 15:03:43', '2025-12-08 15:03:43', '[\"outside_1764578952_1764578952_6caf0bcbab25120115d6.jpg\"]', '[\"inside_1763951415_1763951415_5071c6ae4c5608654b6b.jpg\",\"inside_1763951549_1763951549_521cb65b3f8a00f32286.jpg\",\"inside_1763951415_1763951415_5071c6ae4c5608654b6b.jpg\"]'),
(41, 29, 'ทดสอบ1', 'adad', 45.00, 10.00, 'active', '2025-12-09 09:51:29', '2025-12-09 09:51:29', '[\"field_out_1765273889_1765273889_ba5b318399b0ca5f9bf7.jpg\"]', '[\"field_in_1765273889_1765273889_1208da4ce0b638a3317f.jpg\"]');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `vendor_name` varchar(255) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `bank_account` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `username`, `email`, `password_hash`, `vendor_name`, `lastname`, `phone_number`, `tax_id`, `bank_account`, `status`, `created_at`, `updated_at`, `birthday`, `province`) VALUES
(2, 'Joot Ball', 'vendor@gmail.com', '$2y$10$LE9COKa7VuvqZfOsJQDQL.GWlQVQNESE1VV1yUDksly2rGxD.5YaW', 'jootball', NULL, '0963852745', '12356749', '1658486', 'approved', '2025-11-11 09:13:20', '2025-11-19 06:42:21', NULL, NULL),
(4, 'test', 'test@gmail.com', '$2y$10$0PUqFQMEG/umItwZnEjw8OuTaWYuAtq8tN/n1w7IlcpXOsd6vWiFC', 'test', 'test', '0659871234', '4444444', '3219616', 'pending', '2025-11-14 06:40:50', '2025-11-19 06:53:59', '2025-11-14', 'กรุงเทพมหานคร'),
(5, 'venven', 'dorven@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$RFN3Snlibmp2Tkhwc1ZqRA$kD+dwmQ+vlFq+3e4Wqn+o1QHKF3YzRkBUMZSe6Ar8BY', 'Ven.co', NULL, '8756085055', '888788', '9874613261', 'approved', '2025-11-17 06:59:58', '2025-11-18 09:30:18', NULL, NULL),
(7, 'Vad', 'vadmin@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$SG5DNFptdGhySDNyaWZVTw$fRNMMZ8bT3txfG99lUMbytXauyzrjuXi8xWO9LLnor0', 'vad', NULL, '0876987843', '656581', '', 'approved', '2025-11-18 04:17:05', '2025-11-19 06:46:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_items`
--

CREATE TABLE `vendor_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `stadium_facility_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_items`
--

INSERT INTO `vendor_items` (`id`, `stadium_facility_id`, `name`, `description`, `price`, `unit`, `image`, `status`, `created_at`, `updated_at`) VALUES
(21, 45, 'ada', 'adad', 552.00, 'ttyu', 'item_1765273957_1765273957_fec7ddc4ef91c850b2ec.png', 'active', '2025-12-09 09:52:37', '2025-12-09 09:52:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_customer_id_foreign` (`customer_id`),
  ADD KEY `bookings_stadium_id_foreign` (`stadium_id`),
  ADD KEY `bookings_vendor_id_foreign` (`vendor_id`),
  ADD KEY `fk_booking_field` (`field_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `facility_types`
--
ALTER TABLE `facility_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stadiums`
--
ALTER TABLE `stadiums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stadiums_category_id_foreign` (`category_id`);

--
-- Indexes for table `stadium_facilities`
--
ALTER TABLE `stadium_facilities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_field_facility` (`field_id`,`facility_type_id`);

--
-- Indexes for table `stadium_fields`
--
ALTER TABLE `stadium_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_stadium_fields` (`stadium_id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendor_items`
--
ALTER TABLE `vendor_items`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `facility_types`
--
ALTER TABLE `facility_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `stadiums`
--
ALTER TABLE `stadiums`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `stadium_facilities`
--
ALTER TABLE `stadium_facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `stadium_fields`
--
ALTER TABLE `stadium_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vendor_items`
--
ALTER TABLE `vendor_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_stadium_id_foreign` FOREIGN KEY (`stadium_id`) REFERENCES `stadiums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_field` FOREIGN KEY (`field_id`) REFERENCES `stadium_fields` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stadium_fields`
--
ALTER TABLE `stadium_fields`
  ADD CONSTRAINT `fk_stadium_fields` FOREIGN KEY (`stadium_id`) REFERENCES `stadiums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
