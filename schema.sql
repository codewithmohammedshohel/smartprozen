-- SmartProzen Apex CMS - Database Schema
--
-- This schema is generated based on the analysis of the project files.
-- It includes tables for admin, users, products (including digital), orders,
-- content management (pages, posts), media, SEO, and other core CMS features.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Use the correct database
USE `smartprozen_db`;

--
-- Table structure for table `roles`
--
CREATE TABLE `roles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `permissions` JSON DEFAULT NULL, -- Store permissions as JSON (e.g., {"manage_products": true, "manage_users": false})
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Default data for table `roles`
--
INSERT INTO `roles` (`id`, `name`, `permissions`) VALUES
(1, 'Super Admin', '{"manage_admins": true, "manage_roles": true, "manage_products": true, "manage_pages": true, "manage_posts": true, "manage_orders": true, "manage_users": true, "manage_coupons": true, "manage_gateways": true, "manage_settings": true, "media_library": true, "generate_reports": true}'),
(2, 'Editor', '{"manage_products": true, "manage_pages": true, "manage_posts": true, "media_library": true}'),
(3, 'Viewer', '{"view_reports": true}');

--
-- Table structure for table `admin_users`
--
CREATE TABLE `admin_users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role_id` INT(11) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `users`
--
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `product_categories`
--
CREATE TABLE `product_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` JSON NOT NULL, -- Bilingual category name
  `description` JSON DEFAULT NULL, -- Bilingual category description
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `parent_id` INT(11) DEFAULT NULL, -- For hierarchical categories
  `image_filename` VARCHAR(255) DEFAULT NULL, -- Category image
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT(11) DEFAULT 0, -- For custom ordering
  `meta_title` JSON DEFAULT NULL, -- SEO meta title
  `meta_description` JSON DEFAULT NULL, -- SEO meta description
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_sort` (`sort_order`),
  FOREIGN KEY (`parent_id`) REFERENCES `product_categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `products`
--
CREATE TABLE `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` JSON NOT NULL, -- Bilingual product name (e.g., {"en": "Product Name", "bn": "পণ্যের নাম"})
  `description` JSON DEFAULT NULL, -- Bilingual product description
  `short_description` JSON DEFAULT NULL, -- Bilingual short description
  `price` DECIMAL(10,2) NOT NULL,
  `sale_price` DECIMAL(10,2) DEFAULT NULL, -- Sale price for discounts
  `sku` VARCHAR(100) DEFAULT NULL, -- Stock Keeping Unit
  `stock_quantity` INT(11) DEFAULT 0, -- Stock quantity (0 for digital products)
  `is_digital` TINYINT(1) NOT NULL DEFAULT 1, -- 1 for digital products, 0 for physical
  `is_active` TINYINT(1) NOT NULL DEFAULT 1, -- Product visibility
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0, -- Featured product flag
  `image_filename` VARCHAR(255) DEFAULT NULL, -- Main product image
  `gallery_images` JSON DEFAULT NULL, -- Additional product images
  `digital_file_path` VARCHAR(255) DEFAULT NULL, -- Path to the digital file for download
  `file_size` VARCHAR(50) DEFAULT NULL, -- File size for digital products
  `download_limit` INT(11) DEFAULT NULL, -- Download limit per user
  `download_expiry_days` INT(11) DEFAULT NULL, -- Download expiry in days
  `category_id` INT(11) DEFAULT NULL, -- Product category
  `tags` JSON DEFAULT NULL, -- Product tags
  `meta_title` JSON DEFAULT NULL, -- SEO meta title
  `meta_description` JSON DEFAULT NULL, -- SEO meta description
  `weight` DECIMAL(8,2) DEFAULT NULL, -- Weight for shipping (physical products)
  `dimensions` VARCHAR(100) DEFAULT NULL, -- Dimensions (physical products)
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `idx_category` (`category_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_digital` (`is_digital`),
  FOREIGN KEY (`category_id`) REFERENCES `product_categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `orders`
