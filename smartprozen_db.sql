-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 21, 2025 at 09:36 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smartprozen_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_type` enum('admin','user','guest') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_type`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `timestamp`) VALUES
(1, 'admin', 1, 'admin_login', 'Admin admin logged in successfully.', '::1', NULL, '2025-09-21 18:56:16');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role_id` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `email`, `password`, `full_name`, `role_id`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@smartprozen.com', '$2y$12$9M6sWDjxMRxiWwBg7dR83e2FvhzgjIapJQTNS4v2CSL9gizQ7XLo2', 'Administrator', 1, 1, NULL, '2025-09-21 18:34:23', '2025-09-21 18:34:23');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `minimum_amount` decimal(10,2) DEFAULT NULL,
  `maximum_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `valid_from` datetime DEFAULT NULL,
  `valid_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `description`, `discount_type`, `discount_value`, `minimum_amount`, `maximum_discount`, `usage_limit`, `used_count`, `is_active`, `valid_from`, `valid_until`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME10', 'Welcome discount for new customers', 'percentage', 10.00, 50.00, NULL, 1000, 0, 1, NULL, NULL, '2025-09-21 18:43:16', '2025-09-21 18:43:16'),
(2, 'SAVE20', '20% off on orders over $100', 'percentage', 20.00, 100.00, NULL, 500, 0, 1, NULL, NULL, '2025-09-21 18:43:16', '2025-09-21 18:43:16');

-- --------------------------------------------------------

--
-- Table structure for table `media_library`
--

CREATE TABLE `media_library` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_type` enum('image','video','audio','document','other') NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(50) NOT NULL,
  `menu_items` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `location`, `menu_items`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Main Navigation', 'header', '[{\"title\":\"Home\",\"url\":\"/\",\"type\":\"page\"},{\"title\":\"Products\",\"url\":\"/products\",\"type\":\"page\"},{\"title\":\"About\",\"url\":\"/about\",\"type\":\"page\"},{\"title\":\"Contact\",\"url\":\"/contact\",\"type\":\"page\"}]', 1, '2025-09-21 18:34:25', '2025-09-21 18:34:25');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `version` varchar(20) DEFAULT '1.0.0',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`, `slug`, `description`, `is_active`, `version`, `created_at`, `updated_at`) VALUES
(1, 'E-commerce', 'ecommerce', 'Core e-commerce functionality including products, cart, and checkout', 1, '1.0.0', '2025-09-21 18:49:06', '2025-09-21 18:49:06'),
(2, 'Blog System', 'blog', 'Content management system for blog posts and articles', 1, '1.0.0', '2025-09-21 18:49:06', '2025-09-21 18:49:06'),
(3, 'User Reviews', 'reviews', 'Product review and rating system', 1, '1.0.0', '2025-09-21 18:49:06', '2025-09-21 18:49:06'),
(4, 'Wishlist', 'wishlist', 'Customer wishlist functionality', 1, '1.0.0', '2025-09-21 18:49:06', '2025-09-21 18:49:06'),
(5, 'Coupon System', 'coupons', 'Discount codes and promotional offers', 1, '1.0.0', '2025-09-21 18:49:06', '2025-09-21 18:49:06'),
(6, 'Testimonials', 'testimonials', 'Customer testimonials and feedback', 1, '1.0.0', '2025-09-21 18:49:06', '2025-09-21 18:49:06'),
(7, 'Contact Form', 'contact', 'Contact form and messaging system', 1, '1.0.0', '2025-09-21 18:49:06', '2025-09-21 18:49:06'),
(8, 'Newsletter', 'newsletter', 'Email subscription and newsletter management', 1, '1.0.0', '2025-09-21 18:49:06', '2025-09-21 18:49:06'),
(9, 'Analytics', 'analytics', 'Website analytics and reporting', 1, '1.0.0', '2025-09-21 18:49:06', '2025-09-21 18:49:06'),
(10, 'SEO Tools', 'seo', 'Search engine optimization tools and metadata management', 1, '1.0.0', '2025-09-21 18:49:06', '2025-09-21 18:49:06');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'USD',
  `shipping_address` text DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `template_slug` varchar(100) DEFAULT 'default_page',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `is_homepage` tinyint(1) DEFAULT 0,
  `featured_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `template_slug`, `meta_title`, `meta_description`, `meta_keywords`, `is_published`, `is_homepage`, `featured_image`, `created_at`, `updated_at`) VALUES
(1, 'Home', 'home', '{}', 'default_page', 'SmartProZen - Smart Tech, Simplified Living', 'Discover our curated collection of smart gadgets and professional accessories designed to elevate your everyday.', NULL, 1, 1, NULL, '2025-09-21 18:34:24', '2025-09-21 18:34:24');

-- --------------------------------------------------------

--
-- Table structure for table `page_sections`
--

CREATE TABLE `page_sections` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `section_type` varchar(50) NOT NULL,
  `content_json` longtext DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_gateways`
--

CREATE TABLE `payment_gateways` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `settings` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_gateways`
--

INSERT INTO `payment_gateways` (`id`, `name`, `slug`, `description`, `is_active`, `settings`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Credit Card', 'credit_card', 'Accept payments via credit and debit cards', 1, '{\"test_mode\": true}', 1, '2025-09-21 18:43:16', '2025-09-21 18:43:16'),
(2, 'PayPal', 'paypal', 'Accept payments via PayPal', 1, '{\"test_mode\": true}', 2, '2025-09-21 18:43:16', '2025-09-21 18:43:16');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `manage_stock` tinyint(1) DEFAULT 1,
  `stock_status` enum('instock','outofstock','onbackorder') DEFAULT 'instock',
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `product_type` enum('physical','digital','service') DEFAULT 'physical',
  `digital_file` varchar(255) DEFAULT NULL,
  `download_limit` int(11) DEFAULT NULL,
  `download_expiry` int(11) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `gallery_images` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `short_description`, `sku`, `price`, `sale_price`, `stock_quantity`, `manage_stock`, `stock_status`, `weight`, `dimensions`, `product_type`, `digital_file`, `download_limit`, `download_expiry`, `featured_image`, `gallery_images`, `category_id`, `meta_title`, `meta_description`, `meta_keywords`, `is_featured`, `is_published`, `created_at`, `updated_at`) VALUES
(1, 'ZenBuds Pro 3', 'zenbuds-pro-3', 'Premium wireless earbuds with noise cancellation', 'Premium wireless earbuds', 'ZBP3-001', 89.99, 79.99, 50, 1, 'instock', NULL, NULL, 'physical', NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, 1, 1, '2025-09-21 18:34:25', '2025-09-21 18:34:25'),
(2, 'SmartGlow Ambient Light', 'smartglow-ambient-light', 'Smart LED light with 16M colors', 'Smart LED light', 'SGL-001', 59.99, 49.99, 75, 1, 'instock', NULL, NULL, 'physical', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, 1, '2025-09-21 18:34:25', '2025-09-21 18:34:25'),
(3, 'ProCharge Wireless Stand', 'procharge-wireless-stand', 'Fast wireless charging stand', 'Fast wireless charging', 'PCS-001', 45.00, 39.99, 100, 1, 'instock', NULL, NULL, 'physical', NULL, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, 1, 1, '2025-09-21 18:34:25', '2025-09-21 18:34:25');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `slug`, `description`, `parent_id`, `image`, `meta_title`, `meta_description`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Smart Home Devices', 'smart-home', 'Transform your home with intelligent devices', NULL, NULL, NULL, NULL, 1, 1, '2025-09-21 18:34:25', '2025-09-21 18:34:25'),
(2, 'Professional Audio', 'audio', 'Premium audio equipment for professionals', NULL, NULL, NULL, NULL, 2, 1, '2025-09-21 18:34:25', '2025-09-21 18:34:25'),
(3, 'Mobile Accessories', 'mobile-accessories', 'Essential accessories for mobile devices', NULL, NULL, NULL, NULL, 3, 1, '2025-09-21 18:34:25', '2025-09-21 18:34:25');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `rating` int(1) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `guest_name`, `guest_email`, `rating`, `title`, `comment`, `is_approved`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, NULL, 5, 'Excellent sound quality!', 'These earbuds have amazing sound quality and the noise cancellation is incredible.', 1, '2025-09-21 18:43:16', '2025-09-21 18:43:16'),
(2, 2, NULL, NULL, NULL, 5, 'Perfect ambient lighting', 'Love the SmartGlow light! The colors are vibrant and the music sync feature is so cool.', 1, '2025-09-21 18:43:17', '2025-09-21 18:43:17'),
(3, 1, NULL, NULL, NULL, 5, 'Excellent sound quality!', 'These earbuds have amazing sound quality and the noise cancellation is incredible.', 1, '2025-09-21 18:45:16', '2025-09-21 18:45:16'),
(4, 2, NULL, NULL, NULL, 5, 'Perfect ambient lighting', 'Love the SmartGlow light! The colors are vibrant and the music sync feature is so cool.', 1, '2025-09-21 18:45:16', '2025-09-21 18:45:16'),
(5, 1, NULL, NULL, NULL, 5, 'Excellent sound quality!', 'These earbuds have amazing sound quality and the noise cancellation is incredible.', 1, '2025-09-21 18:45:17', '2025-09-21 18:45:17'),
(6, 2, NULL, NULL, NULL, 5, 'Perfect ambient lighting', 'Love the SmartGlow light! The colors are vibrant and the music sync feature is so cool.', 1, '2025-09-21 18:45:17', '2025-09-21 18:45:17'),
(7, 1, NULL, NULL, NULL, 5, 'Excellent sound quality!', 'These earbuds have amazing sound quality and the noise cancellation is incredible.', 1, '2025-09-21 18:49:07', '2025-09-21 18:49:07'),
(8, 2, NULL, NULL, NULL, 5, 'Perfect ambient lighting', 'Love the SmartGlow light! The colors are vibrant and the music sync feature is so cool.', 1, '2025-09-21 18:49:07', '2025-09-21 18:49:07'),
(9, 1, NULL, NULL, NULL, 5, 'Excellent sound quality!', 'These earbuds have amazing sound quality and the noise cancellation is incredible.', 1, '2025-09-21 18:56:02', '2025-09-21 18:56:02'),
(10, 2, NULL, NULL, NULL, 5, 'Perfect ambient lighting', 'Love the SmartGlow light! The colors are vibrant and the music sync feature is so cool.', 1, '2025-09-21 18:56:02', '2025-09-21 18:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `permissions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `permissions`, `created_at`) VALUES
(1, 'Super Admin', '[\"all\"]', '2025-09-21 18:29:58');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','textarea','number','boolean','json','file') DEFAULT 'text',
  `category` varchar(50) DEFAULT 'general',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `category`, `description`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'SmartProZen', 'text', 'general', 'Website name', 1, '2025-09-21 18:29:58', '2025-09-21 18:29:58'),
