-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2025 at 07:48 AM
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
-- Database: `pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(12, 'weather', 'weather related post will show here', '2025-08-12 14:17:52', '2025-08-12 15:09:12'),
(13, 'fashion', 'Fashion related posts\r\n&nbsp;', '2025-08-12 18:41:16', '2025-08-21 01:15:36'),
(14, 'Lifestyle', '5 Powerful Morning Habits to Transform Your Day', '2025-08-12 18:41:29', '2025-08-21 13:33:17'),
(15, 'Technology', 'Top 7 Laravel Tips Every Developer Should Know', '2025-08-12 18:41:40', '2025-08-21 13:36:30'),
(16, 'Productivity', 'How to Stay Productive as a Remote Developer', '2025-08-12 18:41:51', '2025-08-21 13:38:39'),
(17, 'Food & Recipes', '10 Easy and Healthy Breakfast Recipes for Busy Mornings', '2025-08-12 18:42:02', '2025-08-21 13:44:24'),
(20, 'Travel', 'Top 7 Travel Destinations to Visit in 2025\r\n&nbsp;', '2025-08-13 20:33:10', '2025-08-21 13:46:25'),
(21, 'Lifestyle / Gardening', 'The Ultimate Guide to Indoor Plants for Beginners', '2025-08-20 15:48:39', '2025-08-21 13:54:20'),
(22, 'Health / Food', '10 Healthy Breakfast Ideas to Start Your Day Right', '2025-08-21 13:56:36', '2025-08-21 13:56:36'),
(23, 'Photography', 'Top 10 Tips for Beginner Photographers', '2025-08-21 14:21:22', '2025-08-21 14:21:22'),
(24, 'Sports / Cricket', 'Top 5 Cricket Matches That Changed the Game', '2025-08-21 14:23:45', '2025-08-21 14:23:45'),
(25, 'Entertainment / Movies', 'Top 10 Must-Watch Movies of the Decade', '2025-08-21 14:26:34', '2025-08-21 14:26:34'),
(26, 'News / Current Affairs', 'Devastating Floods Hit Pakistan: Thousands Affected', '2025-08-21 14:29:32', '2025-08-21 14:29:32');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `total_likes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `updated_at`) VALUES
(1, 'ali', 'ali@gmail.com', 'news', 'wrong news', '2025-08-21 20:44:35', '2025-08-21 20:44:35'),
(2, 'ahmad', 'islamabad', 'post interest', 'amazing ......feeling good after reading this', '2025-08-22 17:09:39', '2025-08-22 17:09:39');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(2, 'smith', 'smith@gmail.com', '03047951820', 'Lahore', '2025-09-01 13:33:11', '2025-09-01 13:33:11');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `methods`
--

