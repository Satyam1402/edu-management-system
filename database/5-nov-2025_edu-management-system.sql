-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2025 at 01:25 PM
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
-- Database: `edu-management-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `certificate_request_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `issued_date` date DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `franchise_id` bigint(20) UNSIGNED DEFAULT NULL,
  `number` varchar(255) NOT NULL,
  `status` enum('requested','approved','issued') NOT NULL DEFAULT 'requested',
  `issued_at` timestamp NULL DEFAULT NULL,
  `issued_by` bigint(20) UNSIGNED DEFAULT NULL,
  `valid_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `certificate_request_id`, `student_id`, `title`, `description`, `issued_date`, `course_id`, `franchise_id`, `number`, `status`, `issued_at`, `issued_by`, `valid_until`, `created_at`, `updated_at`) VALUES
(11, NULL, 42, NULL, NULL, NULL, 4, 4, 'UJLXVDDG', 'issued', '2025-10-22 23:15:45', NULL, NULL, '2025-10-22 23:14:56', '2025-10-29 00:10:05'),
(12, NULL, 43, 'new certificate', 'thisis new ', '2025-10-26', 2, 4, '012345', 'requested', NULL, NULL, NULL, '2025-10-26 01:24:09', '2025-10-29 00:10:05');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_requests`
--

CREATE TABLE `certificate_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `franchise_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `requested_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `certificate_requests`
--

INSERT INTO `certificate_requests` (`id`, `enrollment_id`, `franchise_id`, `student_id`, `course_id`, `payment_id`, `status`, `note`, `approved_by`, `approved_at`, `rejected_by`, `rejected_at`, `rejection_reason`, `admin_notes`, `requested_at`, `created_at`, `updated_at`) VALUES
(1, NULL, 4, 43, 2, 24, 'approved', NULL, 1, '2025-10-28 03:27:57', NULL, NULL, NULL, NULL, '2025-10-27 06:24:54', '2025-10-27 06:24:54', '2025-10-28 03:27:57');

-- --------------------------------------------------------

--
-- Table structure for table `collections`
--

CREATE TABLE `collections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `franchise_id` bigint(20) UNSIGNED NOT NULL,
  `period` varchar(255) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `commission_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','processed','paid') NOT NULL DEFAULT 'pending',
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `certificate_fee` decimal(8,2) NOT NULL DEFAULT 100.00,
  `fee` decimal(10,2) DEFAULT NULL,
  `discount_fee` decimal(10,2) DEFAULT NULL COMMENT 'Discounted fee if applicable',
  `is_free` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Is this course free',
  `franchise_fee` decimal(10,2) DEFAULT NULL COMMENT 'Special fee for franchise students',
  `fee_notes` text DEFAULT NULL COMMENT 'Additional notes about pricing',
  `duration_months` int(11) DEFAULT NULL,
  `curriculum` longtext DEFAULT NULL,
  `prerequisites` text DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') DEFAULT 'beginner',
  `learning_outcomes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`learning_outcomes`)),
  `certificate_template` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `max_students` int(11) DEFAULT NULL,
  `passing_percentage` decimal(5,2) DEFAULT 60.00,
  `instructor_name` varchar(255) DEFAULT NULL,
  `instructor_email` varchar(255) DEFAULT NULL,
  `course_image` varchar(255) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `is_featured` tinyint(1) DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive','draft','archived') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `code`, `description`, `certificate_fee`, `fee`, `discount_fee`, `is_free`, `franchise_fee`, `fee_notes`, `duration_months`, `curriculum`, `prerequisites`, `level`, `learning_outcomes`, `certificate_template`, `category`, `max_students`, `passing_percentage`, `instructor_name`, `instructor_email`, `course_image`, `tags`, `is_featured`, `deleted_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Web Development Bootcamp', 'WEB001', 'Complete full-stack web development course', 100.00, 25000.00, NULL, 0, NULL, NULL, 6, NULL, NULL, 'beginner', NULL, NULL, NULL, NULL, 60.00, NULL, NULL, NULL, NULL, 0, '2025-10-22 05:58:44', 'active', '2025-10-16 04:55:09', '2025-10-22 05:58:44'),