--
CREATE TABLE `orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_number` VARCHAR(50) NOT NULL, -- Human-readable order number
  `user_id` INT(11) NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  `discount_amount` DECIMAL(10,2) DEFAULT 0,
  `tax_amount` DECIMAL(10,2) DEFAULT 0,
  `shipping_amount` DECIMAL(10,2) DEFAULT 0,
  `status` ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Completed', 'Cancelled', 'Refunded') NOT NULL DEFAULT 'Pending',
  `payment_status` ENUM('Pending', 'Paid', 'Failed', 'Refunded') NOT NULL DEFAULT 'Pending',
  `payment_method` VARCHAR(50) NOT NULL,
  `payment_transaction_id` VARCHAR(255) DEFAULT NULL,
  `shipping_name` VARCHAR(255) NOT NULL,
  `shipping_email` VARCHAR(255) NOT NULL,
  `shipping_phone` VARCHAR(20) DEFAULT NULL,
  `shipping_address` TEXT NOT NULL,
  `shipping_city` VARCHAR(255) NOT NULL,
  `shipping_state` VARCHAR(255) DEFAULT NULL,
  `shipping_zip` VARCHAR(20) NOT NULL,
  `shipping_country` VARCHAR(100) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL, -- Order notes
  `tracking_number` VARCHAR(100) DEFAULT NULL,
  `shipped_at` TIMESTAMP NULL DEFAULT NULL,
  `delivered_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_status` (`payment_status`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `order_items`
--
CREATE TABLE `order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `product_name` VARCHAR(255) NOT NULL, -- Store product name at time of purchase
  `product_sku` VARCHAR(100) DEFAULT NULL, -- Store SKU at time of purchase
  `quantity` INT(11) NOT NULL,
  `price_at_purchase` DECIMAL(10,2) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL, -- quantity * price_at_purchase
  `is_digital` TINYINT(1) NOT NULL DEFAULT 1, -- Store if product was digital at purchase
  `digital_file_path` VARCHAR(255) DEFAULT NULL, -- Store file path at purchase
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_product` (`product_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `wishlist`
--
CREATE TABLE `wishlist` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product` (`user_id`, `product_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_product` (`product_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `downloads`
--
CREATE TABLE `downloads` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_size` VARCHAR(50) DEFAULT NULL,
  `download_count` INT(11) NOT NULL DEFAULT 0,
  `max_downloads` INT(11) DEFAULT NULL, -- NULL means unlimited
  `expires_at` TIMESTAMP NULL DEFAULT NULL, -- NULL means never expires
  `last_downloaded_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_product` (`product_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `seo_metadata`
--
CREATE TABLE `seo_metadata` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `entity_type` VARCHAR(50) NOT NULL, -- e.g., 'product', 'page', 'post'
  `entity_id` INT(11) NOT NULL,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_entity_type_id` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `reviews`
--
CREATE TABLE `reviews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `rating` TINYINT(1) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `comment` TEXT DEFAULT NULL,
  `is_approved` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `testimonials`
--
CREATE TABLE `testimonials` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `author_name` VARCHAR(255) NOT NULL,
  `author_title` VARCHAR(255) DEFAULT NULL,
  `testimonial_text` TEXT NOT NULL,
  `rating` TINYINT(1) DEFAULT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `is_approved` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `pages`
--
CREATE TABLE `pages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` JSON NOT NULL, -- Bilingual page title
  `content` JSON DEFAULT NULL, -- Bilingual page content
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `posts`
--
CREATE TABLE `posts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` JSON NOT NULL, -- Bilingual post title
  `content` JSON DEFAULT NULL, -- Bilingual post content
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `media_library`
--
CREATE TABLE `media_library` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `filename` VARCHAR(255) NOT NULL,
  `alt_text` VARCHAR(255) DEFAULT NULL,
  `uploaded_by` INT(11) DEFAULT NULL, -- admin_user_id
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`uploaded_by`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `coupons`
--
CREATE TABLE `coupons` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `discount_type` ENUM('percentage', 'fixed') NOT NULL,
  `discount_value` DECIMAL(10,2) NOT NULL,
  `expiry_date` DATE DEFAULT NULL,
  `usage_limit` INT(11) DEFAULT NULL, -- Total usage limit
  `used_count` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `payment_gateways`
--
CREATE TABLE `payment_gateways` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `is_active` TINYINT(1) NOT NULL DEFAULT 0,
  `settings` JSON DEFAULT NULL, -- Store gateway specific settings as JSON
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Default data for table `payment_gateways`
--
INSERT INTO `payment_gateways` (`id`, `name`, `slug`, `is_active`, `settings`) VALUES
(1, 'Stripe', 'stripe', 0, '{"publishable_key": "pk_test_...", "secret_key": "sk_test_..."}'),
(2, 'PayPal', 'paypal', 0, '{"client_id": "...", "client_secret": "..."}');

--
-- Table structure for table `activity_logs`
--
CREATE TABLE `activity_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_type` ENUM('admin', 'user', 'system') NOT NULL,
  `user_id` INT(11) DEFAULT NULL,
  `action` VARCHAR(255) NOT NULL,
  `details` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `email_templates`
--
CREATE TABLE `email_templates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `subject` VARCHAR(255) NOT NULL,
  `body` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Default data for table `email_templates`
--
INSERT INTO `email_templates` (`id`, `name`, `subject`, `body`) VALUES
(1, 'Order Confirmation', 'Your Order #{{order_id}} Confirmation', 'Dear {{user_name}},\n\nThank you for your order. Your order #{{order_id}} has been confirmed.\n\nDetails:\n{{order_details}}\n\nBest regards,\nSmartProzen Team'),
(2, 'Password Reset', 'Password Reset Request', 'Dear {{user_name}},\n\nYou have requested a password reset. Please click the following link to reset your password: {{reset_link}}\n\nIf you did not request this, please ignore this email.\n\nBest regards,\nSmartProzen Team');

--
-- Table structure for table `modules`
--
CREATE TABLE `modules` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `is_active` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Default data for table `modules`
--
INSERT INTO `modules` (`id`, `name`, `is_active`) VALUES
(1, 'Blog', 1),
(2, 'E-commerce', 1),
(3, 'SEO', 1),
(4, 'Analytics', 0);

--
-- Table structure for table `subscribers`
--
CREATE TABLE `subscribers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `subscribed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `contact_messages`
--
CREATE TABLE `contact_messages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `sent_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `settings`
--
CREATE TABLE `settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(255) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `menus`
--
CREATE TABLE `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `items`, `created_at`, `updated_at`) VALUES
(1, 'main-menu', '[{"label":"Home","url":"/"},{"label":"All Products","url":"products_list.php"},{"label":"Contact","url":"contact.php"},{"label":"About","url":"page.php?slug=about-us"}]', '2025-09-18 04:23:04', '2025-09-18 04:23:04');

--
-- Table structure for table `section_templates`
--
CREATE TABLE IF NOT EXISTS `section_templates` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `template_type` VARCHAR(50) NOT NULL,
    `default_content_json` JSON,
    `thumbnail_image` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `page_sections`
--
CREATE TABLE IF NOT EXISTS `page_sections` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `page_id` INT NOT NULL,
    `section_template_id` INT NULL,
    `section_type` VARCHAR(50) NOT NULL,
    `content_json` JSON,
    `display_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`section_template_id`) REFERENCES `section_templates`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Default data for table `product_categories`
--
INSERT INTO `product_categories` (`name`, `description`, `slug`, `is_active`, `sort_order`) VALUES
('{"en": "Digital Products", "bn": "ডিজিটাল পণ্য"}', '{"en": "Digital downloads and software", "bn": "ডিজিটাল ডাউনলোড এবং সফটওয়্যার"}', 'digital-products', 1, 1),
('{"en": "E-books", "bn": "ই-বুক"}', '{"en": "Electronic books and publications", "bn": "ইলেকট্রনিক বই এবং প্রকাশনা"}', 'e-books', 1, 2),
('{"en": "Software", "bn": "সফটওয়্যার"}', '{"en": "Software applications and tools", "bn": "সফটওয়্যার অ্যাপ্লিকেশন এবং সরঞ্জাম"}', 'software', 1, 3),
('{"en": "Templates", "bn": "টেমপ্লেট"}', '{"en": "Design templates and themes", "bn": "ডিজাইন টেমপ্লেট এবং থিম"}', 'templates', 1, 4);

--
-- Default data for table `section_templates`
--
INSERT INTO `section_templates` (`name`, `template_type`, `default_content_json`) VALUES
('Rich Text Block', 'rich_text', '{"text": {"en": "<p>This is a default rich text block. You can edit its content.</p>"}}'),
('Default Hero Section', 'hero', '{"title": {"en": "Welcome to Our Site!"}, "subtitle": {"en": "Discover amazing products and services."}, "image_id": "", "image_filename": "", "button_text": {"en": "Learn More"}, "button_link": "#"}'),
('Featured Products Grid', 'featured_products', '{"product_ids": []}'),
('Frequently Asked Questions', 'faq', '{"items": [{"question": {"en": "What is your return policy?"}, "answer": {"en": "Our return policy allows returns within 30 days of purchase."}}, {"question": {"en": "How can I contact support?"}, "answer": {"en": "You can contact our support team via email or phone."}}]}');

--
-- Default data for table `settings`
--
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', '{"en": "SmartProZen", "bn": "স্মার্টপ্রোজেন"}'),
('site_description', '{"en": "Your ultimate online shopping destination for digital products", "bn": "ডিজিটাল পণ্যের জন্য আপনার চূড়ান্ত অনলাইন শপিং গন্তব্য"}'),
('business_name', '{"en": "SmartProZen", "bn": "স্মার্টপ্রোজেন"}'),
('business_email', 'info@smartprozen.com'),
('business_phone', '+1-555-0123'),
('business_address', '{"en": "123 Business Street, City, State 12345", "bn": "১২৩ ব্যবসা স্ট্রিট, শহর, রাজ্য ১২৩৪৫"}'),
('currency', 'USD'),
('currency_symbol', '$'),
('theme_skin', 'default'),
('google_font', 'Poppins'),
('logo_filename', ''),
('favicon_filename', ''),
('social_facebook', ''),
('social_twitter', ''),
('social_instagram', ''),
('social_linkedin', ''),
('google_analytics', ''),
('meta_keywords', '{"en": "digital products, e-commerce, online shopping", "bn": "ডিজিটাল পণ্য, ই-কমার্স, অনলাইন শপিং"}'),
('maintenance_mode', '0'),
('registration_enabled', '1'),
('email_verification_required', '1'),
('max_file_upload_size', '10485760'), -- 10MB
('allowed_file_types', 'pdf,zip,rar,doc,docx,ppt,pptx,xls,xlsx,mp4,mp3,jpg,jpeg,png,gif'),
('default_language', 'en'),
('timezone', 'UTC'),
('date_format', 'Y-m-d'),
('time_format', 'H:i:s'),
('items_per_page', '12'),
('enable_reviews', '1'),
('enable_wishlist', '1'),
('enable_coupons', '1'),
('enable_guest_checkout', '1'),
('tax_rate', '0.00'),
('shipping_enabled', '0'),
('free_shipping_threshold', '0.00'),
('order_notification_email', 'orders@smartprozen.com'),
('support_email', 'support@smartprozen.com');

--
-- Default data for table `pages` (Home Page)
--
INSERT INTO `pages` (`title`, `slug`, `content`) VALUES
('{"en": "Home", "bn": "হোম"}', 'home', '{}');

--
-- Default data for table `page_sections` for Home Page
--
INSERT INTO `page_sections` (`page_id`, `section_template_id`, `section_type`, `content_json`, `display_order`) VALUES
((SELECT id FROM pages WHERE slug = 'home'), (SELECT id FROM section_templates WHERE name = 'Default Hero Section'), 'hero', '{"title": {"en": "Welcome to SmartProZen!"}, "subtitle": {"en": "Your ultimate online shopping destination for quality products."}, "image_id": "", "image_filename": "", "button_text": {"en": "Shop Now"}, "button_link": "/products"}', 0),
((SELECT id FROM pages WHERE slug = 'home'), (SELECT id FROM section_templates WHERE name = 'Rich Text Block'), 'rich_text', '{"text": {"en": "<h2>About Us</h2><p>SmartProZen is dedicated to providing you with the best online shopping experience. We offer a wide range of products to meet your needs.</p>"}}', 1),
((SELECT id FROM pages WHERE slug = 'home'), (SELECT id FROM section_templates WHERE name = 'Featured Products Grid'), 'featured_products', '{"product_ids": []}', 2);

COMMIT;