(2, 'site_tagline', 'Smart Tech, Simplified Living', 'text', 'general', 'Website tagline', 1, '2025-09-21 18:29:58', '2025-09-21 18:29:58'),
(3, 'contact_email', 'info@smartprozen.com', 'text', 'contact', 'Contact email', 1, '2025-09-21 18:29:58', '2025-09-21 18:29:58'),
(4, 'currency', 'USD', 'text', 'shop', 'Default currency', 1, '2025-09-21 18:29:58', '2025-09-21 18:29:58'),
(5, 'currency_symbol', '$', 'text', 'shop', 'Currency symbol', 1, '2025-09-21 18:29:58', '2025-09-21 18:29:58');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `rating` int(1) DEFAULT 5,
  `testimonial` text NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `email`, `company`, `position`, `rating`, `testimonial`, `avatar`, `is_featured`, `is_published`, `created_at`, `updated_at`) VALUES
(1, 'Mark Thompson', NULL, 'AudioEngine Studios', 'Senior Audio Engineer', 5, 'The ZenBuds Pro are the best wireless earbuds I have ever used. The sound quality is incredible.', NULL, 1, 1, '2025-09-21 18:34:25', '2025-09-21 18:34:25'),
(2, 'Sarah Kim', NULL, 'Design Co.', 'Creative Director', 5, 'My order from SmartProZen arrived in just two days! The quality exceeded my expectations.', NULL, 1, 1, '2025-09-21 18:34:25', '2025-09-21 18:34:25'),
(3, 'Mark Thompson', NULL, 'AudioEngine Studios', 'Senior Audio Engineer', 5, 'The ZenBuds Pro are the best wireless earbuds I have ever used. The sound quality is incredible.', NULL, 1, 1, '2025-09-21 18:39:02', '2025-09-21 18:39:02'),
(4, 'Sarah Kim', NULL, 'Design Co.', 'Creative Director', 5, 'My order from SmartProZen arrived in just two days! The quality exceeded my expectations.', NULL, 1, 1, '2025-09-21 18:39:02', '2025-09-21 18:39:02'),
(5, 'Mark Thompson', NULL, 'AudioEngine Studios', 'Senior Audio Engineer', 5, 'The ZenBuds Pro are the best wireless earbuds I have ever used. The sound quality is incredible.', NULL, 1, 1, '2025-09-21 18:39:57', '2025-09-21 18:39:57'),
(6, 'Sarah Kim', NULL, 'Design Co.', 'Creative Director', 5, 'My order from SmartProZen arrived in just two days! The quality exceeded my expectations.', NULL, 1, 1, '2025-09-21 18:39:57', '2025-09-21 18:39:57'),
(7, 'Mark Thompson', NULL, 'AudioEngine Studios', 'Senior Audio Engineer', 5, 'The ZenBuds Pro are the best wireless earbuds I have ever used. The sound quality is incredible.', NULL, 1, 1, '2025-09-21 18:43:16', '2025-09-21 18:43:16'),
(8, 'Sarah Kim', NULL, 'Design Co.', 'Creative Director', 5, 'My order from SmartProZen arrived in just two days! The quality exceeded my expectations.', NULL, 1, 1, '2025-09-21 18:43:16', '2025-09-21 18:43:16'),
(9, 'Mark Thompson', NULL, 'AudioEngine Studios', 'Senior Audio Engineer', 5, 'The ZenBuds Pro are the best wireless earbuds I have ever used. The sound quality is incredible.', NULL, 1, 1, '2025-09-21 18:45:15', '2025-09-21 18:45:15'),
(10, 'Sarah Kim', NULL, 'Design Co.', 'Creative Director', 5, 'My order from SmartProZen arrived in just two days! The quality exceeded my expectations.', NULL, 1, 1, '2025-09-21 18:45:15', '2025-09-21 18:45:15'),
(11, 'Mark Thompson', NULL, 'AudioEngine Studios', 'Senior Audio Engineer', 5, 'The ZenBuds Pro are the best wireless earbuds I have ever used. The sound quality is incredible.', NULL, 1, 1, '2025-09-21 18:45:17', '2025-09-21 18:45:17'),
(12, 'Sarah Kim', NULL, 'Design Co.', 'Creative Director', 5, 'My order from SmartProZen arrived in just two days! The quality exceeded my expectations.', NULL, 1, 1, '2025-09-21 18:45:17', '2025-09-21 18:45:17'),
(13, 'Mark Thompson', NULL, 'AudioEngine Studios', 'Senior Audio Engineer', 5, 'The ZenBuds Pro are the best wireless earbuds I have ever used. The sound quality is incredible.', NULL, 1, 1, '2025-09-21 18:49:06', '2025-09-21 18:49:06'),
(14, 'Sarah Kim', NULL, 'Design Co.', 'Creative Director', 5, 'My order from SmartProZen arrived in just two days! The quality exceeded my expectations.', NULL, 1, 1, '2025-09-21 18:49:06', '2025-09-21 18:49:06'),
(15, 'Mark Thompson', NULL, 'AudioEngine Studios', 'Senior Audio Engineer', 5, 'The ZenBuds Pro are the best wireless earbuds I have ever used. The sound quality is incredible.', NULL, 1, 1, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(16, 'Sarah Kim', NULL, 'Design Co.', 'Creative Director', 5, 'My order from SmartProZen arrived in just two days! The quality exceeded my expectations.', NULL, 1, 1, '2025-09-21 18:56:01', '2025-09-21 18:56:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `country` varchar(50) DEFAULT 'US',
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_type` (`user_type`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `action` (`action`),
  ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `media_library`
--
ALTER TABLE `media_library`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_type` (`file_type`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `location` (`location`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `page_sections`
--
ALTER TABLE `page_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`);

--
-- Indexes for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_approved` (`is_approved`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `media_library`
--
ALTER TABLE `media_library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `page_sections`
--
ALTER TABLE `page_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
