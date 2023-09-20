-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2023 at 03:05 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nurseries`
--

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

CREATE TABLE `areas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `ar_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `en_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `areas`
--

INSERT INTO `areas` (`id`, `city_id`, `ar_name`, `en_name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, 'العزازية', NULL, NULL, '2023-09-14 03:14:21', NULL),
(2, 2, 'منطقة الحرمين', NULL, NULL, '2023-09-14 03:14:21', NULL),
(3, 1, 'الحي السابع', NULL, NULL, '2023-09-14 03:14:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `user_id`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'test', 'dasdasd', '2023-09-09 21:54:14', '2023-09-09 21:54:14'),
(2, 1, 'تجربة', 'dasdasd', '2023-09-09 21:58:49', '2023-09-09 21:58:49');

-- --------------------------------------------------------

--
-- Table structure for table `articles_comments`
--

CREATE TABLE `articles_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `comment` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles_comments`
--

INSERT INTO `articles_comments` (`id`, `article_id`, `user_id`, `comment`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'dasdasd', '2023-09-09 22:42:04', '2023-09-09 22:42:29');

-- --------------------------------------------------------

--
-- Table structure for table `cares`
--

CREATE TABLE `cares` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ar_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `en_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_required` tinyint(4) NOT NULL DEFAULT 0,
  `is_default` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cares`
--

INSERT INTO `cares` (`id`, `ar_name`, `en_name`, `is_required`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'السقية', 'water', 1, NULL, '2023-09-19 00:36:28', NULL),
(2, 'السماد', 'food', 0, NULL, '2023-09-19 00:36:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ar_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `en_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `ar_name`, `en_name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'نباتات صيفية', 'summer plants', NULL, '2023-09-05 01:34:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories_features`
--

CREATE TABLE `categories_features` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `ar_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `en_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories_features`
--

INSERT INTO `categories_features` (`id`, `category_id`, `ar_name`, `en_name`, `created_at`, `updated_at`) VALUES
(1, 1, 'توصيل امن', 'safe delivery', '2023-09-06 01:46:03', NULL),
(2, 1, 'الرعاية الطبية', 'medical care', '2023-09-05 01:46:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories_heading_questions`
--

CREATE TABLE `categories_heading_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `ar_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `en_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories_heading_questions`
--

INSERT INTO `categories_heading_questions` (`id`, `category_id`, `ar_name`, `en_name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'مواعيد المياة', 'water timeline', NULL, '2023-09-05 01:38:29', NULL),
(2, 1, 'مواعيد الحصاد', 'Harvest timeline', NULL, '2023-09-05 01:38:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories_heading_questions_datas`
--

CREATE TABLE `categories_heading_questions_datas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_heading_question_id` bigint(20) UNSIGNED NOT NULL,
  `ar_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `en_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories_heading_questions_datas`
--

INSERT INTO `categories_heading_questions_datas` (`id`, `category_heading_question_id`, `ar_name`, `en_name`, `type`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'كم مره يوميا', 'how many times in one day', 'text', NULL, '2023-09-05 01:40:09', NULL),
(2, 2, 'كم مره خلال السنة', 'end month', 'text', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `seen` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `government_id` bigint(20) UNSIGNED NOT NULL,
  `ar_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `en_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `government_id`, `ar_name`, `en_name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'الرياض', NULL, NULL, '2023-09-14 03:13:24', NULL),
(2, 1, 'مكة المكرمة', NULL, NULL, '2023-09-14 03:13:24', NULL),
(3, 3, 'المدينة المنورة', NULL, NULL, '2023-09-14 03:13:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `code`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'السعودية', '966', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favourites`
--

CREATE TABLE `favourites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favourites`
--

INSERT INTO `favourites` (`id`, `user_id`, `product_id`, `created_at`, `updated_at`) VALUES
(3, 1, 2, '2023-09-05 21:47:27', '2023-09-05 21:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `financial_reconciliations`
--

CREATE TABLE `financial_reconciliations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `total_money` double(8,2) NOT NULL,
  `admin_profit_percentage` double(8,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `financial_reconciliations`
--

INSERT INTO `financial_reconciliations` (`id`, `user_id`, `total_money`, `admin_profit_percentage`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 4, 270.00, 50.00, NULL, '2023-09-16 03:50:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `financial_reconciliations_proit_percentages`
--

CREATE TABLE `financial_reconciliations_proit_percentages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from_who` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `percentage` double(8,2) NOT NULL,
  `note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `financial_reconciliations_proit_percentages`
--

INSERT INTO `financial_reconciliations_proit_percentages` (`id`, `from_who`, `percentage`, `note`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', 50.00, 'سيتم عمل المخالصات بشكل شهري من يوم واحد ليوم ثلاثه', NULL, '2023-09-15 20:43:42', NULL),
(2, 'seller', 10.00, NULL, NULL, '2023-09-15 20:43:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `following_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `followers`
--

INSERT INTO `followers` (`id`, `user_id`, `following_id`, `created_at`, `updated_at`) VALUES
(2, 1, 2, '2023-09-06 21:13:29', '2023-09-06 21:13:29'),
(3, 3, 1, '2023-09-06 21:13:29', '2023-09-06 21:13:29');

-- --------------------------------------------------------

--
-- Table structure for table `governments`
--

CREATE TABLE `governments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ar_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `en_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `governments`
--

INSERT INTO `governments` (`id`, `ar_name`, `en_name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'أملج', NULL, NULL, '2023-09-14 03:14:21', NULL),
(2, 'الأفلاج', NULL, NULL, '2023-09-14 03:14:21', NULL),
(3, 'البدائع', NULL, NULL, '2023-09-14 03:14:21', NULL),
(4, 'الجبيل', NULL, NULL, '2023-09-14 03:14:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `imageable_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imageable_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `imageable_id`, `imageable_type`, `name`, `created_at`, `updated_at`) VALUES
(1, '1', 'App\\Models\\users_commercial_info', 'sellers_commercial/16937963433813880558953_image.jpg', '2023-09-04 01:59:03', '2023-09-04 01:59:03'),
(2, '1', 'App\\Models\\users_commercial_info', 'sellers_commercial/1693796343292998079707_image.jpg', '2023-09-04 01:59:03', '2023-09-04 01:59:03'),
(3, '1', 'App\\Models\\users_commercial_info', 'sellers_commercial/16937963431108215240397_image.jpg', '2023-09-04 01:59:03', '2023-09-04 01:59:03'),
(4, '2', 'App\\Models\\products', 'products/16937963433813880558953_image.jpg', '2023-09-04 01:59:03', '2023-09-04 01:59:03'),
(5, '2', 'App\\Models\\products', 'products/1693796343292998079707_image.jpg', '2023-09-04 01:59:03', '2023-09-04 01:59:03'),
(6, '1', 'App\\Models\\products', 'products/16937963431108215240397_image.jpg', '2023-09-04 01:59:03', '2023-09-04 01:59:03'),
(7, '1', 'App\\Models\\articles', 'articles/16943000542858124589889_image.jpg', '2023-09-09 21:54:14', '2023-09-09 21:54:14'),
(8, '2', 'App\\Models\\articles', 'articles/16943003296003474857029_image.jpg', '2023-09-09 21:58:49', '2023-09-09 21:58:49');

-- --------------------------------------------------------

--
-- Table structure for table `important_infos`
--

CREATE TABLE `important_infos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(2, 'default', '{\"uuid\":\"9c9aebc0-4c11-45cd-a231-8d8b845ffad6\",\"displayName\":\"App\\\\Jobs\\\\sendNotificationsToFollowersJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\sendNotificationsToFollowersJob\",\"command\":\"O:40:\\\"App\\\\Jobs\\\\sendNotificationsToFollowersJob\\\":13:{s:46:\\\"\\u0000App\\\\Jobs\\\\sendNotificationsToFollowersJob\\u0000data\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:20:\\\"App\\\\Models\\\\followers\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:53:\\\"\\u0000App\\\\Jobs\\\\sendNotificationsToFollowersJob\\u0000content_msg\\\";a:2:{s:2:\\\"ar\\\";s:53:\\\"تم نشر منتج جديد من قبل ali mohamed\\\";s:2:\\\"en\\\";s:49:\\\"there is a new product published from ali mohamed\\\";}s:45:\\\"\\u0000App\\\\Jobs\\\\sendNotificationsToFollowersJob\\u0000url\\\";s:10:\\\"\\/following\\\";s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1694820359, 1694820359),
(3, 'default', '{\"uuid\":\"3cbbe1a8-5e5c-4a65-b851-7b01b6ceba01\",\"displayName\":\"App\\\\Jobs\\\\sendNotificationsToFollowersJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\sendNotificationsToFollowersJob\",\"command\":\"O:40:\\\"App\\\\Jobs\\\\sendNotificationsToFollowersJob\\\":13:{s:46:\\\"\\u0000App\\\\Jobs\\\\sendNotificationsToFollowersJob\\u0000data\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:20:\\\"App\\\\Models\\\\followers\\\";s:2:\\\"id\\\";a:1:{i:0;i:2;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:53:\\\"\\u0000App\\\\Jobs\\\\sendNotificationsToFollowersJob\\u0000content_msg\\\";a:2:{s:2:\\\"ar\\\";s:46:\\\"تم نشر منتج جديد من قبل saad\\\";s:2:\\\"en\\\";s:42:\\\"there is a new product published from saad\\\";}s:45:\\\"\\u0000App\\\\Jobs\\\\sendNotificationsToFollowersJob\\u0000url\\\";s:10:\\\"\\/following\\\";s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1695084597, 1695084597),
(4, 'default', '{\"uuid\":\"03715763-0d71-4f29-abc3-f694c4875c48\",\"displayName\":\"App\\\\Jobs\\\\sendNotificationsToFollowersJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\sendNotificationsToFollowersJob\",\"command\":\"O:40:\\\"App\\\\Jobs\\\\sendNotificationsToFollowersJob\\\":13:{s:46:\\\"\\u0000App\\\\Jobs\\\\sendNotificationsToFollowersJob\\u0000data\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:20:\\\"App\\\\Models\\\\followers\\\";s:2:\\\"id\\\";a:1:{i:0;i:2;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:53:\\\"\\u0000App\\\\Jobs\\\\sendNotificationsToFollowersJob\\u0000content_msg\\\";a:2:{s:2:\\\"ar\\\";s:46:\\\"تم نشر منتج جديد من قبل saad\\\";s:2:\\\"en\\\";s:42:\\\"there is a new product published from saad\\\";}s:45:\\\"\\u0000App\\\\Jobs\\\\sendNotificationsToFollowersJob\\u0000url\\\";s:10:\\\"\\/following\\\";s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1695098836, 1695098836);

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `item_id`, `type`, `created_at`, `updated_at`) VALUES
(2, 1, 2, 'products', '2023-09-05 22:00:44', '2023-09-05 22:00:44'),
(4, 1, 1, 'articles', '2023-09-09 22:48:51', '2023-09-09 22:48:51'),
(5, 2, 1, 'articles', '2023-09-09 22:48:51', '2023-09-09 22:48:51');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2013_07_14_203748_create_roles_table', 1),
(2, '2013_07_14_212529_create_countries_table', 1),
(3, '2014_07_14_212550_create_cities_table', 1),
(4, '2014_10_12_100000_create_password_resets_table', 1),
(5, '2019_08_19_000000_create_failed_jobs_table', 1),
(6, '2021_10_12_000000_create_users_table', 1),
(7, '2022_07_15_011253_create_supports_table', 1),
(8, '2022_07_16_155307_create_notifications_table', 1),
(9, '2022_12_03_313807_create_chats_table', 1),
(10, '2023_05_04_014700_create_reports_table', 1),
(11, '2023_05_04_030931_create_pages_table', 1),
(12, '2023_05_04_031020_create_privileges_table', 1),
(13, '2023_05_04_031524_create_payment_actions_table', 1),
(14, '2023_05_06_163113_create_important_infos_table', 1),
(15, '2023_06_10_234812_create_jobs_table', 1),
(16, '2023_09_04_013319_create_users_bank_infos_table', 1),
(17, '2023_09_04_013346_create_users_store_infos_table', 1),
(18, '2023_09_04_013538_create_users_commercial_infos_table', 1),
(19, '2023_09_04_013904_create_images_table', 1),
(20, '2023_09_04_014745_create_categories_table', 1),
(21, '2023_09_04_014856_create_products_table', 1),
(22, '2023_09_04_015506_create_products_wholesale_prices_table', 1),
(23, '2023_09_04_020517_create_categories_heading_questions_table', 1),
(24, '2023_09_04_020544_create_categories_heading_questions_datas_table', 1),
(25, '2023_09_04_020949_create_categories_features_table', 1),
(26, '2023_09_04_021132_create_products_features_prices_table', 1),
(27, '2023_09_04_021438_create_products_discounts_table', 1),
(28, '2023_09_04_022442_create_favourites_table', 1),
(29, '2023_09_04_022858_create_likes_table', 1),
(30, '2023_09_05_031531_create_products_questions_answers_table', 1),
(31, '2023_09_06_010139_create_seens_table', 2),
(32, '2023_09_06_022851_create_searches_table', 3),
(33, '2023_09_06_235242_create_followers_table', 4),
(34, '2023_09_07_020222_create_orders_table', 5),
(35, '2023_09_07_020231_create_orders_items_table', 5),
(36, '2023_09_07_020253_create_orders_items_features_table', 5),
(37, '2023_09_07_020719_create_orders_items_rates_table', 5),
(38, '2023_09_07_033823_create_orders_shipment_infos_table', 5),
(39, '2023_09_09_212339_create_articles_table', 6),
(40, '2023_09_09_212747_create_articles_comments_table', 6),
(41, '2023_09_14_025524_create_governments_table', 7),
(42, '2023_09_14_025916_create_financial_reconciliations_table', 7),
(43, '2023_09_14_030009_create_financial_reconciliations_profit_percentages_table', 7),
(44, '2023_09_14_033755_create_areas_table', 8),
(45, '2023_09_14_034034_create_products_deliveries_table', 8),
(46, '2023_09_14_034321_create_user_addresses_table', 8),
(47, '2023_09_14_034340_create_orders_addresses_table', 8),
(48, '2023_09_17_052319_create_cares_table', 9),
(49, '2023_09_17_052354_create_products_cares_table', 9),
(50, '2023_09_19_032725_users_products_care_alerts', 10),
(51, '2023_09_19_034821_create_users_products_cares_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `ar_content` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `en_content` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seen` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `sender_id`, `receiver_id`, `ar_content`, `en_content`, `url`, `seen`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'تم نشر منتج جديد من قبل ali', 'there is a new product published from ali', '/following', 0, '2023-09-06 22:51:38', '2023-09-06 22:51:38'),
(2, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:45:16', '2023-09-07 14:45:16'),
(3, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:45:16', '2023-09-07 14:45:16'),
(6, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:48:27', '2023-09-07 14:48:27'),
(7, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:48:27', '2023-09-07 14:48:27'),
(18, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:50:33', '2023-09-07 14:50:33'),
(19, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:50:33', '2023-09-07 14:50:33'),
(20, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:52:32', '2023-09-07 14:52:32'),
(21, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:52:32', '2023-09-07 14:52:32'),
(22, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:54:02', '2023-09-07 14:54:02'),
(23, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:54:02', '2023-09-07 14:54:02'),
(24, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:56:41', '2023-09-07 14:56:41'),
(25, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:56:41', '2023-09-07 14:56:41'),
(26, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:57:15', '2023-09-07 14:57:15'),
(27, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:57:15', '2023-09-07 14:57:15'),
(32, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:58:20', '2023-09-07 14:58:20'),
(33, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:58:20', '2023-09-07 14:58:20'),
(36, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:59:12', '2023-09-07 14:59:12'),
(37, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 14:59:12', '2023-09-07 14:59:12'),
(38, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 15:00:57', '2023-09-07 15:00:57'),
(39, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 15:00:57', '2023-09-07 15:00:57'),
(44, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 15:02:42', '2023-09-07 15:02:42'),
(45, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 15:02:42', '2023-09-07 15:02:42'),
(68, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 15:22:27', '2023-09-07 15:22:27'),
(69, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 15:22:27', '2023-09-07 15:22:27'),
(84, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 15:29:01', '2023-09-07 15:29:01'),
(85, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-07 15:29:01', '2023-09-07 15:29:01'),
(88, 4, 2, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-08 01:42:38', '2023-09-08 01:42:38'),
(89, 1, 4, 'تم عمل طلب جديد من قبل ali', 'New order has been made from ali', '/orders', 0, '2023-09-08 01:42:38', '2023-09-08 01:42:38'),
(106, 4, 2, 'تم عمل طلب جديد من قبل ali mohamed', 'New order has been made from ali mohamed', '/orders', 0, '2023-09-15 22:12:05', '2023-09-15 22:12:05'),
(107, 1, 4, 'تم عمل طلب جديد من قبل ali mohamed', 'New order has been made from ali mohamed', '/orders', 0, '2023-09-15 22:12:05', '2023-09-15 22:12:05'),
(108, 4, 2, 'تم عمل طلب جديد من قبل ali mohamed', 'New order has been made from ali mohamed', '/orders', 0, '2023-09-16 02:36:53', '2023-09-16 02:36:53'),
(109, 1, 4, 'تم عمل طلب جديد من قبل ali mohamed', 'New order has been made from ali mohamed', '/orders', 0, '2023-09-16 02:36:53', '2023-09-16 02:36:53'),
(114, 4, 2, 'تم عمل طلب جديد من قبل ali mohamed', 'New order has been made from ali mohamed', '/orders', 0, '2023-09-16 02:41:38', '2023-09-16 02:41:38'),
(115, 1, 4, 'تم عمل طلب جديد من قبل ali mohamed', 'New order has been made from ali mohamed', '/orders', 0, '2023-09-16 02:41:38', '2023-09-16 02:41:38'),
(116, 4, 2, 'تم عمل طلب جديد من قبل ali mohamed', 'New order has been made from ali mohamed', '/orders', 0, '2023-09-16 02:43:47', '2023-09-16 02:43:47'),
(117, 1, 4, 'تم عمل طلب جديد من قبل ali mohamed', 'New order has been made from ali mohamed', '/orders', 0, '2023-09-16 02:43:47', '2023-09-16 02:43:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'visa',
  `has_coupon` tinyint(4) NOT NULL DEFAULT 0,
  `seller_profit` tinyint(4) NOT NULL DEFAULT 0,
  `financial_reconciliation_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `seller_id`, `payment_method`, `has_coupon`, `seller_profit`, `financial_reconciliation_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(10, 1, 2, 'visa', 0, 0, 1, NULL, '2023-09-07 14:50:33', '2023-09-07 14:50:33'),
(11, 1, 2, 'visa', 0, 0, NULL, NULL, '2023-09-07 14:52:32', '2023-09-07 14:52:32'),
(43, 1, 2, 'visa', 0, 0, 1, NULL, '2023-09-07 15:29:01', '2023-09-07 15:29:01'),
(45, 1, 2, 'visa', 0, 0, NULL, NULL, '2023-09-08 01:42:38', '2023-09-08 01:42:38'),
(54, 1, 2, 'visa', 0, 0, NULL, NULL, '2023-09-15 22:12:05', '2023-09-15 22:12:05'),
(55, 1, 2, 'visa', 0, 0, NULL, NULL, '2023-09-16 02:36:53', '2023-09-16 02:36:53'),
(59, 5, 2, 'visa', 0, 0, NULL, NULL, '2023-09-16 02:43:47', '2023-09-16 02:43:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders_addresses`
--

CREATE TABLE `orders_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `user_address_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_price` float NOT NULL,
  `days_delivery` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders_addresses`
--

INSERT INTO `orders_addresses` (`id`, `order_id`, `user_address_id`, `delivery_price`, `days_delivery`, `created_at`, `updated_at`) VALUES
(2, 54, 2, 0, 0, '2023-09-15 22:12:05', '2023-09-15 22:12:05'),
(3, 55, 1, 0, 0, '2023-09-16 02:36:53', '2023-09-16 02:36:53'),
(5, 59, 1, 3, 200, '2023-09-16 02:43:47', '2023-09-16 02:43:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders_items`
--

CREATE TABLE `orders_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders_items`
--

INSERT INTO `orders_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(6, 10, 2, 5, 100, '2023-09-07 14:50:33', '2023-09-07 14:50:33'),
(7, 43, 2, 5, 100, '2023-09-07 14:52:32', '2023-09-07 14:52:32'),
(11, 43, 2, 1, 70, '2023-09-07 15:29:01', '2023-09-07 15:29:01'),
(12, 45, 2, 1, 70, '2023-09-08 01:42:38', '2023-09-08 01:42:38'),
(17, 54, 2, 1, 70, '2023-09-15 22:12:05', '2023-09-15 22:12:05'),
(18, 55, 2, 1, 70, '2023-09-16 02:36:53', '2023-09-16 02:36:53'),
(19, 55, 6, 3, 50, '2023-09-16 02:36:53', '2023-09-16 02:36:53'),
(26, 59, 2, 1, 70, '2023-09-16 02:43:47', '2023-09-16 02:43:47'),
(27, 59, 6, 3, 50, '2023-09-16 02:43:47', '2023-09-16 02:43:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders_items_features`
--

CREATE TABLE `orders_items_features` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `product_feature_id` bigint(20) UNSIGNED NOT NULL,
  `price` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders_items_features`
--

INSERT INTO `orders_items_features` (`id`, `order_item_id`, `product_feature_id`, `price`, `created_at`, `updated_at`) VALUES
(1, 6, 3, 0.00, '2023-09-07 14:50:33', '2023-09-07 14:50:33'),
(2, 6, 4, 100.00, '2023-09-07 14:50:33', '2023-09-07 14:50:33'),
(3, 7, 3, 0.00, '2023-09-07 14:52:32', '2023-09-07 14:52:32'),
(4, 7, 4, 100.00, '2023-09-07 14:52:32', '2023-09-07 14:52:32'),
(11, 11, 3, 0.00, '2023-09-07 15:29:01', '2023-09-07 15:29:01'),
(12, 11, 4, 100.00, '2023-09-07 15:29:01', '2023-09-07 15:29:01'),
(13, 12, 3, 0.00, '2023-09-08 01:42:38', '2023-09-08 01:42:38'),
(14, 12, 4, 100.00, '2023-09-08 01:42:38', '2023-09-08 01:42:38'),
(23, 17, 3, 0.00, '2023-09-15 22:12:05', '2023-09-15 22:12:05'),
(24, 17, 4, 100.00, '2023-09-15 22:12:05', '2023-09-15 22:12:05'),
(25, 18, 3, 0.00, '2023-09-16 02:36:53', '2023-09-16 02:36:53'),
(26, 18, 4, 100.00, '2023-09-16 02:36:53', '2023-09-16 02:36:53'),
(27, 19, 3, 0.00, '2023-09-16 02:36:53', '2023-09-16 02:36:53'),
(28, 19, 4, 100.00, '2023-09-16 02:36:53', '2023-09-16 02:36:53'),
(41, 26, 3, 0.00, '2023-09-16 02:43:47', '2023-09-16 02:43:47'),
(42, 26, 4, 100.00, '2023-09-16 02:43:47', '2023-09-16 02:43:47'),
(43, 27, 3, 0.00, '2023-09-16 02:43:47', '2023-09-16 02:43:47'),
(44, 27, 4, 100.00, '2023-09-16 02:43:47', '2023-09-16 02:43:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders_items_rates`
--

CREATE TABLE `orders_items_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate_product_info` int(11) NOT NULL,
  `rate_product_services` int(11) NOT NULL,
  `rate_product_delivery` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders_items_rates`
--

INSERT INTO `orders_items_rates` (`id`, `user_id`, `order_item_id`, `comment`, `rate_product_info`, `rate_product_services`, `rate_product_delivery`, `created_at`, `updated_at`) VALUES
(3, 1, 6, 'very good', 3, 2, 3, '2023-09-08 01:18:09', '2023-09-08 01:18:09'),
(5, 1, 7, 'very good', 2, 1, 2, '2023-09-08 01:22:30', '2023-09-08 01:22:30'),
(6, 1, 11, 'very good', 3, 4, 1, '2023-09-08 01:22:39', '2023-09-08 01:22:39'),
(7, 5, 26, 'very good', 4, 5, 5, '2023-09-17 01:00:59', '2023-09-17 01:00:59'),
(9, 5, 27, 'very good', 4, 2, 1, '2023-09-17 01:00:59', '2023-09-17 01:00:59');

-- --------------------------------------------------------

--
-- Table structure for table `orders_shipment_infos`
--

CREATE TABLE `orders_shipment_infos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `content` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_actions`
--

CREATE TABLE `payment_actions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `money` int(11) NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `privileges`
--

CREATE TABLE `privileges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `page_id` bigint(20) UNSIGNED NOT NULL,
  `add` tinyint(4) NOT NULL,
  `update` tinyint(4) NOT NULL,
  `delete` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `ar_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ar_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `en_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `en_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `main_price` double(8,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `category_id`, `ar_name`, `ar_description`, `en_name`, `en_description`, `quantity`, `main_price`, `deleted_at`, `created_at`, `updated_at`) VALUES
(2, 2, 1, 'نعناع', 'وصف منتج النعناع', NULL, NULL, 8, 100.00, NULL, '2023-09-05 01:14:28', '2023-09-16 02:43:47'),
(4, 2, 1, 'نعناع', 'وصف منتج النعناع', NULL, NULL, 10, 100.00, NULL, '2023-09-06 22:50:40', '2023-09-06 22:50:40'),
(6, 2, 1, 'نعناع', 'وصف منتج النعناع', NULL, NULL, 1, 100.00, NULL, '2023-09-15 22:25:58', '2023-09-16 02:43:47'),
(7, 2, 1, 'نعناع', 'وصف منتج النعناع', NULL, NULL, 10, 100.00, NULL, '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(8, 2, 1, 'نعناع', 'وصف منتج النعناع', NULL, NULL, 10, 100.00, NULL, '2023-09-19 03:47:16', '2023-09-19 03:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `products_cares`
--

CREATE TABLE `products_cares` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `care_id` bigint(20) UNSIGNED NOT NULL,
  `time_number` int(11) NOT NULL,
  `time_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products_cares`
--

INSERT INTO `products_cares` (`id`, `user_id`, `product_id`, `care_id`, `time_number`, `time_type`, `type`, `created_at`, `updated_at`) VALUES
(1, 2, 7, 1, 3, 'minute', 'seller', '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(2, 2, 7, 1, 2, 'week', 'seller', '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(3, 2, 8, 1, 3, 'minute', 'seller', '2023-09-19 03:47:16', '2023-09-19 03:47:16'),
(4, 2, 8, 1, 2, 'hour', 'seller', '2023-09-19 03:47:16', '2023-09-19 03:47:16'),
(7, 1, 7, 2, 2, 'day', 'client', '2023-09-19 04:03:59', '2023-09-19 04:03:59');

-- --------------------------------------------------------

--
-- Table structure for table `products_deliveries`
--

CREATE TABLE `products_deliveries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `location_id` int(11) NOT NULL,
  `location_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double(8,2) NOT NULL,
  `days_delivery` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products_deliveries`
--

INSERT INTO `products_deliveries` (`id`, `product_id`, `location_id`, `location_type`, `price`, `days_delivery`, `created_at`, `updated_at`) VALUES
(2, 6, 1, 'city', 400.00, 3, '2023-09-15 22:25:58', '2023-09-15 22:25:58'),
(3, 6, 1, 'area', 100.00, 3, '2023-09-15 22:25:58', '2023-09-15 22:25:58'),
(4, 2, 1, 'area', 100.00, 3, '2023-09-15 22:25:58', '2023-09-15 22:25:58'),
(5, 7, 1, 'city', 400.00, 3, '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(6, 7, 1, 'area', 100.00, 3, '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(7, 8, 1, 'city', 400.00, 3, '2023-09-19 03:47:16', '2023-09-19 03:47:16'),
(8, 8, 1, 'area', 100.00, 3, '2023-09-19 03:47:16', '2023-09-19 03:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `products_discounts`
--

CREATE TABLE `products_discounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `discount` double(8,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products_discounts`
--

INSERT INTO `products_discounts` (`id`, `product_id`, `discount`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 2, 30.00, '2022-11-05', '2024-09-05', '2023-09-05 01:14:28', '2023-09-05 01:14:28'),
(2, 2, 50.00, '2029-09-05', '2030-09-05', '2023-09-05 01:14:28', '2023-09-05 01:14:28'),
(7, 4, 30.00, '2023-09-05', '2024-09-05', '2023-09-06 22:50:40', '2023-09-06 22:50:40'),
(8, 4, 50.00, '2021-09-05', '2030-09-05', '2023-09-06 22:50:40', '2023-09-06 22:50:40'),
(11, 6, 30.00, '2023-09-05', '2024-09-05', '2023-09-15 22:25:58', '2023-09-15 22:25:58'),
(12, 6, 50.00, '2021-09-05', '2030-09-05', '2023-09-15 22:25:58', '2023-09-15 22:25:58'),
(13, 7, 30.00, '2023-09-05', '2024-09-05', '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(14, 7, 50.00, '2021-09-05', '2030-09-05', '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(15, 8, 30.00, '2023-09-05', '2024-09-05', '2023-09-19 03:47:16', '2023-09-19 03:47:16'),
(16, 8, 50.00, '2021-09-05', '2030-09-05', '2023-09-19 03:47:16', '2023-09-19 03:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `products_features_prices`
--

CREATE TABLE `products_features_prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `category_feature_id` bigint(20) UNSIGNED NOT NULL,
  `price` double(8,2) NOT NULL DEFAULT 0.00,
  `note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products_features_prices`
--

INSERT INTO `products_features_prices` (`id`, `product_id`, `category_feature_id`, `price`, `note`, `created_at`, `updated_at`) VALUES
(3, 2, 1, 0.00, NULL, '2023-09-05 14:25:24', '2023-09-05 14:25:24'),
(4, 2, 2, 100.00, 'في  حالة ان المسافة لا تزيد عن ساعه تعديل زمن', '2023-09-05 14:25:24', '2023-09-05 14:25:24'),
(5, 4, 1, 0.00, NULL, '2023-09-06 22:50:40', '2023-09-06 22:50:40'),
(6, 4, 2, 100.00, 'في  حالة ان المسافة لا تزيد عن ساعه تعديل زمن', '2023-09-06 22:50:40', '2023-09-06 22:50:40'),
(9, 6, 1, 0.00, NULL, '2023-09-15 22:25:58', '2023-09-15 22:25:58'),
(10, 6, 2, 100.00, 'في  حالة ان المسافة لا تزيد عن ساعه تعديل زمن', '2023-09-15 22:25:58', '2023-09-15 22:25:58'),
(11, 7, 1, 0.00, NULL, '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(12, 7, 2, 100.00, 'في  حالة ان المسافة لا تزيد عن ساعه تعديل زمن', '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(13, 8, 1, 0.00, NULL, '2023-09-19 03:47:16', '2023-09-19 03:47:16'),
(14, 8, 2, 100.00, 'في  حالة ان المسافة لا تزيد عن ساعه تعديل زمن', '2023-09-19 03:47:16', '2023-09-19 03:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `products_questions_answers`
--

CREATE TABLE `products_questions_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `category_heading_questions_data_id` bigint(20) UNSIGNED NOT NULL,
  `ar_answer` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `en_answer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products_questions_answers`
--

INSERT INTO `products_questions_answers` (`id`, `product_id`, `category_heading_questions_data_id`, `ar_answer`, `en_answer`, `created_at`, `updated_at`) VALUES
(3, 2, 1, 'مره الساعه الرابعه عصرا', NULL, '2023-09-05 01:14:28', '2023-09-05 01:14:28'),
(4, 2, 1, 'مرة  الساعه العاشرة مساء', NULL, '2023-09-05 01:14:28', '2023-09-05 01:14:28'),
(9, 4, 1, 'مره الساعه الرابعه عصرا', NULL, '2023-09-06 22:50:40', '2023-09-06 22:50:40'),
(10, 4, 1, 'مرة  الساعه العاشرة مساء', NULL, '2023-09-06 22:50:40', '2023-09-06 22:50:40'),
(13, 6, 1, 'مره الساعه الرابعه عصرا', NULL, '2023-09-15 22:25:58', '2023-09-15 22:25:58'),
(14, 6, 1, 'مرة  الساعه العاشرة مساء', NULL, '2023-09-15 22:25:58', '2023-09-15 22:25:58'),
(15, 7, 1, 'مره الساعه الرابعه عصرا', NULL, '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(16, 7, 1, 'مرة  الساعه العاشرة مساء', NULL, '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(17, 8, 1, 'مره الساعه الرابعه عصرا', NULL, '2023-09-19 03:47:16', '2023-09-19 03:47:16'),
(18, 8, 1, 'مرة  الساعه العاشرة مساء', NULL, '2023-09-19 03:47:16', '2023-09-19 03:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `products_wholesale_prices`
--

CREATE TABLE `products_wholesale_prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `min_quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products_wholesale_prices`
--

INSERT INTO `products_wholesale_prices` (`id`, `product_id`, `min_quantity`, `price`, `created_at`, `updated_at`) VALUES
(1, 2, 10, 6, '2023-09-05 01:14:28', '2023-09-05 01:14:28'),
(2, 2, 20, 2, '2023-09-05 01:14:28', '2023-09-05 01:14:28'),
(7, 4, 10, 6, '2023-09-06 22:50:40', '2023-09-06 22:50:40'),
(8, 4, 20, 2, '2023-09-06 22:50:40', '2023-09-06 22:50:40'),
(11, 6, 10, 6, '2023-09-15 22:25:58', '2023-09-15 22:25:58'),
(12, 6, 20, 2, '2023-09-15 22:25:58', '2023-09-15 22:25:58'),
(13, 7, 10, 6, '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(14, 7, 20, 2, '2023-09-18 23:49:56', '2023-09-18 23:49:56'),
(15, 8, 10, 6, '2023-09-19 03:47:16', '2023-09-19 03:47:16'),
(16, 8, 20, 2, '2023-09-19 03:47:16', '2023-09-19 03:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `info` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', NULL, '2023-09-04 01:18:39', NULL),
(2, 'client', NULL, '2023-09-07 01:18:39', NULL),
(3, 'seller', NULL, '2023-09-07 01:18:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `searches`
--

CREATE TABLE `searches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `searches`
--

INSERT INTO `searches` (`id`, `user_id`, `item_id`, `type`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'products', '2023-09-05 23:41:05', '2023-09-05 23:41:05');

-- --------------------------------------------------------

--
-- Table structure for table `seens`
--

CREATE TABLE `seens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seens`
--

INSERT INTO `seens` (`id`, `item_id`, `type`, `count`, `created_at`, `updated_at`) VALUES
(1, 2, 'products', 39, '2023-09-05 22:12:05', '2023-09-19 00:32:23'),
(2, 1, 'articles', 8, '2023-09-09 22:26:37', '2023-09-09 22:51:47');

-- --------------------------------------------------------

--
-- Table structure for table `supports`
--

CREATE TABLE `supports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activation_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `block` tinyint(4) NOT NULL DEFAULT 0,
  `register_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activation_status` int(11) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `country_id`, `username`, `email`, `activation_code`, `phone`, `address`, `block`, `register_by`, `activation_status`, `remember_token`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'ali mohamed', 'ali@yahoo.com', '1289', '01152296646', '', 0, '', 1, NULL, NULL, '2023-09-04 00:41:14', '2023-09-08 02:27:05'),
(2, 3, 1, 'saad', 'saad@yahoo.com', '1289', '01005663932', '', 0, 'app', 1, NULL, NULL, '2023-09-04 00:41:14', '2023-09-04 01:06:47'),
(3, 3, 1, 'fatma', 'fatma@yahoo.com', '1289', '01152296646', '', 0, '', 1, NULL, NULL, '2023-09-04 00:41:14', '2023-09-04 01:06:47'),
(4, 1, 1, 'admin', 'admin@yahoo.com', '1289', '01152296646', '', 0, '', 1, NULL, NULL, '2023-09-04 00:41:14', '2023-09-04 01:06:47'),
(5, 2, 1, 'shehab mohamed', 'shehab@yahoo.com', '2046', '01152296644', NULL, 0, 'app', 0, NULL, NULL, '2023-09-17 00:08:02', '2023-09-17 00:26:51');

-- --------------------------------------------------------

--
-- Table structure for table `users_bank_infos`
--

CREATE TABLE `users_bank_infos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `owner_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_account` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_iban` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_bank_infos`
--

INSERT INTO `users_bank_infos` (`id`, `user_id`, `owner_name`, `bank_name`, `bank_account`, `bank_iban`, `created_at`, `updated_at`) VALUES
(1, 1, 'فهد العتيبي', 'البنك  الاهلي الدولي', '312312321EEEE', 'KSA3123123', '2023-09-04 02:05:54', '2023-09-04 02:05:54');

-- --------------------------------------------------------

--
-- Table structure for table `users_commercial_infos`
--

CREATE TABLE `users_commercial_infos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `commercial_register` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_card` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_commercial_infos`
--

INSERT INTO `users_commercial_infos` (`id`, `user_id`, `commercial_register`, `tax_card`, `created_at`, `updated_at`) VALUES
(1, 1, '31233123', '3123123', '2023-09-04 01:59:03', '2023-09-04 01:59:03');

-- --------------------------------------------------------

--
-- Table structure for table `users_products_cares`
--

CREATE TABLE `users_products_cares` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_products_cares`
--

INSERT INTO `users_products_cares` (`id`, `user_id`, `product_id`, `created_at`, `updated_at`) VALUES
(11, 1, 7, '2023-09-19 03:51:20', '2023-09-19 03:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `users_products_care_alerts`
--

CREATE TABLE `users_products_care_alerts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_care_id` bigint(20) UNSIGNED NOT NULL,
  `next_alert` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_products_care_alerts`
--

INSERT INTO `users_products_care_alerts` (`id`, `user_id`, `product_care_id`, `next_alert`, `created_at`, `updated_at`) VALUES
(8, 1, 1, '2023-09-19 06:56:02', '2023-09-19 03:51:20', '2023-09-19 03:53:02'),
(9, 1, 2, '2023-10-03 06:53:02', '2023-09-19 03:51:20', '2023-09-19 03:53:02'),
(12, 1, 7, '2023-09-21 07:03:59', '2023-09-19 04:03:59', '2023-09-19 04:03:59');

-- --------------------------------------------------------

--
-- Table structure for table `users_store_infos`
--

CREATE TABLE `users_store_infos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_store_infos`
--

INSERT INTO `users_store_infos` (`id`, `user_id`, `name`, `type`, `address`, `business_phone`, `business_email`, `created_at`, `updated_at`) VALUES
(1, 1, 'مزرعه الفاكهة', 'مزرعه', 'الرياض قسم اول', '01152296646', 'ali@yahoo.com', '2023-09-04 01:35:36', '2023-09-04 01:38:13');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `area_id` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_address` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `area_id`, `address`, `default_address`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'بجوار الحي الملكي', 1, NULL, '2023-09-14 02:25:36', '2023-09-14 02:28:16'),
(2, 1, 2, 'بجوار الحي المالك', 0, NULL, '2023-09-14 02:25:57', '2023-09-14 02:28:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `areas_city_id_foreign` (`city_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `articles_user_id_foreign` (`user_id`);

--
-- Indexes for table `articles_comments`
--
ALTER TABLE `articles_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `articles_comments_article_id_foreign` (`article_id`),
  ADD KEY `articles_comments_user_id_foreign` (`user_id`);

--
-- Indexes for table `cares`
--
ALTER TABLE `cares`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories_features`
--
ALTER TABLE `categories_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_features_category_id_foreign` (`category_id`);

--
-- Indexes for table `categories_heading_questions`
--
ALTER TABLE `categories_heading_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_heading_questions_category_id_foreign` (`category_id`);

--
-- Indexes for table `categories_heading_questions_datas`
--
ALTER TABLE `categories_heading_questions_datas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_heading_question_id` (`category_heading_question_id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chats_sender_id_foreign` (`sender_id`),
  ADD KEY `chats_receiver_id_foreign` (`receiver_id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cities_country_id_foreign` (`government_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `favourites_user_id_foreign` (`user_id`),
  ADD KEY `favourites_product_id_foreign` (`product_id`);

--
-- Indexes for table `financial_reconciliations`
--
ALTER TABLE `financial_reconciliations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `financial_reconciliations_user_id_foreign` (`user_id`);

--
-- Indexes for table `financial_reconciliations_proit_percentages`
--
ALTER TABLE `financial_reconciliations_proit_percentages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `followers_user_id_foreign` (`user_id`),
  ADD KEY `followers_following_id_foreign` (`following_id`);

--
-- Indexes for table `governments`
--
ALTER TABLE `governments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `important_infos`
--
ALTER TABLE `important_infos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `likes_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_sender_id_foreign` (`sender_id`),
  ADD KEY `notifications_receiver_id_foreign` (`receiver_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_seller_id_foreign` (`seller_id`);

--
-- Indexes for table `orders_addresses`
--
ALTER TABLE `orders_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_addresses_order_id_foreign` (`order_id`),
  ADD KEY `orders_addresses_user_address_id_foreign` (`user_address_id`);

--
-- Indexes for table `orders_items`
--
ALTER TABLE `orders_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_items_order_id_foreign` (`order_id`),
  ADD KEY `orders_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `orders_items_features`
--
ALTER TABLE `orders_items_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_items_features_order_item_id_foreign` (`order_item_id`),
  ADD KEY `orders_items_features_product_feature_id_foreign` (`product_feature_id`);

--
-- Indexes for table `orders_items_rates`
--
ALTER TABLE `orders_items_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_items_rates_user_id_foreign` (`user_id`),
  ADD KEY `orders_items_rates_order_item_id_foreign` (`order_item_id`);

--
-- Indexes for table `orders_shipment_infos`
--
ALTER TABLE `orders_shipment_infos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_shipment_infos_user_id_foreign` (`user_id`),
  ADD KEY `orders_shipment_infos_order_id_foreign` (`order_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payment_actions`
--
ALTER TABLE `payment_actions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `privileges`
--
ALTER TABLE `privileges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `privileges_role_id_foreign` (`role_id`),
  ADD KEY `privileges_page_id_foreign` (`page_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products_cares`
--
ALTER TABLE `products_cares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_cares_user_id_foreign` (`user_id`),
  ADD KEY `products_cares_product_id_foreign` (`product_id`),
  ADD KEY `products_cares_care_id_foreign` (`care_id`);

--
-- Indexes for table `products_deliveries`
--
ALTER TABLE `products_deliveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_deliveries_product_id_foreign` (`product_id`);

--
-- Indexes for table `products_discounts`
--
ALTER TABLE `products_discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_discounts_product_id_foreign` (`product_id`);

--
-- Indexes for table `products_features_prices`
--
ALTER TABLE `products_features_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_features_prices_product_id_foreign` (`product_id`),
  ADD KEY `products_features_prices_category_feature_id_foreign` (`category_feature_id`);

--
-- Indexes for table `products_questions_answers`
--
ALTER TABLE `products_questions_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_questions_answers_product_id_foreign` (`product_id`),
  ADD KEY `categories_heading_questions_data_id` (`category_heading_questions_data_id`);

--
-- Indexes for table `products_wholesale_prices`
--
ALTER TABLE `products_wholesale_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_wholesale_prices_product_id_foreign` (`product_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `searches`
--
ALTER TABLE `searches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `searches_user_id_foreign` (`user_id`);

--
-- Indexes for table `seens`
--
ALTER TABLE `seens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supports`
--
ALTER TABLE `supports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `users_bank_infos`
--
ALTER TABLE `users_bank_infos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_bank_infos_user_id_foreign` (`user_id`);

--
-- Indexes for table `users_commercial_infos`
--
ALTER TABLE `users_commercial_infos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_commercial_infos_user_id_foreign` (`user_id`);

--
-- Indexes for table `users_products_cares`
--
ALTER TABLE `users_products_cares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_products_cares_user_id_foreign` (`user_id`),
  ADD KEY `users_products_cares_product_id_foreign` (`product_id`);

--
-- Indexes for table `users_products_care_alerts`
--
ALTER TABLE `users_products_care_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_products_care_alerts_user_id_foreign` (`user_id`),
  ADD KEY `users_products_care_alerts_product_care_id_foreign` (`product_care_id`);

--
-- Indexes for table `users_store_infos`
--
ALTER TABLE `users_store_infos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_store_infos_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_addresses_area_id_foreign` (`area_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `areas`
--
ALTER TABLE `areas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `articles_comments`
--
ALTER TABLE `articles_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cares`
--
ALTER TABLE `cares`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories_features`
--
ALTER TABLE `categories_features`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories_heading_questions`
--
ALTER TABLE `categories_heading_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories_heading_questions_datas`
--
ALTER TABLE `categories_heading_questions_datas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favourites`
--
ALTER TABLE `favourites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `financial_reconciliations`
--
ALTER TABLE `financial_reconciliations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `financial_reconciliations_proit_percentages`
--
ALTER TABLE `financial_reconciliations_proit_percentages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `governments`
--
ALTER TABLE `governments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `important_infos`
--
ALTER TABLE `important_infos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `orders_addresses`
--
ALTER TABLE `orders_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders_items`
--
ALTER TABLE `orders_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `orders_items_features`
--
ALTER TABLE `orders_items_features`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `orders_items_rates`
--
ALTER TABLE `orders_items_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders_shipment_infos`
--
ALTER TABLE `orders_shipment_infos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_actions`
--
ALTER TABLE `payment_actions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `privileges`
--
ALTER TABLE `privileges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products_cares`
--
ALTER TABLE `products_cares`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products_deliveries`
--
ALTER TABLE `products_deliveries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products_discounts`
--
ALTER TABLE `products_discounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `products_features_prices`
--
ALTER TABLE `products_features_prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `products_questions_answers`
--
ALTER TABLE `products_questions_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products_wholesale_prices`
--
ALTER TABLE `products_wholesale_prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `searches`
--
ALTER TABLE `searches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `seens`
--
ALTER TABLE `seens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `supports`
--
ALTER TABLE `supports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users_bank_infos`
--
ALTER TABLE `users_bank_infos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users_commercial_infos`
--
ALTER TABLE `users_commercial_infos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users_products_cares`
--
ALTER TABLE `users_products_cares`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users_products_care_alerts`
--
ALTER TABLE `users_products_care_alerts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users_store_infos`
--
ALTER TABLE `users_store_infos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `areas`
--
ALTER TABLE `areas`
  ADD CONSTRAINT `areas_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `articles_comments`
--
ALTER TABLE `articles_comments`
  ADD CONSTRAINT `articles_comments_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `articles_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `categories_features`
--
ALTER TABLE `categories_features`
  ADD CONSTRAINT `categories_features_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `categories_heading_questions`
--
ALTER TABLE `categories_heading_questions`
  ADD CONSTRAINT `categories_heading_questions_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `categories_heading_questions_datas`
--
ALTER TABLE `categories_heading_questions_datas`
  ADD CONSTRAINT `fk_category_heading_question_id` FOREIGN KEY (`category_heading_question_id`) REFERENCES `categories_heading_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chats_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_country_id_foreign` FOREIGN KEY (`government_id`) REFERENCES `governments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `favourites`
--
ALTER TABLE `favourites`
  ADD CONSTRAINT `favourites_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favourites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `financial_reconciliations`
--
ALTER TABLE `financial_reconciliations`
  ADD CONSTRAINT `financial_reconciliations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_following_id_foreign` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `followers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders_addresses`
--
ALTER TABLE `orders_addresses`
  ADD CONSTRAINT `orders_addresses_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_addresses_user_address_id_foreign` FOREIGN KEY (`user_address_id`) REFERENCES `user_addresses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders_items`
--
ALTER TABLE `orders_items`
  ADD CONSTRAINT `orders_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders_items_features`
--
ALTER TABLE `orders_items_features`
  ADD CONSTRAINT `orders_items_features_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `orders_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_items_features_product_feature_id_foreign` FOREIGN KEY (`product_feature_id`) REFERENCES `products_features_prices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders_items_rates`
--
ALTER TABLE `orders_items_rates`
  ADD CONSTRAINT `orders_items_rates_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `orders_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_items_rates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders_shipment_infos`
--
ALTER TABLE `orders_shipment_infos`
  ADD CONSTRAINT `orders_shipment_infos_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_shipment_infos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `privileges`
--
ALTER TABLE `privileges`
  ADD CONSTRAINT `privileges_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `privileges_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products_cares`
--
ALTER TABLE `products_cares`
  ADD CONSTRAINT `products_cares_care_id_foreign` FOREIGN KEY (`care_id`) REFERENCES `cares` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `products_cares_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `products_cares_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products_deliveries`
--
ALTER TABLE `products_deliveries`
  ADD CONSTRAINT `products_deliveries_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products_discounts`
--
ALTER TABLE `products_discounts`
  ADD CONSTRAINT `products_discounts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products_features_prices`
--
ALTER TABLE `products_features_prices`
  ADD CONSTRAINT `products_features_prices_category_feature_id_foreign` FOREIGN KEY (`category_feature_id`) REFERENCES `categories_features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `products_features_prices_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products_questions_answers`
--
ALTER TABLE `products_questions_answers`
  ADD CONSTRAINT `products_questions_answers_ibfk_1` FOREIGN KEY (`category_heading_questions_data_id`) REFERENCES `categories_heading_questions_datas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `products_questions_answers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products_wholesale_prices`
--
ALTER TABLE `products_wholesale_prices`
  ADD CONSTRAINT `products_wholesale_prices_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `searches`
--
ALTER TABLE `searches`
  ADD CONSTRAINT `searches_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_bank_infos`
--
ALTER TABLE `users_bank_infos`
  ADD CONSTRAINT `users_bank_infos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_commercial_infos`
--
ALTER TABLE `users_commercial_infos`
  ADD CONSTRAINT `users_commercial_infos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_products_cares`
--
ALTER TABLE `users_products_cares`
  ADD CONSTRAINT `users_products_cares_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_products_cares_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_products_care_alerts`
--
ALTER TABLE `users_products_care_alerts`
  ADD CONSTRAINT `users_products_care_alerts_product_care_id_foreign` FOREIGN KEY (`product_care_id`) REFERENCES `products_cares` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_products_care_alerts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_store_infos`
--
ALTER TABLE `users_store_infos`
  ADD CONSTRAINT `users_store_infos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