(2, 'Digital Marketing Mastery', 'DM002', 'Comprehensive digital marketing course', 100.00, 1.00, NULL, 0, NULL, NULL, 3, NULL, NULL, 'beginner', NULL, NULL, 'marketing', NULL, 60.00, NULL, NULL, NULL, NULL, 1, NULL, 'active', '2025-10-16 04:55:09', '2025-10-23 08:11:16'),
(3, 'Data Science Fundamentals', 'DS003', 'Introduction to data science and analytics', 100.00, 1.00, NULL, 0, NULL, NULL, 8, NULL, NULL, 'beginner', NULL, NULL, 'technology', NULL, 60.00, NULL, NULL, NULL, NULL, 0, NULL, 'active', '2025-10-16 04:55:09', '2025-10-23 08:10:54'),
(4, 'Advanced Web Development', 'AWD2025', 'Complete web development course with modern technologies and frameworks', 100.00, 1.00, NULL, 0, NULL, NULL, 8, 'HTML5, CSS3, JavaScript ES6+, React, Node.js, Laravel, MySQL, Git, AWS deployment', 'Basic computer knowledge, willingness to learn', 'advanced', '[\"Build full-stack web applications\",\"Master React and Laravel\",\"Deploy applications to cloud\"]', NULL, 'technology', 30, 70.00, 'John Smith', 'john@example.com', NULL, '[\"react\",\"laravel\",\"javascript\",\"php\",\"mysql\"]', 0, NULL, 'active', '2025-10-17 01:29:40', '2025-10-23 08:10:25'),
(5, 'aaaaa', 'CRS-KMDVXJ', 'vgbnvvnbvbn', 100.00, 1000.00, 20.00, 0, 999.00, 'xcffxfddf', 3, NULL, NULL, 'beginner', NULL, NULL, 'technology', NULL, 60.00, NULL, NULL, NULL, NULL, 1, '2025-10-30 00:43:06', 'active', '2025-10-30 00:42:44', '2025-10-30 00:43:06'),
(6, 'Python AI Course', '152630', 'python course', 100.00, 1100.00, 10.00, 0, 1000.00, 'python new course', 3, NULL, NULL, 'intermediate', NULL, NULL, 'technology', NULL, 60.00, NULL, NULL, NULL, NULL, 1, NULL, 'active', '2025-10-30 00:45:31', '2025-10-30 00:57:43');

-- --------------------------------------------------------

--
-- Table structure for table `course_enrollments`
--

CREATE TABLE `course_enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `franchise_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('enrolled','completed','dropped') NOT NULL DEFAULT 'enrolled',
  `enrollment_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_enrollments`
--

