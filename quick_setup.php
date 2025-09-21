<?php
/**
 * Quick Setup - Simplified version for faster execution
 */

// Quick setup for development environment
// Note: This script is designed for local development use

// Increase execution time
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '256M');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Quick Setup</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<style>.progress{height:25px;}</style></head><body>";
echo "<div class='container mt-5'>";
echo "<h2>SmartProZen Quick Setup</h2>";
echo "<div class='progress mb-3'><div class='progress-bar' role='progressbar' style='width: 0%' id='progress'></div></div>";

try {
    // Step 1: Load configuration
    echo "<p>Step 1: Loading configuration...</p>";
    if (!file_exists('config.php')) {
        throw new Exception("config.php not found!");
    }
    require_once 'config.php';
    updateProgress(10);
    
    // Step 2: Database connection
    echo "<p>Step 2: Connecting to database...</p>";
    if (!file_exists('core/db.php')) {
        throw new Exception("core/db.php not found!");
    }
    require_once 'core/db.php';
    updateProgress(20);
    
    // Step 3: Create database
    echo "<p>Step 3: Creating database...</p>";
    $conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db(DB_NAME);
    updateProgress(30);
    
    // Step 4: Create essential tables only
    echo "<p>Step 4: Creating essential tables...</p>";
    $essential_tables = [
        "CREATE TABLE IF NOT EXISTS `settings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `setting_key` varchar(100) NOT NULL,
            `setting_value` text,
            `setting_type` varchar(20) DEFAULT 'text',
            `category` varchar(50) DEFAULT 'general',
            `description` text,
            `is_public` tinyint(1) DEFAULT 0,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `setting_key` (`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS `admin_users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(50) NOT NULL,
            `email` varchar(100) NOT NULL,
            `password` varchar(255) NOT NULL,
            `full_name` varchar(100) NOT NULL,
            `role_id` int(11) DEFAULT 1,
            `is_active` tinyint(1) DEFAULT 1,
            `last_login` timestamp NULL DEFAULT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `username` (`username`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS `pages` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `slug` varchar(255) NOT NULL,
            `content` longtext,
            `template_slug` varchar(100) DEFAULT 'default_page',
            `meta_title` varchar(255) DEFAULT NULL,
            `meta_description` text DEFAULT NULL,
            `is_published` tinyint(1) DEFAULT 1,
            `is_homepage` tinyint(1) DEFAULT 0,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `slug` (`slug`),
            KEY `is_homepage` (`is_homepage`),
            KEY `is_published` (`is_published`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS `page_sections` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `page_id` int(11) NOT NULL,
            `section_type` varchar(50) NOT NULL,
            `title` varchar(255) DEFAULT NULL,
            `content_json` longtext,
            `display_order` int(11) DEFAULT 0,
            `is_active` tinyint(1) DEFAULT 1,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `page_id` (`page_id`),
            KEY `section_type` (`section_type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    foreach ($essential_tables as $sql) {
        $conn->query($sql);
    }
    updateProgress(50);
    
    // Step 5: Load functions
    echo "<p>Step 5: Loading core functions...</p>";
    require_once 'core/functions.php';
    updateProgress(60);
    
    // Step 6: Insert basic data
    echo "<p>Step 6: Inserting basic data...</p>";
    
    // Insert settings
    $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES ('site_name', 'SmartProZen', 'text', 'general', 'Website name', 1)");
    $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES ('site_tagline', 'Smart Tech, Simplified Living', 'text', 'general', 'Website tagline', 1)");
    
    // Insert admin user
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT IGNORE INTO admin_users (username, email, password, full_name, role_id) VALUES ('admin', 'admin@smartprozen.com', '$hashed_password', 'Administrator', 1)");
    
    // Insert homepage
    $conn->query("INSERT IGNORE INTO pages (id, title, slug, content, template_slug, meta_title, meta_description, is_published, is_homepage) VALUES (1, 'Home', 'home', '{}', 'default_page', 'SmartProZen - Smart Tech, Simplified Living', 'Discover our curated collection of smart gadgets and professional accessories designed to elevate your everyday.', 1, 1)");
    
    // Insert homepage sections
    $hero_content = json_encode([
        'title' => 'Smart Tech, Simplified Living.',
        'subtitle' => 'Discover our curated collection of smart gadgets and professional accessories designed to elevate your everyday.',
        'button_text' => 'Shop Now',
        'button_url' => '/products_list.php'
    ]);
    $conn->query("INSERT IGNORE INTO page_sections (page_id, section_type, title, content_json, display_order, is_active) VALUES (1, 'hero', 'Hero Section', '$hero_content', 1, 1)");
    
    updateProgress(80);
    
    // Step 7: Create directories
    echo "<p>Step 7: Creating directories...</p>";
    $dirs = ['uploads', 'uploads/logos', 'uploads/media', 'uploads/products'];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    updateProgress(90);
    
    // Step 8: Final check
    echo "<p>Step 8: Final verification...</p>";
    $admin_check = $conn->query("SELECT COUNT(*) as count FROM admin_users WHERE username = 'admin'")->fetch_assoc();
    $homepage_check = $conn->query("SELECT COUNT(*) as count FROM pages WHERE slug = 'home'")->fetch_assoc();
    
    updateProgress(100);
    
    // Success
    echo "<div class='alert alert-success mt-4'>";
    echo "<h3>✅ Setup Complete!</h3>";
    echo "<p><strong>Admin Access:</strong> <a href='" . SITE_URL . "/admin/login.php'>" . SITE_URL . "/admin/login.php</a></p>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>Homepage:</strong> <a href='" . SITE_URL . "'>" . SITE_URL . "</a></p>";
    echo "</div>";
    
    echo "<div class='mt-3'>";
    echo "<a href='" . SITE_URL . "/admin/login.php' class='btn btn-primary'>Go to Admin Panel</a>";
    echo "<a href='" . SITE_URL . "' class='btn btn-success ms-2'>View Homepage</a>";
    echo "<a href='" . SITE_URL . "/master_setup.php' class='btn btn-info ms-2'>Full Setup</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger mt-4'>";
    echo "<h3>❌ Setup Failed!</h3>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</div></body></html>";

function updateProgress($percent) {
    echo "<script>document.getElementById('progress').style.width = '$percent%'; document.getElementById('progress').textContent = '$percent%';</script>";
    ob_flush();
    flush();
}
?>
