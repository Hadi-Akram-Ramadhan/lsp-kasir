-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2025 at 05:21 AM
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
-- Database: `kasirdoy`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `created_at`) VALUES
(1, 1, 'login', 'User login ke sistem', '2025-04-05 10:59:41'),
(2, 1, 'create', 'Membuat data baru', '2025-04-05 10:59:41'),
(3, 1, 'delete', 'Menghapus data', '2025-04-05 10:59:41'),
(4, 5, 'delete', 'Menghapus data', '2025-04-05 10:59:41'),
(5, 5, 'create', 'Membuat data baru', '2025-04-05 10:59:41'),
(6, 5, 'logout', 'User logout dari sistem', '2025-04-05 10:59:41'),
(7, 5, 'update', 'Mengubah data', '2025-04-05 10:59:41'),
(8, 6, 'login', 'User login ke sistem', '2025-04-05 10:59:41'),
(9, 6, 'login', 'User login ke sistem', '2025-04-05 10:59:41'),
(10, 6, 'delete', 'Menghapus data', '2025-04-05 10:59:41'),
(11, 7, 'login', 'User login ke sistem', '2025-04-05 10:59:41'),
(12, 7, 'create', 'Membuat data baru', '2025-04-05 10:59:41'),
(13, 7, 'login', 'User login ke sistem', '2025-04-05 10:59:41'),
(14, 8, 'logout', 'User logout dari sistem', '2025-04-05 10:59:41'),
(15, 8, 'create', 'Membuat data baru', '2025-04-05 10:59:41'),
(16, 8, 'create', 'Membuat data baru', '2025-04-05 10:59:41'),
(17, 8, 'update', 'Mengubah data', '2025-04-05 10:59:41'),
(18, 9, 'update', 'Mengubah data', '2025-04-05 10:59:41'),
(19, 9, 'login', 'User login ke sistem', '2025-04-05 10:59:41'),
(20, 9, 'create', 'Membuat data baru', '2025-04-05 10:59:41'),
(21, 1, 'delete', 'Menghapus data', '2025-04-05 10:59:43'),
(22, 1, 'update', 'Mengubah data', '2025-04-05 10:59:43'),
(23, 1, 'logout', 'User logout dari sistem', '2025-04-05 10:59:43'),
(24, 1, 'create', 'Membuat data baru', '2025-04-05 10:59:43'),
(25, 1, 'login', 'User login ke sistem', '2025-04-05 10:59:43'),
(26, 5, 'create', 'Membuat data baru', '2025-04-05 10:59:43'),
(27, 5, 'update', 'Mengubah data', '2025-04-05 10:59:43'),
(28, 5, 'create', 'Membuat data baru', '2025-04-05 10:59:43'),
(29, 5, 'update', 'Mengubah data', '2025-04-05 10:59:43'),
(30, 5, 'delete', 'Menghapus data', '2025-04-05 10:59:43'),
(31, 6, 'create', 'Membuat data baru', '2025-04-05 10:59:43'),
(32, 6, 'update', 'Mengubah data', '2025-04-05 10:59:43'),
(33, 6, 'delete', 'Menghapus data', '2025-04-05 10:59:43'),
(34, 6, 'update', 'Mengubah data', '2025-04-05 10:59:43'),
(35, 6, 'logout', 'User logout dari sistem', '2025-04-05 10:59:43'),
(36, 7, 'logout', 'User logout dari sistem', '2025-04-05 10:59:43'),
(37, 7, 'login', 'User login ke sistem', '2025-04-05 10:59:43'),
(38, 7, 'update', 'Mengubah data', '2025-04-05 10:59:43'),
(39, 7, 'login', 'User login ke sistem', '2025-04-05 10:59:43'),
(40, 7, 'login', 'User login ke sistem', '2025-04-05 10:59:43'),
(41, 8, 'create', 'Membuat data baru', '2025-04-05 10:59:43'),
(42, 8, 'logout', 'User logout dari sistem', '2025-04-05 10:59:43'),
(43, 8, 'delete', 'Menghapus data', '2025-04-05 10:59:43'),
(44, 9, 'logout', 'User logout dari sistem', '2025-04-05 10:59:43'),
(45, 9, 'login', 'User login ke sistem', '2025-04-05 10:59:43'),
(46, 9, 'delete', 'Menghapus data', '2025-04-05 10:59:43'),
(47, 5, 'create', 'Memproses pembayaran Rp 23.000 untuk Meja C1 via cash', '2025-04-05 11:32:30'),
(48, 5, 'create', 'Memproses pembayaran Rp 95.000 untuk Meja C1 via card', '2025-04-05 11:32:44'),
(49, 7, 'update', 'Mengubah produk Ayam Bakar: stok dari 29 ke 291', '2025-04-05 11:33:29'),
(50, 7, 'create', 'Membuat order baru untuk Meja A2', '2025-04-05 11:34:48'),
(51, 7, 'update', 'Membatalkan order untuk Meja A2', '2025-04-05 11:38:16'),
(52, 7, 'update', 'Menyelesaikan order untuk Meja A3', '2025-04-05 11:38:17'),
(53, 5, 'create', 'Memproses pembayaran Rp 50.000 untuk Meja C2 via card', '2025-04-05 11:39:02'),
(54, 5, 'create', 'Memproses pembayaran Rp 195.000 untuk Meja B2 via cash', '2025-04-05 11:39:27'),
(55, 1, 'update', 'Mengubah produk Ayam Bakar: stok dari 7 ke 100', '2025-04-13 09:23:18'),
(56, 7, 'create', 'Menambahkan produk baru: Kondom Sutra (Rp 12.000.000, Stok: 80)', '2025-04-16 02:51:47'),
(57, 7, 'delete', 'Menghapus produk: Kondom Sutra', '2025-04-16 02:52:26'),
(58, 1, 'create', 'Menambahkan produk baru: Hadi Akram Ramadhan  (Rp 12.000.000, Stok: 12)', '2025-04-16 03:06:22'),
(59, 1, 'create', 'Menambahkan produk baru: Ayam Bakar Kecap1 (Rp 12.000.000, Stok: 12)', '2025-04-16 03:11:28'),
(60, 1, 'delete', 'Menghapus produk: Ayam Bakar Kecap1', '2025-04-16 03:11:34'),
(61, 1, 'delete', 'Menghapus produk: Hadi Akram Ramadhan ', '2025-04-16 03:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `cashier_shifts`
--

CREATE TABLE `cashier_shifts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `end_time` timestamp NULL DEFAULT NULL,
  `total_transactions` int(11) DEFAULT 0,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('active','closed') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cashier_shifts`
--

INSERT INTO `cashier_shifts` (`id`, `user_id`, `start_time`, `end_time`, `total_transactions`, `total_amount`, `status`) VALUES
(1, 5, '2025-03-30 05:59:41', '2025-03-30 13:59:41', 13, 740038.00, 'closed'),
(2, 5, '2025-04-01 05:59:41', '2025-04-01 11:59:41', 19, 1841309.00, 'closed'),
(3, 5, '2025-04-03 05:59:41', '2025-04-03 11:59:41', 6, 796056.00, 'closed'),
(4, 5, '2025-04-05 04:59:41', NULL, 2, 142502.00, 'active'),
(5, 5, '2025-03-30 05:59:43', '2025-03-30 13:59:43', 15, 1061160.00, 'closed'),
(6, 5, '2025-03-29 05:59:43', '2025-03-29 12:59:43', 7, 360675.00, 'closed'),
(7, 5, '2025-03-29 05:59:43', '2025-03-29 11:59:43', 19, 975859.00, 'closed'),
(8, 5, '2025-04-05 03:59:43', NULL, 10, 841980.00, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `type` enum('order','stock','system') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `message`, `is_read`, `created_at`) VALUES
(1, 'order', 'Pesanan baru dari Meja A1', 1, '2025-04-05 10:59:41'),
(2, 'order', 'Pesanan dari Meja B2 telah selesai', 1, '2025-04-05 10:59:41'),
(3, 'order', 'Pesanan dari Meja C3 dibatalkan', 1, '2025-04-05 10:59:41'),
(4, 'stock', 'Stok Nasi Goreng hampir habis', 1, '2025-04-05 10:59:41'),
(5, 'stock', 'Stok Ayam Bakar perlu ditambah', 0, '2025-04-05 10:59:41'),
(6, 'stock', 'Stok Es Teh sudah diisi ulang', 0, '2025-04-05 10:59:41'),
(7, 'system', 'Backup database berhasil', 1, '2025-04-05 10:59:41'),
(8, 'system', 'Update sistem selesai', 1, '2025-04-05 10:59:41'),
(9, 'system', 'Maintenance terjadwal besok', 1, '2025-04-05 10:59:41'),
(10, 'order', 'Pesanan baru dari Meja A1', 1, '2025-04-05 10:59:43'),
(11, 'order', 'Pesanan dari Meja B2 telah selesai', 1, '2025-04-05 10:59:43'),
(12, 'order', 'Pesanan dari Meja C3 dibatalkan', 1, '2025-04-05 10:59:43'),
(13, 'stock', 'Stok Nasi Goreng hampir habis', 0, '2025-04-05 10:59:43'),
(14, 'stock', 'Stok Ayam Bakar perlu ditambah', 1, '2025-04-05 10:59:43'),
(15, 'stock', 'Stok Es Teh sudah diisi ulang', 1, '2025-04-05 10:59:43'),
(16, 'system', 'Backup database berhasil', 0, '2025-04-05 10:59:43'),
(17, 'system', 'Update sistem selesai', 0, '2025-04-05 10:59:43'),
(18, 'system', 'Maintenance terjadwal besok', 0, '2025-04-05 10:59:43'),
(19, 'stock', 'Stok Ayam Bakar menipis (tersisa -277)', 0, '2025-04-05 11:37:12'),
(20, 'stock', 'Stok Ayam Bakar menipis (tersisa -277)', 1, '2025-04-05 11:38:07'),
(21, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-05 11:38:37'),
(22, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-05 12:34:01'),
(23, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-10 10:12:05'),
(24, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-10 10:12:38'),
(25, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-10 10:13:04'),
(26, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-13 09:22:50'),
(27, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-13 09:22:54'),
(28, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-13 09:22:56'),
(29, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-13 09:22:56'),
(30, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-13 09:22:57'),
(31, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-13 09:22:57'),
(32, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-13 09:22:58'),
(33, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-13 09:22:59'),
(34, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-13 09:23:02'),
(35, 'stock', 'Stok Ayam Bakar menipis (tersisa 7)', 1, '2025-04-13 09:23:05');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `table_id` int(11) DEFAULT NULL,
  `waiter_id` int(11) DEFAULT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `table_id`, `waiter_id`, `status`, `created_at`) VALUES
(1, 7, 7, 'completed', '2025-04-04 15:26:12'),
(2, 2, 8, 'completed', '2025-04-04 15:26:12'),
(3, 6, 8, 'completed', '2025-04-04 15:26:12'),
(4, 7, 9, 'completed', '2025-04-04 15:26:12'),
(5, 8, 8, 'completed', '2025-04-04 15:26:12'),
(6, 10, 7, 'completed', '2025-04-04 15:26:12'),
(7, 9, 7, 'completed', '2025-04-04 15:26:12'),
(8, 2, 9, 'completed', '2025-04-04 15:26:12'),
(9, 8, 9, 'completed', '2025-04-04 15:26:12'),
(10, 5, 8, 'completed', '2025-04-04 15:26:12'),
(11, 2, 9, 'pending', '2025-04-04 15:26:12'),
(12, 5, 9, 'pending', '2025-04-04 15:26:13'),
(13, 6, 7, 'completed', '2025-04-04 15:26:13'),
(14, 7, 7, 'completed', '2025-04-04 15:26:43'),
(15, 4, 7, 'cancelled', '2025-04-04 15:37:06'),
(16, 10, 8, 'completed', '2025-04-05 10:58:03'),
(17, 10, 7, 'completed', '2025-04-05 10:58:03'),
(18, 2, 7, 'completed', '2025-04-05 10:58:03'),
(19, 10, 7, 'completed', '2025-04-05 10:58:03'),
(20, 6, 7, 'completed', '2025-04-05 10:58:03'),
(21, 5, 7, 'completed', '2025-04-05 10:58:03'),
(22, 10, 7, 'completed', '2025-04-05 10:58:03'),
(23, 7, 8, 'completed', '2025-04-05 10:58:03'),
(24, 5, 9, 'completed', '2025-04-05 10:58:03'),
(25, 3, 7, 'completed', '2025-04-05 10:58:03'),
(26, 3, 9, 'pending', '2025-04-05 10:58:03'),
(27, 2, 8, 'pending', '2025-04-05 10:58:03'),
(28, 8, 8, 'pending', '2025-04-05 10:58:03'),
(29, 3, 7, 'completed', '2025-04-05 10:59:41'),
(30, 4, 7, 'completed', '2025-04-05 10:59:41'),
(31, 3, 9, 'completed', '2025-04-05 10:59:41'),
(32, 8, 8, 'completed', '2025-04-05 10:59:41'),
(33, 5, 7, 'completed', '2025-04-05 10:59:41'),
(34, 3, 7, 'completed', '2025-04-05 10:59:41'),
(35, 2, 7, 'completed', '2025-04-05 10:59:41'),
(36, 8, 9, 'completed', '2025-04-05 10:59:41'),
(37, 5, 8, 'completed', '2025-04-05 10:59:41'),
(38, 3, 7, 'completed', '2025-04-05 10:59:41'),
(39, 9, 8, 'completed', '2025-04-05 10:59:41'),
(40, 6, 9, 'pending', '2025-04-05 10:59:41'),
(41, 6, 8, 'completed', '2025-04-05 10:59:41'),
(42, 8, 8, 'completed', '2025-04-05 10:59:43'),
(43, 8, 9, 'completed', '2025-04-05 10:59:43'),
(44, 5, 8, 'completed', '2025-04-05 10:59:43'),
(45, 2, 9, 'completed', '2025-04-05 10:59:43'),
(46, 4, 7, 'completed', '2025-04-05 10:59:43'),
(47, 5, 9, 'completed', '2025-04-05 10:59:43'),
(48, 2, 8, 'completed', '2025-04-05 10:59:43'),
(49, 7, 8, 'completed', '2025-04-05 10:59:43'),
(50, 7, 7, 'completed', '2025-04-05 10:59:43'),
(51, 8, 7, 'completed', '2025-04-05 10:59:43'),
(52, 3, 7, 'completed', '2025-04-05 10:59:43'),
(53, 4, 7, 'completed', '2025-04-05 10:59:43'),
(54, 8, 8, 'completed', '2025-04-05 10:59:43'),
(55, 4, 7, 'completed', '2025-04-05 11:33:56'),
(56, 4, 7, 'cancelled', '2025-04-05 11:34:48');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
(1, 1, 4, 2, 35000.00, '2025-04-04 15:26:12'),
(2, 2, 5, 2, 30000.00, '2025-04-04 15:26:12'),
(3, 2, 6, 1, 5000.00, '2025-04-04 15:26:12'),
(4, 2, 7, 1, 7000.00, '2025-04-04 15:26:12'),
(5, 3, 3, 3, 23000.00, '2025-04-04 15:26:12'),
(6, 3, 8, 3, 15000.00, '2025-04-04 15:26:12'),
(7, 3, 9, 3, 45000.00, '2025-04-04 15:26:12'),
(8, 4, 3, 3, 23000.00, '2025-04-04 15:26:12'),
(9, 4, 5, 1, 30000.00, '2025-04-04 15:26:12'),
(10, 4, 7, 1, 7000.00, '2025-04-04 15:26:12'),
(11, 5, 2, 3, 25000.00, '2025-04-04 15:26:12'),
(12, 5, 5, 2, 30000.00, '2025-04-04 15:26:12'),
(13, 5, 6, 1, 5000.00, '2025-04-04 15:26:12'),
(14, 6, 4, 1, 35000.00, '2025-04-04 15:26:12'),
(15, 6, 6, 3, 5000.00, '2025-04-04 15:26:12'),
(16, 6, 7, 2, 7000.00, '2025-04-04 15:26:12'),
(17, 7, 2, 2, 25000.00, '2025-04-04 15:26:12'),
(18, 7, 3, 2, 23000.00, '2025-04-04 15:26:12'),
(19, 7, 6, 2, 5000.00, '2025-04-04 15:26:12'),
(20, 8, 4, 2, 35000.00, '2025-04-04 15:26:12'),
(21, 8, 8, 1, 15000.00, '2025-04-04 15:26:12'),
(22, 9, 2, 3, 25000.00, '2025-04-04 15:26:12'),
(23, 9, 6, 2, 5000.00, '2025-04-04 15:26:12'),
(24, 9, 8, 1, 15000.00, '2025-04-04 15:26:12'),
(25, 10, 4, 2, 35000.00, '2025-04-04 15:26:12'),
(26, 10, 8, 2, 15000.00, '2025-04-04 15:26:12'),
(27, 11, 6, 2, 5000.00, '2025-04-04 15:26:12'),
(28, 11, 7, 3, 7000.00, '2025-04-04 15:26:12'),
(29, 12, 3, 3, 23000.00, '2025-04-04 15:26:13'),
(30, 12, 5, 3, 30000.00, '2025-04-04 15:26:13'),
(31, 12, 8, 2, 15000.00, '2025-04-04 15:26:13'),
(32, 13, 3, 2, 23000.00, '2025-04-04 15:26:13'),
(33, 13, 4, 1, 35000.00, '2025-04-04 15:26:13'),
(34, 13, 5, 2, 30000.00, '2025-04-04 15:26:13'),
(35, 13, 6, 1, 5000.00, '2025-04-04 15:26:13'),
(36, 14, 4, 1, 35000.00, '2025-04-04 15:26:43'),
(37, 14, 7, 1, 7000.00, '2025-04-04 15:26:43'),
(38, 14, 6, 1, 5000.00, '2025-04-04 15:26:43'),
(39, 14, 8, 1, 15000.00, '2025-04-04 15:26:43'),
(40, 14, 3, 1, 23000.00, '2025-04-04 15:26:43'),
(41, 14, 2, 1, 25000.00, '2025-04-04 15:26:43'),
(42, 14, 5, 1, 30000.00, '2025-04-04 15:26:43'),
(43, 14, 9, 1, 45000.00, '2025-04-04 15:26:43'),
(44, 15, 4, 1, 35000.00, '2025-04-04 15:37:06'),
(45, 15, 7, 4, 7000.00, '2025-04-04 15:37:06'),
(46, 15, 6, 3, 5000.00, '2025-04-04 15:37:06'),
(47, 15, 8, 3, 15000.00, '2025-04-04 15:37:06'),
(48, 15, 9, 19, 45000.00, '2025-04-04 15:37:06'),
(49, 16, 4, 3, 35000.00, '2025-04-05 10:58:03'),
(50, 17, 9, 2, 45000.00, '2025-04-05 10:58:03'),
(51, 18, 2, 3, 25000.00, '2025-04-05 10:58:03'),
(52, 18, 3, 1, 23000.00, '2025-04-05 10:58:03'),
(53, 18, 9, 2, 45000.00, '2025-04-05 10:58:03'),
(54, 19, 3, 1, 23000.00, '2025-04-05 10:58:03'),
(55, 20, 4, 2, 35000.00, '2025-04-05 10:58:03'),
(56, 20, 5, 3, 30000.00, '2025-04-05 10:58:03'),
(57, 20, 7, 2, 7000.00, '2025-04-05 10:58:03'),
(58, 20, 8, 2, 15000.00, '2025-04-05 10:58:03'),
(59, 21, 5, 3, 30000.00, '2025-04-05 10:58:03'),
(60, 21, 9, 3, 45000.00, '2025-04-05 10:58:03'),
(61, 22, 6, 3, 5000.00, '2025-04-05 10:58:03'),
(62, 23, 7, 2, 7000.00, '2025-04-05 10:58:03'),
(63, 23, 8, 3, 15000.00, '2025-04-05 10:58:03'),
(64, 23, 9, 2, 45000.00, '2025-04-05 10:58:03'),
(65, 24, 5, 3, 30000.00, '2025-04-05 10:58:03'),
(66, 24, 7, 1, 7000.00, '2025-04-05 10:58:03'),
(67, 25, 5, 1, 30000.00, '2025-04-05 10:58:03'),
(68, 25, 6, 3, 5000.00, '2025-04-05 10:58:03'),
(69, 25, 7, 1, 7000.00, '2025-04-05 10:58:03'),
(70, 25, 9, 1, 45000.00, '2025-04-05 10:58:03'),
(71, 26, 3, 3, 23000.00, '2025-04-05 10:58:03'),
(72, 26, 4, 3, 35000.00, '2025-04-05 10:58:03'),
(73, 27, 7, 2, 7000.00, '2025-04-05 10:58:03'),
(74, 28, 2, 3, 25000.00, '2025-04-05 10:58:03'),
(75, 28, 4, 1, 35000.00, '2025-04-05 10:58:03'),
(76, 28, 5, 1, 30000.00, '2025-04-05 10:58:03'),
(77, 28, 8, 2, 15000.00, '2025-04-05 10:58:03'),
(78, 29, 2, 1, 25000.00, '2025-04-05 10:59:41'),
(79, 29, 3, 2, 23000.00, '2025-04-05 10:59:41'),
(80, 29, 6, 3, 5000.00, '2025-04-05 10:59:41'),
(81, 29, 9, 3, 45000.00, '2025-04-05 10:59:41'),
(82, 30, 3, 1, 23000.00, '2025-04-05 10:59:41'),
(83, 30, 7, 3, 7000.00, '2025-04-05 10:59:41'),
(84, 31, 9, 1, 45000.00, '2025-04-05 10:59:41'),
(85, 32, 4, 3, 35000.00, '2025-04-05 10:59:41'),
(86, 32, 5, 1, 30000.00, '2025-04-05 10:59:41'),
(87, 32, 8, 1, 15000.00, '2025-04-05 10:59:41'),
(88, 33, 2, 1, 25000.00, '2025-04-05 10:59:41'),
(89, 33, 5, 3, 30000.00, '2025-04-05 10:59:41'),
(90, 33, 7, 3, 7000.00, '2025-04-05 10:59:41'),
(91, 33, 8, 1, 15000.00, '2025-04-05 10:59:41'),
(92, 34, 2, 3, 25000.00, '2025-04-05 10:59:41'),
(93, 34, 6, 1, 5000.00, '2025-04-05 10:59:41'),
(94, 34, 7, 2, 7000.00, '2025-04-05 10:59:41'),
(95, 35, 8, 3, 15000.00, '2025-04-05 10:59:41'),
(96, 35, 9, 2, 45000.00, '2025-04-05 10:59:41'),
(97, 36, 3, 3, 23000.00, '2025-04-05 10:59:41'),
(98, 36, 5, 2, 30000.00, '2025-04-05 10:59:41'),
(99, 36, 7, 2, 7000.00, '2025-04-05 10:59:41'),
(100, 37, 3, 2, 23000.00, '2025-04-05 10:59:41'),
(101, 37, 6, 3, 5000.00, '2025-04-05 10:59:41'),
(102, 37, 8, 2, 15000.00, '2025-04-05 10:59:41'),
(103, 37, 9, 1, 45000.00, '2025-04-05 10:59:41'),
(104, 38, 2, 1, 25000.00, '2025-04-05 10:59:41'),
(105, 38, 4, 1, 35000.00, '2025-04-05 10:59:41'),
(106, 39, 5, 1, 30000.00, '2025-04-05 10:59:41'),
(107, 39, 6, 1, 5000.00, '2025-04-05 10:59:41'),
(108, 39, 8, 1, 15000.00, '2025-04-05 10:59:41'),
(109, 40, 4, 1, 35000.00, '2025-04-05 10:59:41'),
(110, 41, 3, 3, 23000.00, '2025-04-05 10:59:41'),
(111, 41, 5, 2, 30000.00, '2025-04-05 10:59:41'),
(112, 41, 7, 3, 7000.00, '2025-04-05 10:59:41'),
(113, 41, 9, 1, 45000.00, '2025-04-05 10:59:41'),
(114, 42, 2, 1, 25000.00, '2025-04-05 10:59:43'),
(115, 42, 3, 2, 23000.00, '2025-04-05 10:59:43'),
(116, 42, 4, 3, 35000.00, '2025-04-05 10:59:43'),
(117, 42, 6, 3, 5000.00, '2025-04-05 10:59:43'),
(118, 43, 2, 1, 25000.00, '2025-04-05 10:59:43'),
(119, 43, 9, 1, 45000.00, '2025-04-05 10:59:43'),
(120, 44, 6, 1, 5000.00, '2025-04-05 10:59:43'),
(121, 45, 4, 1, 35000.00, '2025-04-05 10:59:43'),
(122, 46, 9, 3, 45000.00, '2025-04-05 10:59:43'),
(123, 47, 2, 3, 25000.00, '2025-04-05 10:59:43'),
(124, 47, 5, 3, 30000.00, '2025-04-05 10:59:43'),
(125, 47, 7, 2, 7000.00, '2025-04-05 10:59:43'),
(126, 47, 8, 1, 15000.00, '2025-04-05 10:59:43'),
(127, 48, 4, 1, 35000.00, '2025-04-05 10:59:43'),
(128, 48, 9, 1, 45000.00, '2025-04-05 10:59:43'),
(129, 49, 5, 2, 30000.00, '2025-04-05 10:59:43'),
(130, 50, 9, 1, 45000.00, '2025-04-05 10:59:43'),
(131, 51, 5, 3, 30000.00, '2025-04-05 10:59:43'),
(132, 51, 8, 2, 15000.00, '2025-04-05 10:59:43'),
(133, 51, 9, 2, 45000.00, '2025-04-05 10:59:43'),
(134, 52, 4, 2, 35000.00, '2025-04-05 10:59:43'),
(135, 52, 6, 3, 5000.00, '2025-04-05 10:59:43'),
(136, 52, 8, 2, 15000.00, '2025-04-05 10:59:43'),
(137, 52, 9, 2, 45000.00, '2025-04-05 10:59:43'),
(138, 53, 3, 1, 23000.00, '2025-04-05 10:59:43'),
(139, 54, 5, 3, 30000.00, '2025-04-05 10:59:43'),
(140, 54, 6, 1, 5000.00, '2025-04-05 10:59:43'),
(141, 55, 4, 284, 35000.00, '2025-04-05 11:33:56'),
(142, 56, 4, 284, 35000.00, '2025-04-05 11:34:48');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `stock`, `created_at`) VALUES
(2, 'Nasi Goreng', 25000.00, 49, '2025-04-04 15:26:12'),
(3, 'Mie Goreng', 23000.00, 49, '2025-04-04 15:26:12'),
(4, 'Ayam Bakar', 35000.00, 100, '2025-04-04 15:26:12'),
(5, 'Sate Ayam', 30000.00, 39, '2025-04-04 15:26:12'),
(6, 'Es Teh', 5000.00, 99, '2025-04-04 15:26:12'),
(7, 'Es Jeruk', 7000.00, 99, '2025-04-04 15:26:12'),
(8, 'Juice Alpukat', 15000.00, 24, '2025-04-04 15:26:12'),
(9, 'Sop Iga', 45000.00, 19, '2025-04-04 15:26:12');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` int(11) NOT NULL,
  `table_number` varchar(10) NOT NULL,
  `status` enum('available','occupied') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `table_number`, `status`, `created_at`) VALUES
(2, 'A1', 'occupied', '2025-04-04 15:26:12'),
(3, 'A2', 'available', '2025-04-04 15:26:12'),
(4, 'A3', 'available', '2025-04-04 15:26:12'),
(5, 'B1', 'occupied', '2025-04-04 15:26:12'),
(6, 'B2', 'available', '2025-04-04 15:26:12'),
(7, 'B3', 'available', '2025-04-04 15:26:12'),
(8, 'C1', 'available', '2025-04-04 15:26:12'),
(9, 'C2', 'available', '2025-04-04 15:26:12'),
(10, 'C3', 'available', '2025-04-04 15:26:12'),
(11, 'F2', 'available', '2025-04-16 03:00:43');

-- --------------------------------------------------------

--
-- Table structure for table `table_reservations`
--

CREATE TABLE `table_reservations` (
  `id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `reservation_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `party_size` int(11) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_reservations`
--

INSERT INTO `table_reservations` (`id`, `table_id`, `customer_name`, `customer_phone`, `reservation_time`, `party_size`, `status`, `created_at`) VALUES
(1, 2, 'Siti Rahayu', '084567890123', '2025-04-08 05:59:41', 2, 'pending', '2025-04-05 10:59:41'),
(2, 3, 'Rudi Hartono', '084567890123', '2025-04-11 05:59:41', 4, 'cancelled', '2025-04-05 10:59:41'),
(3, 4, 'Ahmad Hidayat', '085678901234', '2025-04-10 05:59:41', 6, 'confirmed', '2025-04-05 10:59:41'),
(4, 5, 'Budi Santoso', '081234567890', '2025-04-08 05:59:41', 7, 'confirmed', '2025-04-05 10:59:41'),
(5, 5, 'Siti Rahayu', '084567890123', '2025-04-10 05:59:42', 3, 'pending', '2025-04-05 10:59:42'),
(6, 6, 'Ahmad Hidayat', '084567890123', '2025-04-06 05:59:42', 5, 'pending', '2025-04-05 10:59:42'),
(7, 7, 'Ahmad Hidayat', '085678901234', '2025-04-10 05:59:42', 6, 'confirmed', '2025-04-05 10:59:42'),
(8, 8, 'Siti Rahayu', '085678901234', '2025-04-06 05:59:42', 6, 'confirmed', '2025-04-05 10:59:42'),
(9, 9, 'Budi Santoso', '085678901234', '2025-04-08 05:59:42', 8, 'confirmed', '2025-04-05 10:59:42'),
(10, 10, 'Ahmad Hidayat', '083456789012', '2025-04-12 05:59:42', 6, 'pending', '2025-04-05 10:59:42'),
(11, 10, 'Siti Rahayu', '083456789012', '2025-04-08 05:59:42', 6, 'cancelled', '2025-04-05 10:59:42'),
(12, 2, 'Dewi Kusuma', '083456789012', '2025-04-08 05:59:43', 7, 'confirmed', '2025-04-05 10:59:43'),
(13, 2, 'Siti Rahayu', '082345678901', '2025-04-06 05:59:43', 5, 'confirmed', '2025-04-05 10:59:43'),
(14, 3, 'Budi Santoso', '082345678901', '2025-04-07 05:59:43', 2, 'pending', '2025-04-05 10:59:43'),
(15, 4, 'Budi Santoso', '085678901234', '2025-04-07 05:59:43', 3, 'cancelled', '2025-04-05 10:59:43'),
(16, 4, 'Rudi Hartono', '081234567890', '2025-04-08 05:59:43', 5, 'pending', '2025-04-05 10:59:43'),
(17, 5, 'Rudi Hartono', '083456789012', '2025-04-07 05:59:43', 5, 'pending', '2025-04-05 10:59:43'),
(18, 6, 'Dewi Kusuma', '084567890123', '2025-04-07 05:59:43', 8, 'cancelled', '2025-04-05 10:59:43'),
(19, 7, 'Siti Rahayu', '084567890123', '2025-04-06 05:59:43', 7, 'confirmed', '2025-04-05 10:59:43'),
(20, 8, 'Budi Santoso', '085678901234', '2025-04-12 05:59:43', 8, 'confirmed', '2025-04-05 10:59:43'),
(21, 9, 'Ahmad Hidayat', '081234567890', '2025-04-12 05:59:43', 2, 'pending', '2025-04-05 10:59:43'),
(22, 9, 'Dewi Kusuma', '082345678901', '2025-04-06 05:59:43', 6, 'cancelled', '2025-04-05 10:59:43'),
(23, 10, 'Dewi Kusuma', '083456789012', '2025-04-06 05:59:43', 6, 'confirmed', '2025-04-05 10:59:43'),
(24, 10, 'Siti Rahayu', '082345678901', '2025-04-06 05:59:43', 2, 'confirmed', '2025-04-05 10:59:43');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `cashier_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `order_id`, `cashier_id`, `total_amount`, `payment_method`, `created_at`) VALUES
(1, 1, 5, 70000.00, 'card', '2025-04-04 15:26:12'),
(2, 2, 5, 72000.00, 'card', '2025-04-04 15:26:12'),
(3, 3, 5, 249000.00, 'cash', '2025-04-04 15:26:12'),
(4, 4, 5, 106000.00, 'card', '2025-04-04 15:26:12'),
(5, 5, 5, 140000.00, 'card', '2025-04-04 15:26:12'),
(6, 6, 5, 64000.00, 'cash', '2025-04-04 15:26:12'),
(7, 7, 5, 106000.00, 'card', '2025-04-04 15:26:12'),
(8, 8, 5, 85000.00, 'card', '2025-04-04 15:26:12'),
(9, 9, 5, 100000.00, 'cash', '2025-04-04 15:26:12'),
(10, 10, 5, 100000.00, 'card', '2025-04-04 15:26:12'),
(11, 14, 5, 185000.00, 'card', '2025-04-04 15:27:16'),
(12, 13, 5, 146000.00, 'cash', '2025-04-04 15:31:45'),
(13, 16, NULL, 105000.00, 'cash', '2025-04-05 10:58:03'),
(14, 17, NULL, 90000.00, 'card', '2025-04-05 10:58:03'),
(15, 18, NULL, 188000.00, 'cash', '2025-04-05 10:58:03'),
(16, 19, NULL, 23000.00, 'card', '2025-04-05 10:58:03'),
(17, 20, NULL, 204000.00, 'cash', '2025-04-05 10:58:03'),
(18, 21, NULL, 225000.00, 'cash', '2025-04-05 10:58:03'),
(19, 22, NULL, 15000.00, '', '2025-04-05 10:58:03'),
(20, 23, NULL, 149000.00, 'cash', '2025-04-05 10:58:03'),
(21, 24, NULL, 97000.00, 'cash', '2025-04-05 10:58:03'),
(22, 25, NULL, 97000.00, 'cash', '2025-04-05 10:58:03'),
(23, 29, NULL, 221000.00, 'cash', '2025-04-05 10:59:41'),
(24, 30, NULL, 44000.00, 'card', '2025-04-05 10:59:41'),
(25, 31, NULL, 45000.00, 'cash', '2025-04-05 10:59:41'),
(26, 32, NULL, 150000.00, 'cash', '2025-04-05 10:59:41'),
(27, 33, NULL, 151000.00, 'cash', '2025-04-05 10:59:41'),
(28, 34, NULL, 94000.00, 'cash', '2025-04-05 10:59:41'),
(29, 35, NULL, 135000.00, 'cash', '2025-04-05 10:59:41'),
(30, 36, NULL, 143000.00, '', '2025-04-05 10:59:41'),
(31, 37, NULL, 136000.00, 'cash', '2025-04-05 10:59:41'),
(32, 38, NULL, 60000.00, 'cash', '2025-04-05 10:59:41'),
(33, 42, NULL, 191000.00, 'cash', '2025-04-05 10:59:43'),
(34, 43, NULL, 70000.00, 'cash', '2025-04-05 10:59:43'),
(35, 44, NULL, 5000.00, '', '2025-04-05 10:59:43'),
(36, 45, NULL, 35000.00, 'card', '2025-04-05 10:59:43'),
(37, 46, NULL, 135000.00, 'cash', '2025-04-05 10:59:43'),
(38, 47, NULL, 194000.00, 'cash', '2025-04-05 10:59:43'),
(39, 48, NULL, 80000.00, 'cash', '2025-04-05 10:59:43'),
(40, 49, NULL, 60000.00, 'card', '2025-04-05 10:59:43'),
(41, 50, NULL, 45000.00, 'cash', '2025-04-05 10:59:43'),
(42, 51, NULL, 210000.00, 'cash', '2025-04-05 10:59:43'),
(43, 52, 5, 205000.00, 'card', '2025-04-05 11:28:14'),
(44, 53, 5, 23000.00, 'cash', '2025-04-05 11:31:40'),
(45, 53, 5, 23000.00, 'cash', '2025-04-05 11:32:30'),
(46, 54, 5, 95000.00, 'card', '2025-04-05 11:32:44'),
(47, 39, 5, 50000.00, 'card', '2025-04-05 11:39:02'),
(48, 41, 5, 195000.00, 'cash', '2025-04-05 11:39:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('administrator','waiter','kasir','owner') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$FZC7CQiN82JmUs5Cj8OXfeTfH/gfrxf9ynOF.pYBH9wpPH2cRpQUy', 'administrator', '2025-04-04 15:03:04'),
(5, 'kasir', '$2y$10$QKtWzhTX9CgX0nUsre86DeqJ9tqRaCD1X6C5kuq9HgVugRD4hS4HO', 'kasir', '2025-04-04 15:17:14'),
(6, 'owner', '$2y$10$/xLxoYUd.tMnVncAvNnAkOfeM1h./heivGiMn4cfpld5QlCcJ9yRm', 'owner', '2025-04-04 15:17:14'),
(7, 'waiter', '$2y$10$vFmOJrLdJdTkeibx2c2B8.FV/2qpm.5eqqcnXF6L7sOCP8QbiiUn6', 'waiter', '2025-04-04 15:17:14'),
(8, 'waiter1', '$2y$10$a77Y31vkWPI6qWtSiSYFtO8tuAcJrP4tO9R.6z0vKAD3Hqrijy.La', 'waiter', '2025-04-04 15:26:12'),
(9, 'waiter2', '$2y$10$mxahV9Zskbr6gIAyqLUaUe9S00PD4gQ1Z815GkhHQ9rgzS9h2Pn6q', 'waiter', '2025-04-04 15:26:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cashier_shifts`
--
ALTER TABLE `cashier_shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `waiter_id` (`waiter_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `table_reservations`
--
ALTER TABLE `table_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `table_id` (`table_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `cashier_id` (`cashier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `cashier_shifts`
--
ALTER TABLE `cashier_shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `table_reservations`
--
ALTER TABLE `table_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `cashier_shifts`
--
ALTER TABLE `cashier_shifts`
  ADD CONSTRAINT `cashier_shifts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `table_reservations`
--
ALTER TABLE `table_reservations`
  ADD CONSTRAINT `table_reservations_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