CREATE TABLE `methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `methods`
--

INSERT INTO `methods` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'EasyPaisa', '2025-09-01 18:08:07', '2025-09-01 18:08:07'),
(3, 'JazzCash', '2025-09-01 18:08:18', '2025-09-01 18:08:18'),
(4, 'Raast Payment', '2025-09-01 18:08:31', '2025-09-01 18:08:40');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_08_10_103031_add_role_to_users_table', 2),
(5, '2025_08_11_055519_create_categories_table', 3),
(6, '2025_08_12_063810_create_posts_table', 4),
(7, '2025_08_13_072002_add_employee_ad_id_to_users', 5),
(8, '2025_08_13_072508_create_roles_table', 6),
(9, '2025_08_13_123437_create_permissions_table', 7),
(10, '2025_08_14_063744_create_role_permissions_table', 8),
(11, '2025_08_14_064845_create_role_permissions_table', 9),
(12, '2025_08_17_150043_create_role_users_table', 10),
(13, '2025_08_18_054952_create_permission_categories_table', 11),
(14, '2025_08_21_111036_create_news_letters_table', 12),
(15, '2025_08_21_132837_create_contacts_table', 13),
(16, '2025_08_22_054135_create_settings_table', 14),
(17, '2025_08_26_061758_create_comments_table', 15),
(18, '2025_08_26_075226_create_taxes_table', 16),
(19, '2025_08_26_101224_create_products_table', 17),
(20, '2025_08_26_124133_create_product_taxes_table', 18),
(21, '2025_08_27_055510_create_product_categories_table', 19),
(22, '2025_08_27_065335_add_category_id_to_products_table', 20),
(23, '2025_08_27_071915_change_category_id_foreign_key_on_products_table', 21),
(24, '2025_08_27_073751_add_stock_quantity_to_products_table', 22),
(25, '2025_08_27_080047_create_suppliers_table', 23),
(26, '2025_08_27_080505_create_suppliers_table', 24),
(27, '2025_08_27_104923_create_customers_table', 25),
(28, '2025_08_27_112417_create_purchases_table', 26),
(29, '2025_08_28_072445_create_purchase_items_table', 27),
(30, '2025_08_28_181136_create_stores_table', 28),
(31, '2025_08_29_060956_create_product_variants_table', 29),
(32, '2025_08_29_061239_create_product_variants_table', 30),
(33, '2025_08_29_093224_create_stock_movements_table', 31),
(34, '2025_08_29_130200_create_discounts_table', 32),
(35, '2025_09_01_060131_create_sales_table', 33),
(36, '2025_09_01_074649_create_sale_items_table', 34),
(37, '2025_09_01_102148_create_methods_table', 35),
(38, '2025_09_01_115018_create_payments_table', 36),
(39, '2025_09_22_060841_add_column_to_products_table', 37),
(40, '2025_09_22_061157_remove_deleted_at_from_products_table', 38),
(41, '2025_09_22_061426_add_column_to_products_table', 39);

-- --------------------------------------------------------

--
-- Table structure for table `news_letters`
--

CREATE TABLE `news_letters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_letters`
--

INSERT INTO `news_letters` (`id`, `email`, `created_at`, `updated_at`) VALUES
(1, 'usmanmaitla544@gmail.com', '2025-08-21 18:30:32', '2025-08-21 18:30:32'),
(7, 'sulemansikandar17@gmail.com', '2025-08-21 19:59:15', '2025-08-21 19:59:15'),
(8, 'user@gmail.com', '2025-08-21 20:13:20', '2025-08-21 20:13:20'),
(9, 'ahmad@gmail.com', '2025-08-21 20:25:49', '2025-08-21 20:25:49'),
(11, 'john@gmail.com', '2025-08-21 20:26:31', '2025-08-21 20:26:31');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('sulemansikandar17@gmail.com', '$2y$12$9huFweSP/FZIFyfwl1Eut.2sdVz1KSej2Cc5qHQ0QTV9BTl4wm5EO', '2025-09-18 17:38:51'),
('usmanmaitla544@gmail.com', '$2y$12$UfS6t39OntOI0m.Oh8h9jOJMGnHwLPJTIsyfUnMiB7vrMzt/J7XaK', '2025-08-21 20:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `method_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `route` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `permission_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `route`, `created_at`, `updated_at`, `permission_category_id`, `is_show`) VALUES
(52, 'Add User', 'admin-panel/users/add', '2025-08-18 17:33:47', '2025-08-29 17:45:35', 20, 0),
(53, 'Edit User', 'admin-panel/users/edit/{id}', '2025-08-18 17:34:19', '2025-08-29 17:45:49', 20, 0),
(54, 'Users', 'admin-panel/users/listing', '2025-08-18 17:34:47', '2025-08-29 17:58:06', 20, 1),
(55, 'Delete User', 'admin-panel/users/delete/{id}', '2025-08-18 17:35:13', '2025-08-29 17:46:19', 20, 0),
(56, 'Add Role', 'admin-panel/roles/add', '2025-08-18 17:36:00', '2025-08-29 17:46:32', 20, 0),
(57, 'Edit Role', 'admin-panel/roles/edit/{id}', '2025-08-18 17:36:27', '2025-08-29 17:46:45', 20, 0),
(58, 'Roles', 'admin-panel/roles/listing', '2025-08-18 17:36:51', '2025-08-29 17:58:55', 20, 1),
(59, 'Delete Role', 'admin-panel/roles/delete/{id}', '2025-08-18 17:37:24', '2025-08-20 14:01:00', 5, 0),
(60, 'Add Permission', 'admin-panel/permissions/add', '2025-08-18 17:38:14', '2025-08-29 17:47:25', 20, 0),
(61, 'Edit Permission', 'admin-panel/permissions/edit/{id}', '2025-08-18 17:38:37', '2025-08-29 17:47:36', 20, 0),
(62, 'Permissions', 'admin-panel/permissions/listing', '2025-08-18 17:38:58', '2025-08-29 18:01:24', 20, 1),
(63, 'Delete Permission', 'admin-panel/permissions/delete/{id}', '2025-08-18 17:39:19', '2025-08-29 17:48:05', 20, 0),
(64, 'View Dashboard', 'admin-panel/dashboard', '2025-08-18 20:18:16', '2025-09-02 13:56:22', 7, 1),
(65, 'Add Permission Categories', 'admin-panel/permission-categories/add', '2025-08-18 20:19:02', '2025-08-29 17:48:23', 20, 0),
(66, 'edit Permission_Categories', 'admin-panel/permission-categories/edit/{id}', '2025-08-18 20:19:44', '2025-08-29 17:48:41', 20, 0),
(67, 'Delete Permission Categories', 'admin-panel/permission-categories/delete/{id}', '2025-08-18 20:20:21', '2025-08-29 17:48:55', 20, 0),
(68, 'Permission Categories', 'admin-panel/permission-categories/listing', '2025-08-18 20:20:55', '2025-08-29 18:01:57', 20, 1),
(71, 'Update User', 'admin-panel/users/update/{id}', '2025-08-20 14:44:52', '2025-09-18 17:53:10', 20, 0),
(72, 'Update Role', 'admin-panel/roles/update/{id}', '2025-08-20 14:45:32', '2025-08-29 17:49:50', 20, 0),
(73, 'Update Permission', 'admin-panel/permissions/update/{id}', '2025-08-20 14:46:05', '2025-08-29 17:50:03', 20, 0),
(74, 'Update Permission Categories', 'admin-panel/permission-categories/update/{id}', '2025-08-20 14:46:44', '2025-08-29 17:50:21', 20, 0),
(75, 'View Setting', 'admin-panel/settings/listing', '2025-08-22 12:49:30', '2025-08-29 17:50:35', 20, 0),
(76, 'CMS', 'admin-panel/settings/add', '2025-08-22 13:02:52', '2025-08-29 18:02:19', 20, 1),
(77, 'Edit Setting', 'admin-panel/settings/edit/{id}', '2025-08-22 13:42:28', '2025-08-29 17:51:07', 20, 0),
(78, 'Update Setting', 'admin-panel/settings/update/{id}', '2025-08-22 13:48:16', '2025-08-29 17:51:26', 20, 0),
(79, 'Delete Setting', 'admin-panel/settings/delete/{id}', '2025-08-22 14:07:29', '2025-08-29 17:51:55', 20, 0),
(80, 'View Tax', 'admin-panel/tax/listing', '2025-08-26 15:04:30', '2025-08-29 17:53:05', 10, 1),
(81, 'Add New Tax', 'admin-panel/tax/add', '2025-08-26 15:21:20', '2025-08-29 17:53:53', 10, 1),
(82, 'Edit Tax', 'admin-panel/tax/edit/{id}', '2025-08-26 15:44:40', '2025-08-29 17:54:07', 10, 0),
(83, 'Update Tax', 'admin-panel/tax/update/{id}', '2025-08-26 16:06:19', '2025-08-29 17:54:22', 10, 0),
(84, 'Delete Tax', 'admin-panel/tax/delete', '2025-08-26 17:00:54', '2025-08-26 17:00:54', 10, 0),
(85, 'View Products', 'admin-panel/products/listing', '2025-08-26 18:37:13', '2025-08-26 18:37:13', 11, 1),
(86, 'Add Product', 'admin-panel/products/add', '2025-08-26 18:50:58', '2025-08-26 18:50:58', 11, 1),
(87, 'Edit Product', 'admin-panel/products/edit/{id}', '2025-08-26 20:11:55', '2025-08-26 20:11:55', 11, 0),
(88, 'Update Product', 'admin-panel/products/update/{id}', '2025-08-26 20:32:41', '2025-08-26 20:32:41', 11, 0),
(89, 'Delete Product', 'admin-panel/products/delete/{id}', '2025-08-26 20:37:30', '2025-08-26 20:37:30', 11, 0),
(90, 'View Category', 'admin-panel/categories/listing', '2025-08-27 13:09:54', '2025-08-27 13:09:54', 12, 1),
(91, 'Add Category', 'admin-panel/categories/add', '2025-08-27 13:17:22', '2025-08-27 13:17:22', 12, 1),
(92, 'Edit Category', 'admin-panel/categories/edit/{id}', '2025-08-27 13:35:10', '2025-08-27 13:35:10', 12, 0),
(93, 'Update Category', 'admin-panel/categories/update/{id}', '2025-08-27 13:35:40', '2025-08-27 13:35:40', 12, 0),
(94, 'Delete Category', 'admin-panel/categories/delete/{id}', '2025-08-27 13:36:09', '2025-08-27 13:36:09', 12, 0),
(95, 'View Supplier', 'admin-panel/supplier/listing', '2025-08-27 15:17:30', '2025-08-27 15:17:30', 13, 1),
(96, 'Add Supplier', 'admin-panel/supplier/add', '2025-08-27 15:23:49', '2025-08-27 15:23:49', 13, 1),
(97, 'Edit Supplier', 'admin-panel/supplier/edit/{id}', '2025-08-27 17:15:08', '2025-08-27 17:15:08', 13, 0),
(98, 'Update Supplier', 'admin-panel/supplier/update/{id}', '2025-08-27 17:15:34', '2025-08-27 17:15:34', 13, 0),
(99, 'Delete Supplier', 'admin-panel/supplier/delete/{id}', '2025-08-27 17:16:01', '2025-08-27 17:16:01', 13, 0),
(100, 'View Customer', 'admin-panel/customer/listing', '2025-08-27 17:56:24', '2025-08-27 17:56:24', 14, 1),
(101, 'Add Customer', 'admin-panel/customer/add', '2025-08-27 17:56:52', '2025-08-27 17:56:52', 14, 1),
(102, 'Edit Customer', 'admin-panel/customer/edit/{id}', '2025-08-27 17:57:26', '2025-08-27 17:57:26', 14, 0),
(103, 'Update Customer', 'admin-panel/customer/update/{id}', '2025-08-27 17:57:51', '2025-08-27 17:57:51', 14, 0),
(104, 'Delete Customer', 'admin-panel/customer/delete/{id}', '2025-08-27 17:58:17', '2025-08-27 17:58:17', 14, 0),
(105, 'View Purchase', 'admin-panel/purchase/listing', '2025-08-27 19:15:54', '2025-08-27 19:15:54', 15, 1),
(106, 'Add Purchase', 'admin-panel/purchase/add', '2025-08-27 19:16:23', '2025-08-27 19:16:23', 15, 1),
(107, 'Edit Purchase', 'admin-panel/purchase/edit/{id}', '2025-08-27 19:17:00', '2025-08-27 19:17:00', 15, 0),
(108, 'Update Purchase', 'admin-panel/purchase/update/{id}', '2025-08-27 19:17:29', '2025-08-27 19:17:29', 15, 0),
(109, 'Delete Purchase', 'admin-panel/purchase/delete/{id}', '2025-08-27 19:18:08', '2025-08-27 19:18:08', 15, 0),
(110, 'View Purchase Item', 'admin-panel/purchase-items/listing', '2025-08-28 14:53:25', '2025-08-28 14:53:25', 16, 1),
(111, 'Add Purchase Item', 'admin-panel/purchase-item/add', '2025-08-28 15:28:58', '2025-08-28 15:28:58', 16, 1),
(112, 'Edit Purchase Item', 'admin-panel/purchase-items/edit/{id}', '2025-08-28 17:29:01', '2025-08-28 17:29:01', 16, 0),
(113, 'Update Purchase Item', 'admin-panel/purchase-items/update/{id}', '2025-08-28 17:29:45', '2025-08-28 17:29:45', 16, 0),
(114, 'Delete Purchase Item', 'admin-panel/purchase-items/delete/{id}', '2025-08-28 17:30:16', '2025-08-28 17:30:16', 16, 0),
(115, 'View Stores', 'admin-panel/stores/listing', '2025-08-29 01:20:49', '2025-08-29 01:20:49', 17, 1),
(116, 'Add Store', 'admin-panel/stores/add', '2025-08-29 01:40:44', '2025-08-29 01:40:44', 17, 1),
(117, 'Edit Store', 'admin-panel/stores/edit/{id}', '2025-08-29 02:53:15', '2025-08-29 02:53:15', 17, 0),
(118, 'Update Store', 'admin-panel/stores/update/{id}', '2025-08-29 02:53:41', '2025-08-29 02:53:41', 17, 0),
(119, 'Delete Store', 'admin-panel/stores/delete/{id}', '2025-08-29 02:54:04', '2025-08-29 02:54:04', 17, 0),
(120, 'View Product Variant', 'admin-panel/variants/listing', '2025-08-29 13:23:45', '2025-08-29 13:23:45', 18, 1),
(121, 'Add Product Variant', 'admin-panel/variants/add', '2025-08-29 13:37:22', '2025-08-29 13:37:22', 18, 1),
(122, 'Edit Product Variant', 'admin-panel/variants/edit/{id}', '2025-08-29 14:13:28', '2025-08-29 14:13:28', 18, 0),
(123, 'Update Product Variant', 'admin-panel/variants/update/{id}', '2025-08-29 14:14:12', '2025-08-29 14:14:12', 18, 0),
(124, 'Delete Product Variant', 'admin-panel/variants/delete/{id}', '2025-08-29 14:14:41', '2025-08-29 14:14:41', 18, 0),
(125, 'View Stock Movement', 'admin-panel/stock/listing', '2025-08-29 16:45:54', '2025-08-29 16:45:54', 19, 1),
(126, 'Add', 'admin-panel/stock/add', '2025-08-29 18:16:06', '2025-08-29 18:16:06', 19, 1),
(127, 'Edit Stock', 'admin-panel/stock/edit/{id}', '2025-08-29 18:16:37', '2025-08-29 18:16:37', 19, 0),
(128, 'Update Stock', 'admin-panel/stock/update/{id}', '2025-08-29 18:17:06', '2025-08-29 18:17:06', 19, 0),
(129, 'Delete Stock', 'admin-panel/stock/delete/{id}', '2025-08-29 18:17:33', '2025-08-29 18:17:33', 19, 0),
(130, 'View Discounts', 'admin-panel/discounts/listing', '2025-08-29 20:10:59', '2025-08-29 20:10:59', 21, 1),
(131, 'Add Discount', 'admin-panel/discounts/add', '2025-08-29 20:26:36', '2025-08-29 20:26:36', 21, 1),
(132, 'Edit Discount', 'admin-panel/discounts/edit/{id}', '2025-08-29 20:28:25', '2025-08-29 20:28:25', 21, 0),
(133, 'Update Discount', 'admin-panel/discounts/update/{id}', '2025-08-29 20:28:50', '2025-08-29 20:28:50', 21, 0),
(134, 'Delete Discount', 'admin-panel/discounts/delete/{id}', '2025-08-29 20:29:14', '2025-08-29 20:29:14', 21, 0),
(135, 'View Sales', 'admin-panel/sales/listing', '2025-09-01 13:20:14', '2025-09-18 20:27:07', 22, 1),
(136, 'Add Sale', 'admin-panel/sales/add', '2025-09-01 13:22:54', '2025-09-18 20:19:20', 22, 0),
(137, 'Edit Sale', 'admin-panel/sales/edit/{id}', '2025-09-01 13:23:23', '2025-09-01 13:23:23', 22, 0),
(138, 'Update Sale', 'admin-panel/sales/update/{id}', '2025-09-01 13:23:50', '2025-09-01 13:23:50', 22, 0),
(139, 'Delete Sale', 'admin-panel/sales/delete/{id}', '2025-09-01 13:24:22', '2025-09-01 13:24:22', 22, 0),
(140, 'View Sale Item', 'admin-panel/sale-items/listing', '2025-09-01 14:57:56', '2025-09-01 14:57:56', 23, 1),
(141, 'Add Sale Item', 'admin-panel/sale-items/add', '2025-09-01 14:58:20', '2025-09-01 14:58:20', 23, 1),
(142, 'Edit Sale Item', 'admin-panel/sale-items/edit/{id}', '2025-09-01 14:58:49', '2025-09-01 14:58:49', 23, 0),
(143, 'Update Sale Item', 'admin-panel/sale-items/update/{id}', '2025-09-01 14:59:19', '2025-09-01 14:59:19', 23, 0),
(144, 'Delete Sale Item', 'admin-panel/sale-items/delete/{id}', '2025-09-01 14:59:47', '2025-09-01 14:59:47', 23, 0),
(145, 'View Method', 'admin-panel/payment-methods/listing', '2025-09-01 17:33:41', '2025-09-01 17:33:41', 24, 1),
(146, 'Add Method', 'admin-panel/payment-methods/add', '2025-09-01 17:34:09', '2025-09-01 17:34:09', 24, 1),
(147, 'Edit Method', 'admin-panel/payment-methods/edit/{id}', '2025-09-01 17:34:36', '2025-09-01 17:34:36', 24, 0),
(148, 'Update Methode', 'admin-panel/payment-methods/update/{id}', '2025-09-01 17:35:05', '2025-09-01 17:35:05', 24, 0),
(149, 'Delete Methode', 'admin-panel/payment-methods/delete/{id}', '2025-09-01 17:36:05', '2025-09-01 17:36:05', 24, 0),
(150, 'View Payment', 'admin-panel/payments/listing', '2025-09-01 18:57:20', '2025-09-01 18:57:20', 25, 1),
(151, 'Add Payment', 'admin-panel/payments/add', '2025-09-01 18:57:45', '2025-09-01 18:57:45', 25, 1),
(152, 'Edit Payment', 'admin-panel/payments/edit/{id}', '2025-09-01 18:58:15', '2025-09-01 18:58:15', 25, 0),
(153, 'Update Payment', 'admin-panel/payments/update/{id}', '2025-09-01 18:59:24', '2025-09-01 18:59:24', 25, 0),
(154, 'Delete Payment', 'admin-panel/payments/delete/{id}', '2025-09-01 18:59:56', '2025-09-01 18:59:56', 25, 0),
(155, 'View Pos', 'admin-panel/pos/sell', '2025-09-18 18:58:01', '2025-09-18 18:58:01', 22, 1),
(156, 'Add POS', 'admin-panel/pos/cart/add', '2025-09-18 18:58:36', '2025-09-18 20:20:35', 22, 0),
(157, 'Update POS', 'admin-panel/pos/cart/update', '2025-09-18 18:59:05', '2025-09-18 20:20:53', 22, 0),
(158, 'Remove', 'admin-panel/pos/cart/remove', '2025-09-18 18:59:30', '2025-09-18 20:21:15', 22, 0),
(159, 'CheckOut', 'admin-panel/pos/checkout', '2025-09-18 18:59:57', '2025-09-18 20:21:34', 22, 0),
(160, 'Invoice', 'admin-panel/pos/invoice/{id}', '2025-09-18 19:00:23', '2025-09-18 20:34:22', 22, 0),
(161, 'Print PDF', 'admin-panel/pos/pdf/{id}', '2025-09-19 13:28:48', '2025-09-19 13:28:48', 22, 0),
(162, 'Export Excel', 'admin-panel/products/export', '2025-09-21 18:38:24', '2025-09-21 18:38:24', 11, 0),
(163, 'Recycle', 'admin-panel/products/recycle', '2025-09-22 13:57:11', '2025-09-22 13:57:11', 11, 0),
(164, 'Restore', 'admin-panel/products/restore/{id}', '2025-09-22 14:10:10', '2025-09-22 14:10:10', 11, 0),
(165, 'Force Delete', 'admin-panel/products/force-delete/{id}', '2025-09-22 14:20:02', '2025-09-22 14:20:02', 11, 0);

-- --------------------------------------------------------

--
-- Table structure for table `permission_categories`
--

CREATE TABLE `permission_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `display_order` int(255) DEFAULT NULL,
  `icons` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_categories`
--

INSERT INTO `permission_categories` (`id`, `name`, `created_at`, `updated_at`, `display_order`, `icons`) VALUES
(4, 'Users', '2025-08-18 15:26:59', '2025-08-26 18:27:11', 2, 'la la-users'),
(5, 'Roles', '2025-08-18 15:27:11', '2025-08-26 18:27:22', 3, 'fas fa-user-shield'),
(6, 'Permissions', '2025-08-18 17:06:22', '2025-08-27 13:06:21', 7, 'la la-lock'),
(7, 'Dashboard', '2025-08-18 17:07:11', '2025-08-19 15:44:11', 1, 'la la-dashboard'),
(8, 'Permission_Categories', '2025-08-18 20:16:30', '2025-08-27 15:15:58', 9, 'la la-list'),
(9, 'CMS', '2025-08-22 12:44:52', '2025-08-29 17:00:34', 8, 'fas fa-cog'),
(10, 'Tax', '2025-08-26 15:03:26', '2025-08-26 18:27:33', 4, 'fas fa-file-invoice-dollar'),
(11, 'Products', '2025-08-26 18:28:46', '2025-09-02 13:39:55', 3, 'fas fa-cubes'),
(12, 'Product Category', '2025-08-27 13:08:51', '2025-09-02 13:40:07', 2, 'fa fa-folder-open'),
(13, 'Suppliers', '2025-08-27 15:16:49', '2025-08-27 15:16:49', 3, 'fa-people-carry'),
(14, 'Customer', '2025-08-27 17:55:47', '2025-08-27 17:55:47', 5, 'fa-id-card'),
(15, 'Purchase', '2025-08-27 19:13:07', '2025-08-27 19:13:07', 12, 'fa fa-shopping-cart'),
(16, 'Purchase Item', '2025-08-28 14:52:29', '2025-08-28 14:52:29', 13, 'fa-solid fa-sitemap'),
(17, 'Store', '2025-08-29 01:19:48', '2025-08-29 01:19:48', 14, 'fa-solid fa-shop'),
(18, 'Product Variant', '2025-08-29 13:23:00', '2025-09-02 13:40:23', 4, 'fa-brands fa-accessible-icon'),
(19, 'Stock Movement', '2025-08-29 16:45:20', '2025-08-29 16:45:20', 16, 'fa-brands fa-stack-exchange'),
(20, 'Setting', '2025-08-29 17:45:04', '2025-09-02 13:40:47', 18, 'fa-brands fa-centos'),
(21, 'Discount', '2025-08-29 20:10:15', '2025-08-29 20:10:15', 16, 'fas fa-percent'),
(22, 'Sale', '2025-09-01 13:19:40', '2025-09-01 13:19:40', 15, 'fas fa-bullhorn'),
(23, 'Sale Item', '2025-09-01 14:57:23', '2025-09-01 14:57:23', 17, 'fas fa-tags'),
(24, 'Payment Method', '2025-09-01 17:32:57', '2025-09-01 17:32:57', 18, 'fas fa-credit-card'),
(25, 'Payment', '2025-09-01 18:56:12', '2025-09-01 18:56:35', 18, 'fas fa-credit-card');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `category_id`, `title`, `image`, `content`, `status`, `created_at`, `updated_at`) VALUES
(4, 1, 12, 'Weather Update – Rain Expected Tomorrow', '1755151974.jpg', 'According to the latest forecast, scattered showers are expected in the city starting from tomorrow afternoon. Temperatures will range between 22&deg;C and 28&deg;C, with humidity levels around 70%. Residents are advised to carry umbrellas and avoid unnecessary travel during heavy rain spells. Tags: #WeatherUpdate, #RainAlert, #LocalNews', 'draft', '2025-08-14 13:12:54', '2025-08-22 18:44:13'),
(5, 1, 15, 'Top 7 Laravel Tips Every Developer Should Know', '1755758278.jpg', 'Laravel is one of the most popular PHP frameworks, known for its elegant syntax and developer-friendly tools. Whether you\'re a beginner or an experienced developer, here are seven essential tips to level up your Laravel projects:\r\n\r\n\r\nUse Eloquent relationships wisely &ndash; Reduce queries and speed up your app.\r\n\r\n\r\nLeverage Query Scopes &ndash; Make queries reusable and clean.\r\n\r\n\r\nApply Form Requests for validation &ndash; Keep your controllers slim.\r\n\r\n\r\nUse php artisan commands to boost development speed.\r\n\r\n\r\nCache smartly &ndash; Use Redis or Laravel&rsquo;s cache system to improve performance.\r\n\r\n\r\nAlways keep .env secure &ndash; Never commit it to version control.\r\n\r\n\r\nUtilize Laravel Debugbar &ndash; To understand what your app is really doing behind the scenes.\r\n\r\n\r\nThese tips not only improve performance but also keep your codebase maintainable and scalable.', 'draft', '2025-08-19 15:18:50', '2025-08-21 13:37:58'),
(6, 1, 14, '5 Powerful Morning Habits to Transform Your Day', '1755758139.jpg', 'Starting your day with the right habits can significantly boost your productivity, mood, and overall well-being. In this article, we explore five simple yet powerful morning habits you can adopt:\r\n\r\n\r\nWake up early &ndash; Gain control over your schedule.\r\n\r\n\r\nHydrate immediately &ndash; Kickstart your metabolism.\r\n\r\n\r\nPractice mindfulness &ndash; Set a calm tone for the day.\r\n\r\n\r\nReview your goals &ndash; Stay focused on what matters.\r\n\r\n\r\nMove your body &ndash; Get the energy flowing with light stretching or a walk.\r\n\r\n\r\nIncorporate these habits slowly and notice how your days begin to feel more intentional and productive.', 'draft', '2025-08-21 01:19:55', '2025-08-21 13:35:39'),
(7, 1, 16, 'How to Stay Productive as a Remote Developer', '1755758489.jpg', 'Remote development is now more common than ever. While working from home offers flexibility, it also comes with distractions. Here are some tried-and-tested ways to stay productive:\r\n✅ Create a dedicated workspace &ndash; Separate your work and personal environment.✅ Follow a fixed schedule &ndash; Maintain a start and end time like in-office jobs.✅ Use productivity tools &ndash; Tools like Trello, Notion, Slack, and VS Code Live Share can boost collaboration.✅ Take breaks &ndash; Follow the Pomodoro technique to avoid burnout.✅ Avoid multitasking &ndash; Focus on one task at a time to increase efficiency.✅ Overcommunicate with your team &ndash; Remote work needs clear and consistent communication.\r\nRemote work is here to stay, and with the right habits, you can thrive in it.', 'draft', '2025-08-21 13:41:29', '2025-08-21 13:41:29'),
(8, 1, 15, 'The Future of Artificial Intelligence in Everyday Life', '1755758598.jpg', 'Artificial Intelligence (AI) is no longer just a buzzword&mdash;it\'s becoming part of our daily lives. From smartphones to smart homes, AI is shaping how we live, work, and communicate.\r\n🤖 Voice Assistants like Siri, Alexa, and Google Assistant help us manage schedules, play music, and control devices.🛒 E-commerce platforms use AI for product recommendations based on user behavior.🚗 Self-driving cars are being tested worldwide, aiming to reduce human error on roads.🏥 Healthcare is benefiting from AI diagnostics and robotic surgeries.📸 Photo editing apps now include AI-based enhancements and object removals.🎓 Education platforms personalize content delivery using machine learning.\r\nAs AI continues to evolve, ethical use, data privacy, and human oversight must remain key concerns.', 'draft', '2025-08-21 13:43:18', '2025-08-21 13:43:18'),
(9, 1, 17, '10 Easy and Healthy Breakfast Recipes for Busy Mornings', '1755758742.jpg', 'Breakfast is the most important meal of the day, but busy mornings can make it easy to skip. Here are 10 quick, nutritious, and delicious breakfast ideas to kickstart your day:\r\n\r\n\r\nOvernight Oats &ndash; Mix oats, milk, chia seeds, and your favorite fruits the night before.\r\n\r\n\r\nAvocado Toast &ndash; Whole-grain toast with mashed avocado, a sprinkle of salt, and chili flakes.\r\n\r\n\r\nGreek Yogurt Parfait &ndash; Layer Greek yogurt, granola, and fresh berries.\r\n\r\n\r\nSmoothie Bowl &ndash; Blend fruits with milk or yogurt, and top with nuts and seeds.\r\n\r\n\r\nBanana Pancakes &ndash; Mash bananas with eggs for a flourless pancake option.\r\n\r\n\r\nEgg Muffins &ndash; Bake eggs with veggies in muffin tins for a grab-and-go meal.\r\n\r\n\r\nPeanut Butter Banana Toast &ndash; A classic with a protein boost.\r\n\r\n\r\nChia Pudding &ndash; Chia seeds soaked in almond milk overnight, topped with fruits.\r\n\r\n\r\nBreakfast Burrito &ndash; Whole-wheat tortilla with scrambled eggs, veggies, and cheese.\r\n\r\n\r\nFruit &amp; Nut Smoothie &ndash; Blend fruits, nuts, and seeds for a nutrient-rich drink.\r\n\r\n\r\nThese options are simple, healthy, and will keep you energized all morning!', 'draft', '2025-08-21 13:45:42', '2025-08-21 13:45:42'),
(10, 1, 20, 'Top 7 Travel Destinations to Visit in 2025', '1755758884.jpg', 'Planning your next adventure? Here are 7 amazing travel destinations you should consider visiting in 2025:\r\n\r\n\r\nKyoto, Japan &ndash; Explore ancient temples, beautiful gardens, and traditional tea houses.\r\n\r\n\r\nLisbon, Portugal &ndash; A vibrant city with colorful streets, delicious seafood, and rich history.\r\n\r\n\r\nBanff, Canada &ndash; Stunning mountains, turquoise lakes, and outdoor activities for nature lovers.\r\n\r\n\r\nSantorini, Greece &ndash; Famous white buildings with blue domes overlooking the sea.\r\n\r\n\r\nMarrakech, Morocco &ndash; Experience bustling souks, spices, and rich cultural heritage.\r\n\r\n\r\nQueenstown, New Zealand &ndash; Adventure capital offering bungee jumping, skiing, and breathtaking landscapes.\r\n\r\n\r\nCape Town, South Africa &ndash; Beaches, Table Mountain, and a perfect mix of nature and city life.\r\n\r\n\r\nEach destination has something unique to offer, whether you love adventure, culture, or relaxation. Start planning your next dream trip today!', 'draft', '2025-08-21 13:48:04', '2025-08-21 13:48:04'),
(11, 1, 17, '10 Easy and Healthy Breakfast Recipes', '1755758989.jpg', 'Start your day with energy and nutrition! Here are 10 easy and healthy breakfast recipes:\r\n\r\n\r\nOvernight Oats with Berries &ndash; A no-cook breakfast full of fiber and antioxidants.\r\n\r\n\r\nAvocado Toast with Poached Eggs &ndash; Creamy avocado paired with perfectly poached eggs.\r\n\r\n\r\nGreek Yogurt Parfait &ndash; Layered yogurt, granola, and fresh fruits.\r\n\r\n\r\nBanana Pancakes &ndash; Simple pancakes made with bananas, eggs, and oats.\r\n\r\n\r\nSmoothie Bowl &ndash; Blend your favorite fruits and top with seeds and nuts.\r\n\r\n\r\nVeggie Omelette &ndash; Packed with fresh vegetables and protein.\r\n\r\n\r\nChia Seed Pudding &ndash; Soaked chia seeds in almond milk, topped with fruits.\r\n\r\n\r\nPeanut Butter Banana Toast &ndash; Quick, tasty, and full of protein.\r\n\r\n\r\nQuinoa Breakfast Bowl &ndash; Cooked quinoa with nuts, fruits, and a drizzle of honey.\r\n\r\n\r\nWhole Grain Waffles &ndash; Light, crispy, and perfect with fruit toppings.\r\n\r\n\r\nHealthy breakfasts don&rsquo;t have to be boring! Try these recipes to kickstart your day with delicious and nutritious meals.', 'draft', '2025-08-21 13:49:49', '2025-08-21 13:49:49'),
(12, 1, 20, 'Top 7 Tourist Destinations in Japan', '1755759159.jpg', 'Japan is a land of beauty, culture, and adventure. Here are the top 7 tourist destinations you must visit:\r\n\r\n\r\nTokyo &ndash; Explore the bustling city, shopping districts, and historic temples.\r\n\r\n\r\nKyoto &ndash; Famous for its traditional tea houses, shrines, and cherry blossoms.\r\n\r\n\r\nOsaka &ndash; Experience amazing street food, entertainment, and Osaka Castle.\r\n\r\n\r\nHokkaido &ndash; Ideal for winter sports, hot springs, and nature lovers.\r\n\r\n\r\nNara &ndash; Home to friendly deer and ancient temples.\r\n\r\n\r\nHiroshima &ndash; Learn history and visit the Peace Memorial Park.\r\n\r\n\r\nMount Fuji &ndash; Iconic mountain perfect for hiking and photography.\r\n\r\n\r\nJapan offers a unique blend of modern lifestyle and cultural heritage. Plan your trip to experience the beauty and charm of this incredible country.', 'draft', '2025-08-21 13:52:39', '2025-08-21 13:52:39'),
(13, 1, 21, 'The Ultimate Guide to Indoor Plants for Beginners', '1755759310.jpg', 'Indoor plants not only beautify your living space but also improve air quality and mental well-being. Here&rsquo;s a beginner-friendly guide:\r\n\r\n\r\nSnake Plant (Sansevieria) &ndash; Very low maintenance and purifies the air.\r\n\r\n\r\nPeace Lily &ndash; Beautiful white blooms and helps remove toxins from the air.\r\n\r\n\r\nSpider Plant &ndash; Easy to care for and perfect for hanging baskets.\r\n\r\n\r\nAloe Vera &ndash; Useful for skincare and thrives with minimal water.\r\n\r\n\r\nPothos &ndash; Great for beginners, grows fast, and tolerates low light.\r\n\r\n\r\nTips for beginners: Place plants where they get sufficient indirect sunlight, water moderately, and occasionally clean the leaves to allow better photosynthesis. Indoor plants bring life and freshness to any room effortlessly.', '', '2025-08-21 13:55:10', '2025-08-21 13:55:10'),
(14, 1, 22, '10 Healthy Breakfast Ideas to Start Your Day Right', '1755759571.jpg', '\r\nBreakfast is the most important meal of the day, and starting it with nutritious options boosts energy, metabolism, and overall health. Here are 10 healthy breakfast ideas:\r\n\r\n\r\nOatmeal with Fruits &ndash; Rich in fiber and keeps you full for hours.\r\n\r\n\r\nGreek Yogurt with Honey and Nuts &ndash; Protein-packed and delicious.\r\n\r\n\r\nSmoothie Bowl &ndash; Blend fruits, vegetables, and seeds for a refreshing start.\r\n\r\n\r\nAvocado Toast &ndash; Healthy fats with whole-grain bread.\r\n\r\n\r\nChia Seed Pudding &ndash; High in omega-3 and easy to prepare.\r\n\r\n\r\nEgg Muffins &ndash; Quick, protein-rich, and customizable.\r\n\r\n\r\nWhole-Grain Pancakes &ndash; Add banana or berries for extra nutrition.\r\n\r\n\r\nPeanut Butter Banana Toast &ndash; Energy-boosting and tasty.\r\n\r\n\r\nQuinoa Breakfast Bowl &ndash; High-protein and filling.\r\n\r\n\r\nGreen Smoothie &ndash; Spinach, apple, banana, and almond milk for detox.\r\n\r\n\r\nA healthy breakfast ensures you stay energized, focused, and reduces cravings throughout the day.\r\n\r\n\r\n\r\n&nbsp;\r\n\r\n', 'draft', '2025-08-21 13:59:31', '2025-08-21 13:59:31'),
(15, 1, 20, 'Top 10 Travel Destinations for 2025', '1755760749.jpg', 'Planning your next adventure? Here are the top 10 travel destinations for 2025 that offer breathtaking views, rich culture, and unforgettable experiences:\r\n\r\n\r\nKyoto, Japan &ndash; Explore ancient temples and stunning cherry blossoms.\r\n\r\n\r\nLisbon, Portugal &ndash; Vibrant streets, historic architecture, and coastal charm.\r\n\r\n\r\nQueenstown, New Zealand &ndash; Adventure capital with stunning landscapes.\r\n\r\n\r\nMarrakech, Morocco &ndash; Colorful souks, desert excursions, and rich heritage.\r\n\r\n\r\nBanff, Canada &ndash; Majestic mountains and crystal-clear lakes.\r\n\r\n\r\nBarcelona, Spain &ndash; Art, architecture, and Mediterranean vibes.\r\n\r\n\r\nSantorini, Greece &ndash; Iconic sunsets and picturesque white buildings.\r\n\r\n\r\nCape Town, South Africa &ndash; Scenic coastlines and vibrant culture.\r\n\r\n\r\nReykjavik, Iceland &ndash; Northern lights, geysers, and glaciers.\r\n\r\n\r\nBali, Indonesia &ndash; Tropical paradise with beaches and rice terraces.\r\n\r\n\r\nWhether you&rsquo;re seeking adventure, relaxation, or cultural immersion, these destinations should be on every traveler&rsquo;s bucket list.', 'draft', '2025-08-21 14:19:09', '2025-08-21 14:19:09'),
(17, 1, 24, 'Top 5 Cricket Matches That Changed the Game', '1755761117.jpg', 'Cricket is a sport full of unforgettable moments. Here are 5 cricket matches that left a mark on the history of the game:\r\n\r\n\r\n1992 World Cup Final &ndash; Pakistan vs EnglandPakistan chased 249 and lifted their first World Cup trophy, showcasing resilience and teamwork.\r\n\r\n\r\n2005 Ashes &ndash; England vs Australia, 2nd TestKnown as one of the greatest Tests ever, with England winning after a thrilling finish.\r\n\r\n\r\n2007 T20 World Cup Final &ndash; India vs PakistanIndia won the inaugural T20 World Cup in a nail-biting match, igniting the T20 craze worldwide.\r\n\r\n\r\n2011 World Cup Final &ndash; India vs Sri LankaIndia&rsquo;s Dhoni hit the iconic winning six, ending a 28-year World Cup drought.\r\n\r\n\r\n2019 World Cup Final &ndash; England vs New ZealandThe first-ever World Cup final decided by boundary count after a super over tie&mdash;truly historic.\r\n\r\n\r\nCricket is more than a sport; it&rsquo;s full of unforgettable drama and passion.', 'draft', '2025-08-21 14:25:17', '2025-08-21 14:25:17'),
(18, 1, 25, 'op 10 Must-Watch Movies of the Decade', '1755761295.jpg', 'Movies have the power to inspire, entertain, and make us think. Here&rsquo;s a list of 10 must-watch movies from the past decade:\r\n\r\n\r\nInception (2010) &ndash; A mind-bending thriller that explores dreams within dreams.\r\n\r\n\r\nInterstellar (2014) &ndash; A visually stunning journey through space and time.\r\n\r\n\r\nLa La Land (2016) &ndash; A modern musical about love, ambition, and chasing dreams.\r\n\r\n\r\nParasite (2019) &ndash; The Oscar-winning South Korean masterpiece that mixes dark comedy with social commentary.\r\n\r\n\r\nAvengers: Endgame (2019) &ndash; The epic conclusion to the Marvel Cinematic Universe&rsquo;s Infinity Saga.\r\n\r\n\r\nJoker (2019) &ndash; A gripping, psychological take on the origin of the infamous villain.\r\n\r\n\r\nThe Irishman (2019) &ndash; A gangster epic showcasing Martin Scorsese&rsquo;s mastery of storytelling.\r\n\r\n\r\n1917 (2019) &ndash; A visually immersive war film with a continuous-shot effect.\r\n\r\n\r\nDune (2021) &ndash; Sci-fi spectacle with breathtaking visuals and storytelling.\r\n\r\n\r\nEverything Everywhere All at Once (2022) &ndash; A creative and emotional multiverse adventure.\r\n\r\n\r\nWatching these movies will give you an insight into modern cinema&rsquo;s creativity, diversity, and storytelling', 'draft', '2025-08-21 14:28:15', '2025-08-21 14:28:15'),
(19, 1, 26, 'Devastating Floods Hit Pakistan: Thousands Affected', '1755761463.jpg', 'Pakistan has recently faced severe flooding due to unprecedented heavy rainfall, affecting millions of people across multiple provinces. Homes have been destroyed, roads submerged, and thousands of families displaced.\r\nEmergency services, NGOs, and volunteers are working tirelessly to provide relief, including food, clean water, and medical aid. The government has also declared disaster-hit zones and is coordinating rescue operations.\r\nExperts warn that climate change and unplanned urbanization are contributing to the frequency and intensity of such disasters. Citizens are urged to stay updated, follow safety instructions, and help support relief efforts where possible.\r\nThe situation highlights the urgent need for climate resilience measures and better disaster preparedness in Pakistan.', 'draft', '2025-08-21 14:31:03', '2025-08-21 14:31:03');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tax_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `track_stock` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `tax_id`, `category_id`, `name`, `sku`, `barcode`, `price`, `cost`, `track_stock`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 3, 'Denim Jeans', 'DNJ-002', '890123456790', 57.00, 30.00, 1, '2025-08-26 19:28:06', '2025-09-22 14:11:37', NULL),
(3, 1, 2, 'Water Bottle', 'WBT-004', '890123456792', 9.99, 4.50, 0, '2025-08-26 19:29:51', '2025-09-02 13:38:46', NULL),
(4, 1, NULL, 'Wireless Headset', 'WHD-005', '890123456793', 59.99, 35.00, 1, '2025-08-26 19:30:51', '2025-09-22 14:24:19', '2025-09-22 14:24:19'),
(6, 1, 2, 'Brands Kahani', 'vhs222', '3343432221', 3689.00, 3000.00, 1, '2025-09-19 17:12:20', '2025-09-19 17:12:20', NULL),
(7, 1, 2, 'The Cress', 'AZ343', '2323232323232', 2600.00, 2000.00, 1, '2025-09-19 17:13:14', '2025-09-19 17:13:14', NULL),
(8, 1, 2, 'Charcoal', '18000', 'hshhh3', 18000.00, 1000.00, 1, '2025-09-19 17:14:10', '2025-09-19 17:14:10', NULL),
(9, 1, 2, 'Outfitters', 'bh44', '8383838382', 7500.00, 3000.00, 1, '2025-09-19 17:14:47', '2025-09-19 17:14:47', NULL),
(10, 1, 2, 'Khaadi', 'uh44', '838484', 6000.00, 2000.00, 1, '2025-09-19 17:15:51', '2025-09-19 17:15:51', NULL),
(11, 1, 4, 'NJ Mens', 'hsh44', '928184893', 3600.00, 2000.00, 1, '2025-09-19 17:16:59', '2025-09-19 17:16:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'Shirts', '2025-08-27 13:47:53', '2025-08-27 13:47:53'),
(3, 'Shoes', '2025-08-27 13:48:00', '2025-08-27 13:48:00'),
(4, 'paints', '2025-09-19 17:09:36', '2025-09-19 17:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `product_taxes`
--

CREATE TABLE `product_taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tax_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `name`, `sku`, `barcode`, `price`, `cost`, `created_at`, `updated_at`) VALUES
(2, 1, 'Womens Jeans', 'JP-Red-L', '37282738282937', 100.00, 50.00, '2025-08-29 18:38:26', '2025-09-02 13:19:42');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `supplier_id`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(2, 3, 11000.00, 'Draft', '2025-08-28 14:20:24', '2025-08-28 14:20:24'),
(3, 4, 10000.00, 'Draft', '2025-08-28 18:30:34', '2025-08-28 18:30:34');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(8,2) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_id`, `product_id`, `quantity`, `cost`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 20.00, 200.00, '2025-08-28 17:14:09', '2025-08-28 17:14:09'),
(3, 2, 4, 15.00, 200.00, '2025-08-28 18:11:09', '2025-08-28 18:11:09');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin', '2025-08-13 18:43:27', '2025-08-18 18:54:48'),
(4, 'Supplier', '2025-08-14 13:10:35', '2025-08-27 14:33:38'),
(5, 'Cashier', '2025-08-19 16:42:07', '2025-08-26 14:30:58'),
(7, 'User', '2025-08-26 14:31:23', '2025-08-26 14:31:23'),
(8, 'Sale Man', '2025-09-18 17:44:12', '2025-09-18 17:44:12');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(49, 1, 64, '2025-08-19 10:21:40', '2025-08-19 10:21:40'),
(54, 1, 52, '2025-08-19 10:21:40', '2025-08-19 10:21:40'),
(55, 1, 53, '2025-08-19 10:21:40', '2025-08-19 10:21:40'),
(56, 1, 54, '2025-08-19 10:21:40', '2025-08-19 10:21:40'),
(57, 1, 55, '2025-08-19 10:21:40', '2025-08-19 10:21:40'),
(58, 1, 56, '2025-08-19 10:21:40', '2025-08-19 10:21:40'),
(59, 1, 57, '2025-08-19 10:21:40', '2025-08-19 10:21:40'),
(60, 1, 58, '2025-08-19 10:21:40', '2025-08-19 10:21:40'),
(61, 1, 59, '2025-08-19 10:21:40', '2025-08-19 10:21:40'),
(70, 1, 60, '2025-08-19 11:29:37', '2025-08-19 11:29:37'),
(71, 1, 61, '2025-08-19 11:29:37', '2025-08-19 11:29:37'),
(72, 1, 62, '2025-08-19 11:29:37', '2025-08-19 11:29:37'),
(73, 1, 63, '2025-08-19 11:29:37', '2025-08-19 11:29:37'),
(82, 1, 71, '2025-08-20 08:26:57', '2025-08-20 08:26:57'),
(83, 1, 72, '2025-08-20 08:26:57', '2025-08-20 08:26:57'),
(84, 1, 73, '2025-08-20 08:26:57', '2025-08-20 08:26:57'),
(89, 4, 64, '2025-08-20 10:45:31', '2025-08-20 10:45:31'),
(90, 1, 75, '2025-08-22 05:49:47', '2025-08-22 05:49:47'),
(91, 1, 76, '2025-08-22 06:03:24', '2025-08-22 06:03:24'),
(92, 1, 77, '2025-08-22 06:42:46', '2025-08-22 06:42:46'),
(93, 1, 78, '2025-08-22 06:48:32', '2025-08-22 06:48:32'),
(94, 1, 79, '2025-08-22 07:07:48', '2025-08-22 07:07:48'),
(95, 1, 80, '2025-08-26 08:06:25', '2025-08-26 08:06:25'),
(96, 1, 81, '2025-08-26 08:21:37', '2025-08-26 08:21:37'),
(97, 1, 82, '2025-08-26 08:45:00', '2025-08-26 08:45:00'),
(98, 1, 83, '2025-08-26 09:06:33', '2025-08-26 09:06:33'),
(99, 1, 84, '2025-08-26 10:01:10', '2025-08-26 10:01:10'),
(100, 5, 64, '2025-08-26 11:25:52', '2025-08-26 11:25:52'),
(101, 7, 64, '2025-08-26 11:26:10', '2025-08-26 11:26:10'),
(102, 1, 85, '2025-08-26 11:37:42', '2025-08-26 11:37:42'),
(103, 1, 86, '2025-08-26 11:51:13', '2025-08-26 11:51:13'),
(104, 1, 87, '2025-08-26 13:12:13', '2025-08-26 13:12:13'),
(105, 1, 88, '2025-08-26 13:33:06', '2025-08-26 13:33:06'),
(106, 1, 89, '2025-08-26 13:37:48', '2025-08-26 13:37:48'),
(107, 1, 90, '2025-08-27 06:10:21', '2025-08-27 06:10:21'),
(108, 1, 91, '2025-08-27 06:17:38', '2025-08-27 06:17:38'),
(109, 1, 92, '2025-08-27 06:36:28', '2025-08-27 06:36:28'),
(110, 1, 93, '2025-08-27 06:36:28', '2025-08-27 06:36:28'),
(111, 1, 94, '2025-08-27 06:36:28', '2025-08-27 06:36:28'),
(112, 1, 95, '2025-08-27 08:18:08', '2025-08-27 08:18:08'),
(113, 1, 96, '2025-08-27 08:24:05', '2025-08-27 08:24:05'),
(114, 1, 97, '2025-08-27 10:16:27', '2025-08-27 10:16:27'),
(115, 1, 98, '2025-08-27 10:16:27', '2025-08-27 10:16:27'),
(116, 1, 99, '2025-08-27 10:16:27', '2025-08-27 10:16:27'),
(117, 1, 100, '2025-08-27 10:58:40', '2025-08-27 10:58:40'),
(118, 1, 101, '2025-08-27 10:58:40', '2025-08-27 10:58:40'),
(119, 1, 102, '2025-08-27 10:58:40', '2025-08-27 10:58:40'),
(120, 1, 103, '2025-08-27 10:58:40', '2025-08-27 10:58:40'),
(121, 1, 104, '2025-08-27 10:58:40', '2025-08-27 10:58:40'),
(122, 1, 65, '2025-08-27 11:55:29', '2025-08-27 11:55:29'),
(123, 1, 66, '2025-08-27 11:55:29', '2025-08-27 11:55:29'),
(124, 1, 67, '2025-08-27 11:55:29', '2025-08-27 11:55:29'),
(125, 1, 68, '2025-08-27 11:55:29', '2025-08-27 11:55:29'),
(126, 1, 74, '2025-08-27 11:55:29', '2025-08-27 11:55:29'),
(132, 1, 105, '2025-08-28 06:00:27', '2025-08-28 06:00:27'),
(133, 1, 106, '2025-08-28 06:00:27', '2025-08-28 06:00:27'),
(134, 1, 107, '2025-08-28 06:00:27', '2025-08-28 06:00:27'),
(135, 1, 108, '2025-08-28 06:00:27', '2025-08-28 06:00:27'),
(136, 1, 109, '2025-08-28 06:00:27', '2025-08-28 06:00:27'),
(137, 1, 110, '2025-08-28 07:53:56', '2025-08-28 07:53:56'),
(138, 1, 111, '2025-08-28 08:29:16', '2025-08-28 08:29:16'),
(139, 1, 112, '2025-08-28 10:30:35', '2025-08-28 10:30:35'),
(140, 1, 113, '2025-08-28 10:30:35', '2025-08-28 10:30:35'),
(141, 1, 114, '2025-08-28 10:30:35', '2025-08-28 10:30:35'),
(142, 1, 115, '2025-08-28 18:27:49', '2025-08-28 18:27:49'),
(143, 1, 116, '2025-08-28 18:41:02', '2025-08-28 18:41:02'),
(144, 1, 117, '2025-08-28 19:54:22', '2025-08-28 19:54:22'),
(145, 1, 118, '2025-08-28 19:54:22', '2025-08-28 19:54:22'),
(146, 1, 119, '2025-08-28 19:54:22', '2025-08-28 19:54:22'),
(147, 1, 120, '2025-08-29 06:24:01', '2025-08-29 06:24:01'),
(148, 1, 121, '2025-08-29 06:37:36', '2025-08-29 06:37:36'),
(149, 1, 122, '2025-08-29 07:15:17', '2025-08-29 07:15:17'),
(150, 1, 123, '2025-08-29 07:15:17', '2025-08-29 07:15:17'),
(151, 1, 124, '2025-08-29 07:15:17', '2025-08-29 07:15:17'),
(152, 1, 125, '2025-08-29 09:46:44', '2025-08-29 09:46:44'),
(153, 1, 126, '2025-08-29 11:18:15', '2025-08-29 11:18:15'),
(154, 1, 127, '2025-08-29 11:18:15', '2025-08-29 11:18:15'),
(155, 1, 128, '2025-08-29 11:18:15', '2025-08-29 11:18:15'),
(156, 1, 129, '2025-08-29 11:18:15', '2025-08-29 11:18:15'),
(157, 1, 130, '2025-08-29 13:11:16', '2025-08-29 13:11:16'),
(158, 1, 131, '2025-08-29 13:29:31', '2025-08-29 13:29:31'),
(159, 1, 132, '2025-08-29 13:29:31', '2025-08-29 13:29:31'),
(160, 1, 133, '2025-08-29 13:29:31', '2025-08-29 13:29:31'),
(161, 1, 134, '2025-08-29 13:29:31', '2025-08-29 13:29:31'),
(162, 1, 135, '2025-09-01 06:20:36', '2025-09-01 06:20:36'),
(163, 1, 136, '2025-09-01 06:24:43', '2025-09-01 06:24:43'),
(164, 1, 137, '2025-09-01 06:24:43', '2025-09-01 06:24:43'),
(165, 1, 138, '2025-09-01 06:24:43', '2025-09-01 06:24:43'),
(166, 1, 139, '2025-09-01 06:24:43', '2025-09-01 06:24:43'),
(167, 1, 140, '2025-09-01 08:00:07', '2025-09-01 08:00:07'),
(168, 1, 141, '2025-09-01 08:00:07', '2025-09-01 08:00:07'),
(169, 1, 142, '2025-09-01 08:00:07', '2025-09-01 08:00:07'),
(170, 1, 143, '2025-09-01 08:00:07', '2025-09-01 08:00:07'),
(171, 1, 144, '2025-09-01 08:00:07', '2025-09-01 08:00:07'),
(172, 1, 145, '2025-09-01 10:36:32', '2025-09-01 10:36:32'),
(173, 1, 146, '2025-09-01 10:36:32', '2025-09-01 10:36:32'),
(174, 1, 147, '2025-09-01 10:36:32', '2025-09-01 10:36:32'),
(175, 1, 148, '2025-09-01 10:36:32', '2025-09-01 10:36:32'),
(176, 1, 149, '2025-09-01 10:36:32', '2025-09-01 10:36:32'),
(177, 1, 150, '2025-09-01 12:00:20', '2025-09-01 12:00:20'),
(178, 1, 151, '2025-09-01 12:00:20', '2025-09-01 12:00:20'),
(179, 1, 152, '2025-09-01 12:00:20', '2025-09-01 12:00:20'),
(180, 1, 153, '2025-09-01 12:00:20', '2025-09-01 12:00:20'),
(181, 1, 154, '2025-09-01 12:00:20', '2025-09-01 12:00:20'),
(187, 8, 155, '2025-09-18 12:01:12', '2025-09-18 12:01:12'),
(188, 8, 156, '2025-09-18 12:01:12', '2025-09-18 12:01:12'),
(189, 8, 157, '2025-09-18 12:01:12', '2025-09-18 12:01:12'),
(190, 8, 158, '2025-09-18 12:01:12', '2025-09-18 12:01:12'),
(191, 8, 159, '2025-09-18 12:01:12', '2025-09-18 12:01:12'),
(192, 8, 160, '2025-09-18 12:01:12', '2025-09-18 12:01:12'),
(193, 8, 135, '2025-09-18 13:27:33', '2025-09-18 13:27:33'),
(194, 8, 137, '2025-09-18 13:28:32', '2025-09-18 13:28:32'),
(195, 8, 138, '2025-09-18 13:28:32', '2025-09-18 13:28:32'),
(196, 8, 139, '2025-09-18 13:28:32', '2025-09-18 13:28:32'),
(197, 8, 161, '2025-09-19 06:29:09', '2025-09-19 06:29:09'),
(198, 1, 162, '2025-09-21 11:38:50', '2025-09-21 11:38:50'),
(199, 1, 163, '2025-09-22 06:57:33', '2025-09-22 06:57:33'),
(200, 1, 164, '2025-09-22 07:10:27', '2025-09-22 07:10:27'),
(201, 1, 165, '2025-09-22 07:20:22', '2025-09-22 07:20:22');

-- --------------------------------------------------------

--
-- Table structure for table `role_users`
--

CREATE TABLE `role_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_users`
--

INSERT INTO `role_users` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(5, 1, 1, NULL, NULL),
(6, 5, 4, NULL, NULL),
(7, 7, 5, NULL, NULL),
(10, 8, 7, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `store_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `store_id`, `customer_id`, `user_id`, `subtotal`, `tax`, `discount`, `total`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 2, 5, 12.00, 12.00, 12.00, 12.00, 'Draft', '2025-09-01 14:06:50', '2025-09-01 14:38:30'),
(3, 3, 2, 5, 390.93, 0.00, 0.00, 390.93, 'completed', '2025-09-18 19:50:25', '2025-09-18 19:50:25'),
(6, 3, 2, 5, 644.89, 0.00, 0.00, 644.89, 'completed', '2025-09-18 20:08:33', '2025-09-18 20:08:33'),
(7, 3, 2, 5, 704.88, 0.00, 0.00, 704.88, 'completed', '2025-09-18 20:10:52', '2025-09-18 20:10:52'),
(8, 3, 2, 5, 704.88, 0.00, 0.00, 704.88, 'completed', '2025-09-18 20:11:17', '2025-09-18 20:11:17'),
(9, 3, 2, 5, 764.87, 0.00, 0.00, 764.87, 'completed', '2025-09-18 20:11:36', '2025-09-18 20:11:36'),
(11, 3, 2, 5, 884.85, 0.00, 0.00, 884.85, 'completed', '2025-09-18 20:15:57', '2025-09-18 20:15:57'),
(12, 3, 2, 5, 9.99, 0.00, 0.00, 9.99, 'completed', '2025-09-18 20:24:26', '2025-09-18 20:24:26'),
(13, 3, 2, 5, 246.96, 0.00, 0.00, 246.96, 'completed', '2025-09-18 20:52:58', '2025-09-18 20:52:58'),
(16, 3, 2, 5, 69.98, 0.00, 0.00, 69.98, 'completed', '2025-09-18 21:02:06', '2025-09-18 21:02:06'),
(17, 3, 2, 5, 196.96, 0.00, 0.00, 196.96, 'completed', '2025-09-18 21:04:30', '2025-09-18 21:04:30'),
(18, 3, 2, 5, 199.95, 0.00, 0.00, 199.95, 'completed', '2025-09-18 21:05:25', '2025-09-18 21:05:25'),
(19, 3, 2, 5, 844.89, 0.00, 0.00, 844.89, 'completed', '2025-09-19 13:01:45', '2025-09-19 13:01:45'),
(21, 3, 2, 5, 259.94, 0.00, 0.00, 259.94, 'completed', '2025-09-19 13:09:45', '2025-09-19 13:09:45'),
(23, 3, 2, 5, 59.99, 0.00, 0.00, 59.99, 'completed', '2025-09-19 14:06:25', '2025-09-19 14:06:25');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_variant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `product_variant_id`, `quantity`, `price`, `discount`, `tax`, `created_at`, `updated_at`) VALUES
(2, 11, 1, NULL, 5.00, 57.00, NULL, NULL, '2025-09-18 20:15:57', '2025-09-18 20:15:57'),
(3, 11, 3, NULL, 6.00, 9.99, NULL, NULL, '2025-09-18 20:15:57', '2025-09-18 20:15:57'),
(4, 11, 4, NULL, 9.00, 59.99, NULL, NULL, '2025-09-18 20:15:57', '2025-09-18 20:15:57'),
(5, 12, 3, NULL, 1.00, 9.99, NULL, NULL, '2025-09-18 20:24:26', '2025-09-18 20:24:26'),
(6, 13, 3, NULL, 1.00, 9.99, NULL, NULL, '2025-09-18 20:52:58', '2025-09-18 20:52:58'),
(7, 13, 4, NULL, 3.00, 59.99, NULL, NULL, '2025-09-18 20:52:58', '2025-09-18 20:52:58'),
(8, 13, 1, NULL, 1.00, 57.00, NULL, NULL, '2025-09-18 20:52:58', '2025-09-18 20:52:58'),
(9, 16, 4, NULL, 1.00, 59.99, NULL, NULL, '2025-09-18 21:02:06', '2025-09-18 21:02:06'),
(10, 16, 3, NULL, 1.00, 9.99, NULL, NULL, '2025-09-18 21:02:06', '2025-09-18 21:02:06'),
(11, 17, 4, NULL, 2.00, 59.99, NULL, NULL, '2025-09-18 21:04:30', '2025-09-18 21:04:30'),
(12, 17, 3, NULL, 2.00, 9.99, NULL, NULL, '2025-09-18 21:04:30', '2025-09-18 21:04:30'),
(13, 17, 1, NULL, 1.00, 57.00, NULL, NULL, '2025-09-18 21:04:30', '2025-09-18 21:04:30'),
(14, 18, 4, NULL, 3.00, 59.99, NULL, NULL, '2025-09-18 21:05:25', '2025-09-18 21:05:25'),
(15, 18, 3, NULL, 2.00, 9.99, NULL, NULL, '2025-09-18 21:05:25', '2025-09-18 21:05:25'),
(16, 19, 3, NULL, 2.00, 9.99, NULL, NULL, '2025-09-19 13:01:45', '2025-09-19 13:01:45'),
(17, 19, 4, NULL, 9.00, 59.99, NULL, NULL, '2025-09-19 13:01:45', '2025-09-19 13:01:45'),
(18, 19, 1, NULL, 5.00, 57.00, NULL, NULL, '2025-09-19 13:01:45', '2025-09-19 13:01:45'),
(19, 21, 3, NULL, 2.00, 9.99, NULL, NULL, '2025-09-19 13:09:45', '2025-09-19 13:09:45'),
(20, 21, 4, NULL, 4.00, 59.99, NULL, NULL, '2025-09-19 13:09:45', '2025-09-19 13:09:45'),
(21, 23, 4, NULL, 1.00, 59.99, NULL, NULL, '2025-09-19 14:06:25', '2025-09-19 14:06:25');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(9, '_token', 'STgLcoIXflq39VvCiIwAwZWGfzXyBF82OCfy3SAo', '2025-08-25 15:11:09', '2025-08-25 15:11:09'),
(10, 'newsletter', 'Subscribe to our weekly newsletter for the latest updates', '2025-08-25 15:11:09', '2025-08-25 15:11:09'),
(11, 'about_us', 'We provide high-quality articles and resources to help readers stay updated and make informed decisions.', '2025-08-25 15:11:09', '2025-08-25 15:11:09'),
(12, 'facebook', 'https://www.facebook.com/', '2025-08-25 15:11:09', '2025-08-25 15:11:09'),
(13, 'instagram', 'https://www.instagram.com/', '2025-08-25 15:11:09', '2025-08-25 15:11:09'),
(14, 'youtube', 'https://www.youtube.com/', '2025-08-25 15:11:09', '2025-08-25 15:11:09'),
(15, 'twitter', 'https://x.com/', '2025-08-25 15:11:09', '2025-08-25 15:11:09'),
(16, 'login_method', 'phone', '2025-09-22 14:42:29', '2025-09-22 17:11:59');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `store_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_variant_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `store_id`, `product_id`, `product_variant_id`, `quantity`, `type`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 2, '20', 'random', '2025-08-29 19:01:56', '2025-08-29 19:01:56'),
(2, 3, 1, 2, '20', 'fack', '2025-08-29 19:03:46', '2025-08-29 19:52:48');

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(3, 'G-Shop', '123', '2025-08-29 18:33:45', '2025-08-29 18:33:45');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `email`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(3, 'John Doe', 'john@gmail.com', '03468086567', 'USA', '2025-08-27 19:29:19', '2025-08-27 19:29:19'),
(4, 'taylor', 'taylor@gmail.com', '03047951820', 'UK', '2025-08-28 18:29:24', '2025-08-28 18:29:24');

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `rate` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `name`, `rate`, `created_at`, `updated_at`) VALUES
(1, 'GST', 5.00, '2025-08-26 15:37:44', '2025-08-26 15:37:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_ad_id` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `profile_image`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `employee_ad_id`, `is_active`, `role_id`, `phone`) VALUES
(1, 'Suleman Sikandar', 'sulemansikandar17@gmail.com', '1756119957.jpg', NULL, '$2y$12$4lQPCS.w7J1BlFjIvU4tgucgXb9CHsM8QlhTtjobEzqXyOZeQopyy', NULL, '2025-08-10 16:59:40', '2025-09-22 17:04:46', '', 0, 1, '03468086567'),
(5, 'taylor', 'usmanmaitla544@gmail.com', '1756119440.jpg', NULL, '$2y$12$PMmQck3VgKTZEE5QSAUqbu9vaUV.8UrPcDf.cOwceNTO3BM.230oa', NULL, '2025-08-13 17:59:53', '2025-09-18 20:37:54', NULL, NULL, 8, '03000000001'),
(7, 'ahmad', 'ahmad@gmail.com', '1756119487.jpg', NULL, '$2y$12$Tz3K0fD/P6eZtOK02yxBouCkHMQc0OGpwbkF/BMxkHUicN0u6MKoG', NULL, '2025-08-19 17:42:46', '2025-08-25 17:58:07', NULL, NULL, 5, '03000000002'),
(8, 'John Doe', 'john12@gmail.com', '1756119728.jpg', NULL, '$2y$12$eH4HU8W9ndDWxk37x2HiZev0mOU19SRPXqNpu5FgHpuiCbh9d3KnW', NULL, '2025-08-19 18:39:25', '2025-08-25 18:02:08', NULL, NULL, NULL, '03000000003'),
(16, 'ahmad@gmail.com', 'user@gmail.com', NULL, NULL, '$2y$12$MIKyCX4QX93NgOBNJ7TPmOxb/XwhGO9wLeE33NFiAuyiYCFB5bkma', NULL, '2025-08-27 18:27:50', '2025-08-27 18:27:50', NULL, NULL, NULL, '03000000004');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `methods`
--
ALTER TABLE `methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news_letters`
--
ALTER TABLE `news_letters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_letters_email_unique` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_sale_id_foreign` (`sale_id`),
  ADD KEY `payments_method_id_foreign` (`method_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_permission_category` (`permission_category_id`);

--
-- Indexes for table `permission_categories`
--
ALTER TABLE `permission_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD UNIQUE KEY `products_barcode_unique` (`barcode`),
  ADD KEY `products_tax_is_foreign` (`tax_id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_taxes`
--
ALTER TABLE `product_taxes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_taxes_tax_id_foreign` (`tax_id`),
  ADD KEY `product_taxes_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variants_product_id_foreign` (`product_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_permissions_role_id_foreign` (`role_id`),
  ADD KEY `role_permissions_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `role_users`
--
ALTER TABLE `role_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_users_user_id_foreign` (`user_id`),
  ADD KEY `role_users_role_id_foreign` (`role_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_store_id_foreign` (`store_id`),
  ADD KEY `sales_customer_id_foreign` (`customer_id`),
  ADD KEY `sales_user_id_foreign` (`user_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_items_product_id_foreign` (`product_id`),
  ADD KEY `sale_items_product_variant_id_foreign` (`product_variant_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_store_id_foreign` (`store_id`),
  ADD KEY `stock_movements_product_id_foreign` (`product_id`),
  ADD KEY `stock_movements_product_variant_id_foreign` (`product_variant_id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_email_unique` (`email`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `fk_users_roles` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `methods`
--
ALTER TABLE `methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `news_letters`
--
ALTER TABLE `news_letters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `permission_categories`
--
ALTER TABLE `permission_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_taxes`
--
ALTER TABLE `product_taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT for table `role_users`
--
ALTER TABLE `role_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_method_id_foreign` FOREIGN KEY (`method_id`) REFERENCES `methods` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `fk_permission_category` FOREIGN KEY (`permission_category_id`) REFERENCES `permission_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_tax_is_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_taxes`
--
ALTER TABLE `product_taxes`
  ADD CONSTRAINT `product_taxes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_taxes_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_users`
--
ALTER TABLE `role_users`
  ADD CONSTRAINT `role_users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
