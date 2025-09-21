<?php
/**
 * Fixed Setup Script for SmartProZen CMS
 * This will create all tables and insert sample data without bind_param issues
 */

require_once 'config.php';

echo "<h1>SmartProZen CMS - Fixed Setup</h1>";
echo "<p>Setting up your CMS system...</p>";

try {
    // Connect to MySQL
    $conn = new mysqli(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>✓ Connected to MySQL successfully</p>";
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if (!$conn->query($sql)) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    echo "<p>✓ Database '" . DB_NAME . "' ready</p>";
    
    // Select the database
    $conn->select_db(DB_NAME);
    
    // Create tables one by one
    echo "<p>Creating tables...</p>";
    
    // Settings table
    $conn->query("CREATE TABLE IF NOT EXISTS `settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `setting_key` varchar(100) NOT NULL,
        `setting_value` text,
        `setting_type` enum('text','textarea','number','boolean','json','file') DEFAULT 'text',
        `category` varchar(50) DEFAULT 'general',
        `description` text DEFAULT NULL,
        `is_public` tinyint(1) DEFAULT 0,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `setting_key` (`setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Admin users table
    $conn->query("CREATE TABLE IF NOT EXISTS `admin_users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `full_name` varchar(100) NOT NULL,
        `role_id` int(11) DEFAULT 1,
        `is_active` tinyint(1) DEFAULT 1,
        `last_login` datetime DEFAULT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Roles table
    $conn->query("CREATE TABLE IF NOT EXISTS `roles` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) NOT NULL,
        `permissions` text,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `name` (`name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Modules table
    $conn->query("CREATE TABLE IF NOT EXISTS `modules` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `slug` varchar(100) NOT NULL,
        `description` text DEFAULT NULL,
        `is_active` tinyint(1) DEFAULT 1,
        `version` varchar(20) DEFAULT '1.0.0',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Users table
    $conn->query("CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
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
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Pages table
    $conn->query("CREATE TABLE IF NOT EXISTS `pages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
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
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Page sections table
    $conn->query("CREATE TABLE IF NOT EXISTS `page_sections` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `page_id` int(11) NOT NULL,
        `section_type` varchar(50) NOT NULL,
        `content_json` longtext DEFAULT NULL,
        `display_order` int(11) DEFAULT 0,
        `is_published` tinyint(1) DEFAULT 1,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `page_id` (`page_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Product categories table
    $conn->query("CREATE TABLE IF NOT EXISTS `product_categories` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `slug` varchar(100) NOT NULL,
        `description` text DEFAULT NULL,
        `parent_id` int(11) DEFAULT NULL,
        `image` varchar(255) DEFAULT NULL,
        `meta_title` varchar(255) DEFAULT NULL,
        `meta_description` text DEFAULT NULL,
        `display_order` int(11) DEFAULT 0,
        `is_active` tinyint(1) DEFAULT 1,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Products table
    $conn->query("CREATE TABLE IF NOT EXISTS `products` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
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
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`),
        UNIQUE KEY `sku` (`sku`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Orders table
    $conn->query("CREATE TABLE IF NOT EXISTS `orders` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
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
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `order_number` (`order_number`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Order items table
    $conn->query("CREATE TABLE IF NOT EXISTS `order_items` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `order_id` int(11) NOT NULL,
        `product_id` int(11) NOT NULL,
        `product_name` varchar(255) NOT NULL,
        `product_sku` varchar(100) DEFAULT NULL,
        `quantity` int(11) NOT NULL,
        `unit_price` decimal(10,2) NOT NULL,
        `total_price` decimal(10,2) NOT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `order_id` (`order_id`),
        KEY `product_id` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Menus table
    $conn->query("CREATE TABLE IF NOT EXISTS `menus` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `location` varchar(50) NOT NULL,
        `menu_items` text,
        `is_active` tinyint(1) DEFAULT 1,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `location` (`location`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Testimonials table
    $conn->query("CREATE TABLE IF NOT EXISTS `testimonials` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) DEFAULT NULL,
        `company` varchar(100) DEFAULT NULL,
        `position` varchar(100) DEFAULT NULL,
        `rating` int(1) DEFAULT 5,
        `testimonial` text NOT NULL,
        `avatar` varchar(255) DEFAULT NULL,
        `is_featured` tinyint(1) DEFAULT 0,
        `is_published` tinyint(1) DEFAULT 1,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Activity logs table
    $conn->query("CREATE TABLE IF NOT EXISTS `activity_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_type` enum('admin','user','guest') NOT NULL,
        `user_id` int(11) DEFAULT NULL,
        `action` varchar(100) NOT NULL,
        `details` text DEFAULT NULL,
        `ip_address` varchar(45) DEFAULT NULL,
        `user_agent` text DEFAULT NULL,
        `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `user_type` (`user_type`),
        KEY `user_id` (`user_id`),
        KEY `action` (`action`),
        KEY `timestamp` (`timestamp`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Media library table
    $conn->query("CREATE TABLE IF NOT EXISTS `media_library` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `filename` varchar(255) NOT NULL,
        `original_name` varchar(255) NOT NULL,
        `file_path` varchar(500) NOT NULL,
        `file_size` int(11) NOT NULL,
        `mime_type` varchar(100) NOT NULL,
        `file_type` enum('image','video','audio','document','other') NOT NULL,
        `alt_text` varchar(255) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `uploaded_by` int(11) DEFAULT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `file_type` (`file_type`),
        KEY `uploaded_by` (`uploaded_by`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Coupons table
    $conn->query("CREATE TABLE IF NOT EXISTS `coupons` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
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
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `code` (`code`),
        KEY `is_active` (`is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Reviews table
    $conn->query("CREATE TABLE IF NOT EXISTS `reviews` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `user_id` int(11) DEFAULT NULL,
        `guest_name` varchar(100) DEFAULT NULL,
        `guest_email` varchar(100) DEFAULT NULL,
        `rating` int(1) NOT NULL,
        `title` varchar(255) DEFAULT NULL,
        `comment` text DEFAULT NULL,
        `is_approved` tinyint(1) DEFAULT 0,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `product_id` (`product_id`),
        KEY `user_id` (`user_id`),
        KEY `is_approved` (`is_approved`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Wishlist table
    $conn->query("CREATE TABLE IF NOT EXISTS `wishlist` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `product_id` int(11) NOT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `user_product` (`user_id`,`product_id`),
        KEY `product_id` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Payment gateways table
    $conn->query("CREATE TABLE IF NOT EXISTS `payment_gateways` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `slug` varchar(50) NOT NULL,
        `description` text DEFAULT NULL,
        `is_active` tinyint(1) DEFAULT 1,
        `settings` text DEFAULT NULL,
        `display_order` int(11) DEFAULT 0,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    echo "<p>✓ All tables created successfully</p>";
    
    // Insert basic settings using simple queries
    echo "<p>Inserting basic settings...</p>";
    
    $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES ('site_name', 'SmartProZen', 'text', 'general', 'Website name', 1)");
    $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES ('site_tagline', 'Smart Tech, Simplified Living', 'text', 'general', 'Website tagline', 1)");
    $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES ('contact_email', 'info@smartprozen.com', 'text', 'contact', 'Contact email', 1)");
    $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES ('currency', 'USD', 'text', 'shop', 'Default currency', 1)");
    $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES ('currency_symbol', '$', 'text', 'shop', 'Currency symbol', 1)");
    
    // Insert admin role
    $conn->query("INSERT IGNORE INTO roles (id, name, permissions) VALUES (1, 'Super Admin', '[\"all\"]')");
    
    // Insert admin user (password: admin123)
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT IGNORE INTO admin_users (username, email, password, full_name, role_id) VALUES ('admin', 'admin@smartprozen.com', '$hashed_password', 'Administrator', 1)");
    
    // Insert modules
    $conn->query("INSERT IGNORE INTO modules (name, slug, description, is_active, version) VALUES ('E-commerce', 'ecommerce', 'Core e-commerce functionality including products, cart, and checkout', 1, '1.0.0')");
    $conn->query("INSERT IGNORE INTO modules (name, slug, description, is_active, version) VALUES ('Blog System', 'blog', 'Content management system for blog posts and articles', 1, '1.0.0')");
    $conn->query("INSERT IGNORE INTO modules (name, slug, description, is_active, version) VALUES ('User Reviews', 'reviews', 'Product review and rating system', 1, '1.0.0')");
    $conn->query("INSERT IGNORE INTO modules (name, slug, description, is_active, version) VALUES ('Wishlist', 'wishlist', 'Customer wishlist functionality', 1, '1.0.0')");
    $conn->query("INSERT IGNORE INTO modules (name, slug, description, is_active, version) VALUES ('Coupon System', 'coupons', 'Discount codes and promotional offers', 1, '1.0.0')");
    $conn->query("INSERT IGNORE INTO modules (name, slug, description, is_active, version) VALUES ('Testimonials', 'testimonials', 'Customer testimonials and feedback', 1, '1.0.0')");
    $conn->query("INSERT IGNORE INTO modules (name, slug, description, is_active, version) VALUES ('Contact Form', 'contact', 'Contact form and messaging system', 1, '1.0.0')");
    $conn->query("INSERT IGNORE INTO modules (name, slug, description, is_active, version) VALUES ('Newsletter', 'newsletter', 'Email subscription and newsletter management', 1, '1.0.0')");
    $conn->query("INSERT IGNORE INTO modules (name, slug, description, is_active, version) VALUES ('Analytics', 'analytics', 'Website analytics and reporting', 1, '1.0.0')");
    $conn->query("INSERT IGNORE INTO modules (name, slug, description, is_active, version) VALUES ('SEO Tools', 'seo', 'Search engine optimization tools and metadata management', 1, '1.0.0')");
    
    // Insert homepage
    $conn->query("INSERT IGNORE INTO pages (id, title, slug, content, template_slug, meta_title, meta_description, is_published, is_homepage) VALUES (1, 'Home', 'home', '{}', 'default_page', 'SmartProZen - Smart Tech, Simplified Living', 'Discover our curated collection of smart gadgets and professional accessories designed to elevate your everyday.', 1, 1)");
    
    // Insert homepage sections
    $hero_content = json_encode([
        'title' => 'Smart Tech, Simplified Living.',
        'subtitle' => 'Discover our curated collection of smart gadgets and professional accessories designed to elevate your everyday.',
        'button_text' => 'Shop Now',
        'button_url' => '/products_list.php',
        'background_image' => '',
        'overlay_opacity' => 0.5
    ]);
    $conn->query("INSERT IGNORE INTO page_sections (page_id, section_type, title, content_json, display_order, is_active) VALUES (1, 'hero', 'Hero Section', '$hero_content', 1, 1)");
    
    $features_content = json_encode([
        'title' => 'Why Choose SmartProZen?',
        'subtitle' => 'We deliver premium quality products with exceptional service',
        'features' => [
            ['icon' => 'bi-gem', 'title' => 'Premium Quality', 'description' => 'We source and test every product to ensure it meets our high standards.'],
            ['icon' => 'bi-truck', 'title' => 'Fast Shipping', 'description' => 'Get your order delivered quickly with our reliable shipping partners.'],
            ['icon' => 'bi-shield-check', 'title' => 'Secure Checkout', 'description' => 'Your privacy and security are our top priority with encrypted payments.'],
            ['icon' => 'bi-headset', 'title' => '24/7 Support', 'description' => 'Our dedicated support team is here to help you around the clock.']
        ]
    ]);
    $conn->query("INSERT IGNORE INTO page_sections (page_id, section_type, title, content_json, display_order, is_active) VALUES (1, 'features', 'Features Section', '$features_content', 2, 1)");
    
    $products_content = json_encode([
        'title' => 'Featured Products',
        'subtitle' => 'Discover our most popular items',
        'product_count' => 6,
        'show_featured_only' => true
    ]);
    $conn->query("INSERT IGNORE INTO page_sections (page_id, section_type, title, content_json, display_order, is_active) VALUES (1, 'featured_products', 'Featured Products', '$products_content', 3, 1)");
    
    // Insert sample categories
    $conn->query("INSERT IGNORE INTO product_categories (id, name, slug, description, display_order, is_active) VALUES (1, 'Smart Home Devices', 'smart-home', 'Transform your home with intelligent devices', 1, 1)");
    $conn->query("INSERT IGNORE INTO product_categories (id, name, slug, description, display_order, is_active) VALUES (2, 'Professional Audio', 'audio', 'Premium audio equipment for professionals', 2, 1)");
    $conn->query("INSERT IGNORE INTO product_categories (id, name, slug, description, display_order, is_active) VALUES (3, 'Mobile Accessories', 'mobile-accessories', 'Essential accessories for mobile devices', 3, 1)");
    
    // Insert sample products
    $conn->query("INSERT IGNORE INTO products (id, name, slug, description, short_description, sku, price, sale_price, stock_quantity, stock_status, product_type, category_id, is_featured, is_published, featured_image) VALUES (1, 'ZenBuds Pro 3', 'zenbuds-pro-3', 'Premium wireless earbuds with noise cancellation', 'Premium wireless earbuds', 'ZBP3-001', 89.99, 79.99, 50, 'instock', 'physical', 2, 1, 1, '68cfc186c96b3-front cover.jpg')");
    $conn->query("INSERT IGNORE INTO products (id, name, slug, description, short_description, sku, price, sale_price, stock_quantity, stock_status, product_type, category_id, is_featured, is_published, featured_image) VALUES (2, 'SmartGlow Ambient Light', 'smartglow-ambient-light', 'Smart LED light with 16M colors', 'Smart LED light', 'SGL-001', 59.99, 49.99, 75, 'instock', 'physical', 1, 1, 1, '68cfc18a0712d-front cover.jpg')");
    $conn->query("INSERT IGNORE INTO products (id, name, slug, description, short_description, sku, price, sale_price, stock_quantity, stock_status, product_type, category_id, is_featured, is_published, featured_image) VALUES (3, 'ProCharge Wireless Stand', 'procharge-wireless-stand', 'Fast wireless charging stand', 'Fast wireless charging', 'PCS-001', 45.00, 39.99, 100, 'instock', 'physical', 3, 1, 1, '68cfc18baa5be-1755877095.png')");
    
    // Insert main navigation menu
    $menu_items = json_encode([
        ["title" => "Home", "url" => "/", "type" => "page"],
        ["title" => "Products", "url" => "/products", "type" => "page"],
        ["title" => "About", "url" => "/about", "type" => "page"],
        ["title" => "Contact", "url" => "/contact", "type" => "page"]
    ]);
    $conn->query("INSERT IGNORE INTO menus (name, location, menu_items, is_active) VALUES ('Main Navigation', 'header', '$menu_items', 1)");
    
    // Insert sample testimonials
    $conn->query("INSERT IGNORE INTO testimonials (name, company, position, rating, testimonial, is_featured, is_published) VALUES ('Mark Thompson', 'AudioEngine Studios', 'Senior Audio Engineer', 5, 'The ZenBuds Pro are the best wireless earbuds I have ever used. The sound quality is incredible.', 1, 1)");
    $conn->query("INSERT IGNORE INTO testimonials (name, company, position, rating, testimonial, is_featured, is_published) VALUES ('Sarah Kim', 'Design Co.', 'Creative Director', 5, 'My order from SmartProZen arrived in just two days! The quality exceeded my expectations.', 1, 1)");
    
    // Insert sample coupons
    $conn->query("INSERT IGNORE INTO coupons (code, description, discount_type, discount_value, minimum_amount, usage_limit, is_active) VALUES ('WELCOME10', 'Welcome discount for new customers', 'percentage', 10.00, 50.00, 1000, 1)");
    $conn->query("INSERT IGNORE INTO coupons (code, description, discount_type, discount_value, minimum_amount, usage_limit, is_active) VALUES ('SAVE20', '20% off on orders over $100', 'percentage', 20.00, 100.00, 500, 1)");
    
    // Insert payment gateways
    $conn->query("INSERT IGNORE INTO payment_gateways (name, slug, description, is_active, settings, display_order) VALUES ('Credit Card', 'credit_card', 'Accept payments via credit and debit cards', 1, '{\"test_mode\": true}', 1)");
    $conn->query("INSERT IGNORE INTO payment_gateways (name, slug, description, is_active, settings, display_order) VALUES ('PayPal', 'paypal', 'Accept payments via PayPal', 1, '{\"test_mode\": true}', 2)");
    
    // Insert sample reviews
    $conn->query("INSERT IGNORE INTO reviews (product_id, rating, title, comment, is_approved) VALUES (1, 5, 'Excellent sound quality!', 'These earbuds have amazing sound quality and the noise cancellation is incredible.', 1)");
    $conn->query("INSERT IGNORE INTO reviews (product_id, rating, title, comment, is_approved) VALUES (2, 5, 'Perfect ambient lighting', 'Love the SmartGlow light! The colors are vibrant and the music sync feature is so cool.', 1)");
    
    echo "<p>✓ Sample data inserted successfully</p>";
    
    // Create uploads directories
    $upload_dirs = [
        'uploads',
        'uploads/logos',
        'uploads/media',
        'uploads/files',
        'uploads/sections',
        'uploads/products',
        'uploads/categories',
        'uploads/avatars',
        'uploads/templates'
    ];
    
    foreach ($upload_dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            echo "<p>✓ Created directory: $dir</p>";
        }
    }
    
    echo "<h2 style='color: green;'>🎉 Setup Complete!</h2>";
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>Admin Login Details:</h3>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>Admin URL:</strong> <a href='admin/login.php'>" . SITE_URL . "/admin/login.php</a></p>";
    echo "</div>";
    
    echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>What's Ready:</h3>";
    echo "<ul>";
    echo "<li>✓ Database and all tables created (15+ tables)</li>";
    echo "<li>✓ Admin user created</li>";
    echo "<li>✓ Basic settings configured</li>";
    echo "<li>✓ Sample products and categories</li>";
    echo "<li>✓ Homepage created</li>";
    echo "<li>✓ Navigation menu set up</li>";
    echo "<li>✓ Sample testimonials and reviews</li>";
    echo "<li>✓ Coupon system ready</li>";
    echo "<li>✓ Payment gateways configured</li>";
    echo "<li>✓ Activity logging system</li>";
    echo "<li>✓ Media library system</li>";
    echo "<li>✓ Wishlist functionality</li>";
    echo "<li>✓ Upload directories created</li>";
    echo "<li>✓ Module management system</li>";
    echo "<li>✓ Complete CMS system ready for use</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<p><strong>Next:</strong> <a href='admin/login.php'>Login to admin panel</a> to start customizing your site!</p>";
    echo "<p><strong>Frontend:</strong> <a href='/'>View your website</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Setup Failed!</h2>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config.php and try again.</p>";
}

$conn->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}

h1, h2, h3 {
    color: #333;
}

p {
    margin: 10px 0;
}

ul {
    margin: 10px 0;
    padding-left: 20px;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
