<?php
/**
 * Quick Setup Script for SmartProZen CMS
 * This will create all tables and insert sample data
 */

require_once 'config.php';

echo "<h1>SmartProZen CMS - Quick Setup</h1>";
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
    
    echo "<p>✓ All tables created successfully</p>";
    
    // Insert basic settings
    echo "<p>Inserting basic settings...</p>";
    
    $basic_settings = [
        ['site_name', 'SmartProZen', 'text', 'general', 'Website name', 1],
        ['site_tagline', 'Smart Tech, Simplified Living', 'text', 'general', 'Website tagline', 1],
        ['contact_email', 'info@smartprozen.com', 'text', 'contact', 'Contact email', 1],
        ['currency', 'USD', 'text', 'shop', 'Default currency', 1],
        ['currency_symbol', '$', 'text', 'shop', 'Currency symbol', 1]
    ];
    
    foreach ($basic_settings as $setting) {
        $stmt = $conn->prepare("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $setting[0], $setting[1], $setting[2], $setting[3], $setting[4], $setting[5]);
        $stmt->execute();
        $stmt->close();
    }
    
    // Insert admin role
    $conn->query("INSERT IGNORE INTO roles (id, name, permissions) VALUES (1, 'Super Admin', '[\"all\"]')");
    
    // Insert admin user (password: admin123)
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT IGNORE INTO admin_users (username, email, password, full_name, role_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", 'admin', 'admin@smartprozen.com', $hashed_password, 'Administrator', 1);
    $stmt->execute();
    
    echo "<p>✓ Basic data inserted successfully</p>";
    
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
    echo "<li>✓ Database and all tables created</li>";
    echo "<li>✓ Admin user created</li>";
    echo "<li>✓ Basic settings configured</li>";
    echo "<li>✓ Upload directories created</li>";
    echo "<li>✓ CMS system ready for use</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<p><strong>Next:</strong> <a href='admin/login.php'>Login to admin panel</a> to start customizing your site!</p>";
    
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