INSERT INTO `course_enrollments` (`id`, `student_id`, `course_id`, `franchise_id`, `status`, `enrollment_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 44, 2, 4, 'enrolled', '2025-10-29', 'this is enrolled anshu only', '2025-10-29 06:40:19', '2025-10-29 06:40:19');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `franchise_id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payment_method` enum('cash','card','bank_transfer','upi') DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('paid','pending','partial') NOT NULL DEFAULT 'pending',
  `status` enum('active','completed','cancelled','on_hold') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `completion_date` timestamp NULL DEFAULT NULL,
  `grade` varchar(255) DEFAULT NULL,
  `certificate_issued` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `franchise_id`, `enrollment_date`, `payment_method`, `amount_paid`, `payment_status`, `status`, `notes`, `completion_date`, `grade`, `certificate_issued`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 4, '2025-10-30 02:35:14', 'cash', 100.00, 'paid', 'active', 'Test enrollment', NULL, NULL, 0, '2025-10-30 02:35:14', '2025-10-30 02:35:14', NULL),
(2, 10, 2, 4, '2025-10-30 02:45:00', 'cash', 1.00, 'paid', 'active', 'this is aditya ', NULL, NULL, 0, '2025-10-30 02:45:00', '2025-10-30 02:45:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `mode` enum('offline','paper') NOT NULL DEFAULT 'offline',
  `exam_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 180,
  `total_marks` int(11) NOT NULL DEFAULT 100,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_attempts`
--

CREATE TABLE `exam_attempts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `score` int(11) DEFAULT NULL,
  `total_marks` int(11) NOT NULL DEFAULT 100,
  `result` enum('pass','fail','absent') DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `marks_obtained` int(11) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `result` enum('pass','fail') NOT NULL,
  `exam_start_time` datetime DEFAULT NULL,
  `exam_end_time` datetime DEFAULT NULL,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`)),
  `remarks` text DEFAULT NULL,
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
-- Table structure for table `franchises`
--

CREATE TABLE `franchises` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `established_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `franchises`
--

INSERT INTO `franchises` (`id`, `name`, `code`, `email`, `phone`, `address`, `city`, `state`, `pincode`, `contact_person`, `established_date`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(4, 'SK Enterprises', '456321', 'sk@gmail.com', '1234567890', 'street 123 capetown ,Kumhari ', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2025-10-17 05:18:44', '2025-10-17 05:18:44'),
(5, 'Alpha strategy ', 'ALPSTR001', 'satyamkprajapati2001@gmail.com', '1234567890', 'STR C9 HOUSING BOARD COLONY', 'BHILAI KUMHARI', 'Chhattisgarh', '490042', 'tester', '2000-10-02', 'sasasasa', 'active', '2025-10-19 11:40:23', '2025-10-19 12:25:22');

-- --------------------------------------------------------

--
-- Table structure for table `franchise_wallets`
--

CREATE TABLE `franchise_wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `franchise_id` bigint(20) UNSIGNED NOT NULL,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `franchise_wallets`
--

INSERT INTO `franchise_wallets` (`id`, `franchise_id`, `balance`, `created_at`, `updated_at`) VALUES
(1, 4, 1000.00, '2025-11-05 05:39:08', '2025-11-05 06:54:38');

-- --------------------------------------------------------

--
-- Table structure for table `franchise_wallet_transactions`
--

CREATE TABLE `franchise_wallet_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `franchise_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `source` varchar(255) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'completed',
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `franchise_wallet_transactions`
--

INSERT INTO `franchise_wallet_transactions` (`id`, `franchise_id`, `type`, `amount`, `source`, `payment_method`, `status`, `reference_id`, `meta`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 4, 'credit', 1000.00, 'wallet_topup', 'razorpay', 'completed', NULL, '\"{\\\"ip_address\\\":\\\"127.0.0.1\\\",\\\"user_agent\\\":\\\"Mozilla\\\\\\/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko\\\\\\/20100101 Firefox\\\\\\/144.0\\\",\\\"initiated_at\\\":\\\"2025-11-05T12:23:59.176354Z\\\"}\"', '2025-11-05 06:54:38', '2025-11-05 06:53:59', '2025-11-05 06:54:38');

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
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_10_16_073416_create_permission_tables', 1),
(6, '2025_10_16_074011_create_franchises_table', 1),
(7, '2025_10_16_074023_create_students_table', 1),
(8, '2025_10_16_074045_create_courses_table', 1),
(9, '2025_10_16_074056_create_exams_table', 1),
(10, '2025_10_16_074111_create_exam_attempts_table', 1),
(11, '2025_10_16_074127_create_certificates_table', 1),
(12, '2025_10_16_074138_create_payments_table', 1),
(13, '2025_10_16_074150_create_collections_table', 1),
(14, '2025_10_17_062326_create_exam_results_table', 2),
(15, '2025_10_17_062628_add_additional_fields_to_students_table', 3),
(16, '2025_10_17_062645_add_additional_fields_to_courses_table', 4),
(17, '2025_10_17_082836_add_franchise_id_to_users_table', 5),
(18, '2025_10_19_164445_add_additional_fields_to_franchises_table', 6),
(19, '2025_10_23_132118_add_payment_token_to_payments_table', 7),
(20, '2025_10_25_123844_add_role_to_users_table', 8),
(21, '2025_10_26_063103_add_fields_to_certificates_table', 9),
(22, '2025_10_27_063349_create_certificate_requests_table', 10),
(23, '2025_10_27_120800_add_qr_data_to_payments_table', 11),
(24, '2025_10_28_055853_add_approval_fields_to_certificate_requests_table', 12),
(25, '2025_10_28_084459_add_certificate_request_id_to_certificates_table', 13),
(26, '2025_10_29_052336_add_franchise_id_and_issued_by_to_certificates_table', 14),
(27, '2025_10_29_115929_create_course_enrollments_table', 15),
(28, '2025_10_30_051615_add_pricing_fields_to_courses_table', 16),
(29, '2025_10_30_064131_create_enrollments_table', 17),
(30, '2025_10_30_081332_add_phone_to_users_table', 18),
(31, '2025_11_02_051135_add_enrollment_id_to_certificate_requests_table', 19),
(32, '2025_11_02_115521_create_franchise_wallets_table', 19),
(33, '2025_11_02_115606_create_franchise_wallet_transactions_table', 19),
(34, '2025_11_03_175701_add_status_and_payment_method_to_franchise_wallet_transactions', 19),
(35, '2025_11_05_122210_add_certificate_fee_to_courses_table', 20);

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
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 5),
(2, 'App\\Models\\User', 6),
(2, 'App\\Models\\User', 7),
(2, 'App\\Models\\User', 8),
(2, 'App\\Models\\User', 9);

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
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `payment_token` varchar(32) DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'INR',
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `gateway` varchar(255) DEFAULT NULL,
  `gateway_order_id` varchar(255) DEFAULT NULL,
  `gateway_payment_id` varchar(255) DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `qr_data` text DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_token`, `student_id`, `course_id`, `amount`, `currency`, `status`, `gateway`, `gateway_order_id`, `gateway_payment_id`, `gateway_response`, `qr_data`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 'ORD68F9BE9D125AA', NULL, 42, 2, 15000.00, 'INR', 'refunded', NULL, NULL, 'MANUAL_1761198347', '{\"manual_completion\":true,\"completed_by\":1,\"refund_processed\":true,\"refunded_by\":1,\"refunded_at\":\"2025-10-23T06:19:43.947224Z\",\"refund_reason\":\"Manual refund\"}', NULL, '2025-10-23 00:15:47', '2025-10-23 00:05:25', '2025-10-23 00:49:43'),
(2, 'ORD68F9C1CC31826', NULL, 42, 3, 30000.00, 'INR', 'refunded', 'manual', NULL, 'MANUAL_1761198579', '{\"manual_completion\":true,\"completed_by\":1,\"refund_processed\":true,\"refunded_by\":1,\"refunded_at\":\"2025-10-23T06:19:51.229880Z\",\"refund_reason\":\"Manual refund\"}', NULL, '2025-10-23 00:19:39', '2025-10-23 00:19:00', '2025-10-23 00:49:51'),
(5, 'ORD68F9CD3A13081', NULL, 41, 2, 15000.00, 'INR', 'refunded', 'razorpay', 'order_RWomhtmkppvm9g', 'pay_RWopwF2PuV2IH3', '{\"razorpay_payment_id\":\"pay_RWopwF2PuV2IH3\",\"razorpay_order_id\":\"order_RWomhtmkppvm9g\",\"razorpay_signature\":\"fb903e7838704285ec03215e023c3fc4277ef9cc3d01f14286aa1eaa64d83f18\",\"refund_processed\":true,\"refunded_by\":1,\"refunded_at\":\"2025-10-25T11:47:23.745408Z\",\"refund_reason\":\"Manual refund\"}', NULL, '2025-10-23 01:11:04', '2025-10-23 01:07:46', '2025-10-25 06:17:23'),
(6, 'ORD68F9CE9C17CE6', NULL, 41, 3, 30000.00, 'INR', 'completed', 'razorpay', 'order_RWoswEDferOcoP', 'pay_RWotC1HKxZ9Mqf', '{\"razorpay_payment_id\":\"pay_RWotC1HKxZ9Mqf\",\"razorpay_order_id\":\"order_RWoswEDferOcoP\",\"razorpay_signature\":\"c0285383753b176de0cbfa6c5b96625181f6c702909d191bef1886785c3a6cc1\"}', NULL, '2025-10-23 01:14:27', '2025-10-23 01:13:40', '2025-10-23 01:14:27'),
(11, 'ORD68FA1805372B5', NULL, 41, 2, 15000.00, 'INR', 'completed', 'razorpay', 'order_RWuDq9XBEUJ9HN', 'pay_RWuFQ8Ap058QIg', '{\"razorpay_payment_id\":\"pay_RWuFQ8Ap058QIg\",\"razorpay_order_id\":\"order_RWuDq9XBEUJ9HN\",\"razorpay_signature\":\"3c88ba881c0f852dcda5e049c5a5272088c8255400cadc32cc7d711c61a556e0\"}', NULL, '2025-10-23 06:28:39', '2025-10-23 06:26:53', '2025-10-23 06:28:39'),
(12, 'ORD68FA2E61AAFAD', NULL, 42, 2, 15000.00, 'INR', 'pending', 'upi', NULL, NULL, NULL, NULL, NULL, '2025-10-23 08:02:17', '2025-10-23 08:02:17'),
(13, 'ORD68FA2F3246481', NULL, 42, 4, 25000.00, 'INR', 'pending', 'upi', NULL, NULL, NULL, NULL, NULL, '2025-10-23 08:05:46', '2025-10-23 08:05:46'),
(14, 'ORD68FA308DAC026', NULL, 42, 4, 1.00, 'INR', 'pending', 'upi', NULL, NULL, NULL, NULL, NULL, '2025-10-23 08:11:33', '2025-10-23 08:11:33'),
(15, 'ORD68FF4D521E6DB', NULL, 42, 4, 1.00, 'INR', 'pending', 'upi', NULL, NULL, NULL, NULL, NULL, '2025-10-27 05:15:38', '2025-10-27 05:15:38'),
(16, 'ORD68FF500F06E73', NULL, 43, 3, 1.00, 'INR', 'pending', 'razorpay', NULL, NULL, NULL, NULL, NULL, '2025-10-27 05:27:19', '2025-10-27 05:27:19'),
(17, 'ORD68FF52F8982B1', NULL, 43, 2, 11.00, 'INR', 'pending', 'razorpay', 'order_RaSxZDEtB8ZFaE', NULL, NULL, NULL, NULL, '2025-10-27 05:39:44', '2025-11-01 06:22:49'),
(18, 'ORD68FF53C61379A', NULL, 43, 2, 11.00, 'INR', 'pending', 'razorpay', NULL, NULL, NULL, NULL, NULL, '2025-10-27 05:43:10', '2025-10-27 05:43:10'),
(19, 'ORD68FF55F504261', NULL, 43, 2, 11.00, 'INR', 'pending', 'razorpay', 'order_RYTlwjBSRe3UI1', NULL, NULL, NULL, NULL, '2025-10-27 05:52:29', '2025-10-27 05:52:31'),
(20, 'ORD68FF5605E5B64', NULL, 43, 2, 1.00, 'INR', 'pending', 'razorpay', 'order_RYTmERs8udt9RJ', NULL, NULL, NULL, NULL, '2025-10-27 05:52:45', '2025-10-27 05:52:47'),
(21, 'ORD68FF57E31D0B8', NULL, 43, 2, 1.00, 'INR', 'pending', 'razorpay', 'order_RYTudXEvEuazxB', NULL, NULL, NULL, NULL, '2025-10-27 06:00:43', '2025-10-27 06:00:44'),
(22, 'ORD68FF58D6C8276', NULL, 43, 2, 1.00, 'INR', 'pending', 'razorpay', 'order_RYTyvX4Zam4euc', NULL, NULL, NULL, NULL, '2025-10-27 06:04:46', '2025-10-27 06:04:48'),
(23, 'ORD68FF590285752', NULL, 43, 2, 1.00, 'INR', 'pending', 'razorpay', 'order_RYTzh3XoNItQ5k', NULL, NULL, NULL, NULL, '2025-10-27 06:05:30', '2025-10-27 06:05:32'),
(24, 'ORD1761565408', NULL, 43, 2, 12.00, 'INR', 'completed', 'manual', NULL, 'MANUAL_1761565408', NULL, NULL, '2025-10-27 06:13:28', '2025-10-27 06:13:28', '2025-10-27 06:13:28'),
(25, 'ORD690053D0C4D9C', NULL, 41, 4, 1.00, 'INR', 'pending', 'upi', NULL, NULL, NULL, NULL, NULL, '2025-10-27 23:55:36', '2025-10-27 23:55:36'),
(26, 'ORD690342C606E12', NULL, 45, 3, 1.00, 'INR', 'pending', 'upi', NULL, NULL, NULL, NULL, NULL, '2025-10-30 05:19:42', '2025-10-30 05:19:42'),
(27, 'ORD6905F43C90472', NULL, 46, 6, 1.00, 'INR', 'completed', 'manual', NULL, 'MANUAL_1761997884', NULL, '', '2025-11-01 06:21:24', '2025-11-01 06:21:24', '2025-11-01 06:21:24'),
(28, 'ORD6905FA253CBD6', NULL, 46, 6, 1100.00, 'INR', 'completed', 'manual', NULL, 'MANUAL_1761999397', NULL, '', '2025-11-01 06:46:37', '2025-11-01 06:46:37', '2025-11-01 06:46:37');

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
(1, 'manage_franchises', 'web', '2025-10-16 04:55:08', '2025-10-16 04:55:08'),
(2, 'manage_students', 'web', '2025-10-16 04:55:08', '2025-10-16 04:55:08'),
(3, 'manage_courses', 'web', '2025-10-16 04:55:08', '2025-10-16 04:55:08'),
(4, 'manage_exams', 'web', '2025-10-16 04:55:08', '2025-10-16 04:55:08'),
(5, 'manage_certificates', 'web', '2025-10-16 04:55:08', '2025-10-16 04:55:08'),
(6, 'view_reports', 'web', '2025-10-16 04:55:08', '2025-10-16 04:55:08'),
(7, 'manage_payments', 'web', '2025-10-16 04:55:08', '2025-10-16 04:55:08');

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
(1, 'super_admin', 'web', '2025-10-16 04:55:08', '2025-10-16 04:55:08'),
(2, 'franchise', 'web', '2025-10-16 04:55:08', '2025-10-16 04:55:08');

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
(2, 2),
(3, 1),
(4, 1),
(4, 2),
(5, 1),
(6, 1),
(6, 2),
(7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `franchise_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_phone` varchar(15) DEFAULT NULL,
  `status` enum('active','inactive','graduated','dropped','suspended') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL,
  `batch` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `franchise_id`, `name`, `email`, `phone`, `date_of_birth`, `gender`, `address`, `city`, `state`, `pincode`, `guardian_name`, `guardian_phone`, `status`, `created_at`, `updated_at`, `course_id`, `enrollment_date`, `batch`, `notes`, `profile_photo`, `deleted_at`) VALUES
(39, 'STU000001', 4, 'anuj prjapapati', 'anuj@gmail.com', '1234567890', '2025-05-15', 'male', 'kumhari', 'bhilai', 'cg', '481200', 'neel kamal', '1234567890', 'active', '2025-10-21 02:40:46', '2025-10-21 02:52:18', 1, '2025-10-21', 'batch-2025', NULL, NULL, '2025-10-21 02:52:18'),
(40, 'STU000040', 4, 'Alpha strategy ', 'satyamkprajapati2001@gmail.com', '1234567890', '2025-10-16', 'male', 'STR C9 HOUSING BOARD COLONY', 'BHILAI KUMHARI', 'Chhattisgarh', '490042', 'alpha ', '1234567890', 'active', '2025-10-22 00:38:13', '2025-10-22 00:38:47', 2, '2025-10-22', 'batch-2025', NULL, NULL, '2025-10-22 00:38:47'),
(41, 'STU000041', 5, 'Satyam Kumar', 'apha@gmail.com', '1234567890', '2024-01-30', 'male', 'STR C9 HOUSING BOARD COLONY,MAHAMAYA ROAD,H.P. GAS AGENCY, WARD NO 14,KUMHARI', 'DURG-BHILAI', 'Chhattisgarh', '490042', 'alpha 123', '1234567890', 'graduated', '2025-10-22 00:41:21', '2025-10-22 00:42:30', 2, '2025-10-22', 'batch-2025', NULL, NULL, NULL),
(42, 'STU000042', 5, 'Anjali prajapati', 'anjali@gmail.com', '1234567890', '2001-12-23', 'female', 'street 123 kumharu', 'bhilai', 'Chhattisgarh', '490042', 'neel kamal', '4567991230', 'active', '2025-10-22 23:14:14', '2025-10-22 23:14:14', 4, '2025-10-23', '2026 batch', NULL, NULL, NULL),
(43, 'STU3COQHUJI', 4, 'anuj prajapati 12', 'anuj123@gmail.com', '1234567890', '2025-10-22', NULL, 'kumhari bazzar 12', 'bhilai', 'CG', '1234', NULL, NULL, 'active', '2025-10-26 00:19:01', '2025-10-26 00:34:22', 2, '2025-10-26', NULL, NULL, NULL, NULL),
(44, 'STUMKVNURIH', 4, 'anshu kumar', 'anshu@gmail.com', '1234567890', '2025-10-21', NULL, 'durg', 'durg', 'chhattisgarh', '490042', NULL, NULL, 'active', '2025-10-27 03:07:44', '2025-10-29 06:13:16', 2, '2025-10-27', NULL, NULL, NULL, NULL),
(45, 'STUBAKPM5SQ', 4, 'tester12', 'tester@gmail.com', '1526348970', '2000-02-14', NULL, 'kumhari', 'bhilai', 'Chhattisgarh', '490042', NULL, NULL, 'active', '2025-10-29 03:20:56', '2025-10-29 03:20:56', 3, '2025-10-29', NULL, NULL, NULL, NULL),
(46, 'STUSK 1E4397', 4, 'Aakash singh', 'suraj@gmail.com', '1526348790', '2001-02-14', NULL, 'adarsh nagar new rajendra park', 'Bhilai', 'Chhattisgarh', '490042', NULL, NULL, 'active', '2025-11-01 06:19:22', '2025-11-01 06:19:22', 6, '2025-11-01', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `franchise_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `role`, `franchise_id`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', NULL, 'super_admin', NULL, NULL, '$2y$10$tE6SjIv9/ozLWI1LiqkAvu6mNrvjm7y.l4Psf3xbwurh6kLJgv6py', NULL, '2025-10-16 05:00:20', '2025-10-17 05:42:13'),
(2, 'tester123', 'tester@gmail.com', NULL, 'franchise', 4, NULL, '$2y$10$iWakpRC.IyGzkC4TDbvUyOY3vJsJTdXW9fFOwZJJw50Bg7bTmIxwq', NULL, '2025-10-17 05:28:48', '2025-10-17 05:28:48'),
(3, 'sk prajapati', 'sk@gmail.com', NULL, 'franchise', 4, NULL, '$2y$10$DT3IlFU4XwMlQx0kHovR3uEbcXdpE833Dx1FRiRRvYsXiwe3Lqowe', NULL, '2025-10-17 05:29:31', '2025-10-17 05:42:13'),
(4, 'mr ajit ', 'satyamkprajapati2001@gmail.com', NULL, 'franchise', 5, NULL, '$2y$10$WOVIW/P8NL5f2sbG5aRbXubyKkt1ByWH/1Q.fakcMkbhN2vlyY/s6', NULL, '2025-10-19 11:40:23', '2025-10-19 11:40:23'),
(5, 'Momentum stocks', 'momemtum@gmail.com', NULL, 'franchise', 5, NULL, '$2y$10$b40smgK/bVksoAsKb2LY4.YqNeQfMjj6D4lgu5b.naR0q50TUrvI6', NULL, '2025-10-19 11:44:04', '2025-10-19 11:44:04'),
(10, 'Aditya', 'aditya@gmail.com', '1234567890', 'student', NULL, NULL, '$2y$10$H.Sv6gcjmHoErlJOXg5U4uwYjhBkmUijolOaqPAj8librjgZRtZz6', NULL, '2025-10-30 02:45:00', '2025-10-30 02:45:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificates_number_unique` (`number`),
  ADD KEY `certificates_student_id_status_index` (`student_id`,`status`),
  ADD KEY `certificates_course_id_status_index` (`course_id`,`status`),
  ADD KEY `certificates_certificate_request_id_index` (`certificate_request_id`),
  ADD KEY `certificates_franchise_id_foreign` (`franchise_id`),
  ADD KEY `certificates_issued_by_foreign` (`issued_by`);

--
-- Indexes for table `certificate_requests`
--
ALTER TABLE `certificate_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `certificate_requests_franchise_id_foreign` (`franchise_id`),
  ADD KEY `certificate_requests_student_id_foreign` (`student_id`),
  ADD KEY `certificate_requests_course_id_foreign` (`course_id`),
  ADD KEY `certificate_requests_payment_id_foreign` (`payment_id`),
  ADD KEY `certificate_requests_approved_by_foreign` (`approved_by`),
  ADD KEY `certificate_requests_rejected_by_foreign` (`rejected_by`),
  ADD KEY `certificate_requests_enrollment_id_foreign` (`enrollment_id`);

--
-- Indexes for table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `collections_franchise_id_period_unique` (`franchise_id`,`period`),
  ADD KEY `collections_franchise_id_status_index` (`franchise_id`,`status`),
  ADD KEY `collections_period_status_index` (`period`,`status`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_code_unique` (`code`),
  ADD KEY `courses_status_created_at_index` (`status`,`created_at`);

--
-- Indexes for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_enrollments_student_id_course_id_unique` (`student_id`,`course_id`),
  ADD KEY `course_enrollments_course_id_foreign` (`course_id`),
  ADD KEY `course_enrollments_franchise_id_foreign` (`franchise_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollments_course_id_foreign` (`course_id`),
  ADD KEY `enrollments_student_id_course_id_index` (`student_id`,`course_id`),
  ADD KEY `enrollments_franchise_id_status_index` (`franchise_id`,`status`),
  ADD KEY `enrollments_enrollment_date_index` (`enrollment_date`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exams_course_id_status_index` (`course_id`,`status`),
  ADD KEY `exams_exam_date_index` (`exam_date`);

--
-- Indexes for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_attempts_exam_id_student_id_unique` (`exam_id`,`student_id`),
  ADD KEY `exam_attempts_student_id_result_index` (`student_id`,`result`),
  ADD KEY `exam_attempts_exam_id_result_index` (`exam_id`,`result`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_results_exam_id_student_id_unique` (`exam_id`,`student_id`),
  ADD KEY `exam_results_student_id_foreign` (`student_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `franchises`
--
ALTER TABLE `franchises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `franchises_code_unique` (`code`),
  ADD UNIQUE KEY `franchises_email_unique` (`email`),
  ADD KEY `franchises_status_created_at_index` (`status`,`created_at`);

--
-- Indexes for table `franchise_wallets`
--
ALTER TABLE `franchise_wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `franchise_wallets_franchise_id_unique` (`franchise_id`);

--
-- Indexes for table `franchise_wallet_transactions`
--
ALTER TABLE `franchise_wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `franchise_wallet_transactions_franchise_id_foreign` (`franchise_id`);

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
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_order_id_unique` (`order_id`),
  ADD UNIQUE KEY `payments_payment_token_unique` (`payment_token`),
  ADD KEY `payments_course_id_foreign` (`course_id`),
  ADD KEY `payments_student_id_status_index` (`student_id`,`status`),
  ADD KEY `payments_status_created_at_index` (`status`,`created_at`);

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
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_student_id_unique` (`student_id`),
  ADD UNIQUE KEY `students_email_unique` (`email`),
  ADD KEY `students_franchise_id_status_index` (`franchise_id`,`status`),
  ADD KEY `students_created_at_index` (`created_at`),
  ADD KEY `students_course_id_foreign` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_franchise_id_foreign` (`franchise_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `certificate_requests`
--
ALTER TABLE `certificate_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `collections`
--
ALTER TABLE `collections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `franchises`
--
ALTER TABLE `franchises`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `franchise_wallets`
--
ALTER TABLE `franchise_wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `franchise_wallet_transactions`
--
ALTER TABLE `franchise_wallet_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_certificate_request_id_foreign` FOREIGN KEY (`certificate_request_id`) REFERENCES `certificate_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_issued_by_foreign` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `certificates_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificate_requests`
--
ALTER TABLE `certificate_requests`
  ADD CONSTRAINT `certificate_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `certificate_requests_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `certificate_requests_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificate_requests_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `certificate_requests_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`),
  ADD CONSTRAINT `certificate_requests_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `certificate_requests_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `collections`
--
ALTER TABLE `collections`
  ADD CONSTRAINT `collections_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD CONSTRAINT `course_enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_enrollments_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD CONSTRAINT `exam_attempts_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_attempts_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `franchise_wallets`
--
ALTER TABLE `franchise_wallets`
  ADD CONSTRAINT `franchise_wallets_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `franchise_wallet_transactions`
--
ALTER TABLE `franchise_wallet_transactions`
  ADD CONSTRAINT `franchise_wallet_transactions_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
