<?php
/**
 * Database Setup Component for Master Setup
 */

echo "<div class='progress mb-3'><div class='progress-bar' role='progressbar' style='width: 0%' id='setup-progress'></div></div>";

try {
    // Step 1: Load configuration
    echo "<div class='test-section test-info'>";
    echo "<h5><i class='bi bi-gear'></i> Step 1: Loading Configuration</h5>";
    
    if (!file_exists('config.php')) {
        throw new Exception("config.php not found! Please ensure the configuration file exists.");
    }
    
    require_once 'config.php';
    echo "<p class='text-success'>✅ Configuration loaded successfully</p>";
    echo "<p><strong>Environment:</strong> " . ENVIRONMENT . "</p>";
    echo "<p><strong>Site URL:</strong> " . SITE_URL . "</p>";
    echo "</div>";
    
    updateProgress(10);
    
    // Step 2: Test database connection
    echo "<div class='test-section test-info'>";
    echo "<h5><i class='bi bi-database'></i> Step 2: Testing Database Connection</h5>";
    
    if (!file_exists('core/db.php')) {
        throw new Exception("core/db.php not found! Please ensure the database connection file exists.");
    }
    
    require_once 'core/db.php';
    echo "<p class='text-success'>✅ Database connection successful</p>";
    echo "<p><strong>Host:</strong> " . DB_HOST . ":" . DB_PORT . "</p>";
    echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
    echo "</div>";
    
    updateProgress(20);
    
    // Step 3: Create database if not exists
    echo "<div class='test-section test-info'>";
    echo "<h5><i class='bi bi-database-add'></i> Step 3: Creating Database</h5>";
    
    $create_db_sql = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($create_db_sql)) {
        echo "<p class='text-success'>✅ Database '" . DB_NAME . "' created/verified successfully</p>";
    } else {
        throw new Exception("Failed to create database: " . $conn->error);
    }
    echo "</div>";
    
    updateProgress(30);
    
    // Step 4: Load and execute database schema
    echo "<div class='test-section test-info'>";
    echo "<h5><i class='bi bi-table'></i> Step 4: Creating Tables</h5>";
    
    if (!file_exists('database_schema.sql')) {
        throw new Exception("database_schema.sql not found! Please ensure the schema file exists.");
    }
    
    $schema = file_get_contents('database_schema.sql');
    $statements = explode(';', $schema);
    $tables_created = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^(--|\/\*)/', $statement)) {
            if ($conn->query($statement)) {
                $tables_created++;
            } else {
                // Log error but continue (table might already exist)
                if (strpos($conn->error, 'already exists') === false) {
                    echo "<p class='text-warning'>⚠️ Warning: " . $conn->error . "</p>";
                }
            }
        }
    }
    
    echo "<p class='text-success'>✅ Database tables created/verified successfully</p>";
    echo "<p><strong>Tables processed:</strong> " . $tables_created . "</p>";
    echo "</div>";
    
    updateProgress(50);
    
    // Step 5: Load core functions
    echo "<div class='test-section test-info'>";
    echo "<h5><i class='bi bi-code'></i> Step 5: Loading Core Functions</h5>";
    
    if (!file_exists('core/functions.php')) {
        throw new Exception("core/functions.php not found! Please ensure the functions file exists.");
    }
    
    require_once 'core/functions.php';
    echo "<p class='text-success'>✅ Core functions loaded successfully</p>";
    echo "</div>";
    
    updateProgress(60);
    
    // Step 6: Insert basic settings and data
    echo "<div class='test-section test-info'>";
    echo "<h5><i class='bi bi-data'></i> Step 6: Inserting Sample Data</h5>";
    
    // Insert basic settings
    $settings = [
        ['site_name', 'SmartProZen', 'text', 'general', 'Website name', 1],
        ['site_tagline', 'Smart Tech, Simplified Living', 'text', 'general', 'Website tagline', 1],
        ['contact_email', 'info@smartprozen.com', 'text', 'contact', 'Contact email', 1],
        ['currency', 'USD', 'text', 'shop', 'Default currency', 1],
        ['currency_symbol', '$', 'text', 'shop', 'Currency symbol', 1]
    ];
    
    foreach ($settings as $setting) {
        $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES ('{$setting[0]}', '{$setting[1]}', '{$setting[2]}', '{$setting[3]}', '{$setting[4]}', {$setting[5]})");
    }
    
    // Insert admin role
    $conn->query("INSERT IGNORE INTO roles (id, name, permissions) VALUES (1, 'Super Admin', '[\"all\"]')");
    
    // Insert admin user (password: admin123)
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT IGNORE INTO admin_users (username, email, password, full_name, role_id) VALUES ('admin', 'admin@smartprozen.com', '$hashed_password', 'Administrator', 1)");
    
    // Insert modules
    $modules = [
        ['E-commerce', 'ecommerce', 'Core e-commerce functionality including products, cart, and checkout'],
        ['Blog System', 'blog', 'Content management system for blog posts and articles'],
        ['User Reviews', 'reviews', 'Product review and rating system'],
        ['Wishlist', 'wishlist', 'Customer wishlist functionality'],
        ['Coupon System', 'coupons', 'Discount codes and promotional offers'],
        ['Testimonials', 'testimonials', 'Customer testimonials and feedback'],
        ['Contact Form', 'contact', 'Contact form and messaging system'],
        ['Newsletter', 'newsletter', 'Email subscription and newsletter management'],
        ['Analytics', 'analytics', 'Website analytics and reporting'],
        ['SEO Tools', 'seo', 'Search engine optimization tools and metadata management']
    ];
    
    foreach ($modules as $module) {
        $conn->query("INSERT IGNORE INTO modules (name, slug, description, is_active, version) VALUES ('{$module[0]}', '{$module[1]}', '{$module[2]}', 1, '1.0.0')");
    }
    
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
    $conn->query("INSERT IGNORE INTO products (id, name, slug, description, short_description, sku, price, sale_price, stock_quantity, stock_status, product_type, category_id, is_featured, is_published) VALUES (1, 'ZenBuds Pro 3', 'zenbuds-pro-3', 'Premium wireless earbuds with noise cancellation', 'Premium wireless earbuds', 'ZBP3-001', 89.99, 79.99, 50, 'instock', 'physical', 2, 1, 1)");
    $conn->query("INSERT IGNORE INTO products (id, name, slug, description, short_description, sku, price, sale_price, stock_quantity, stock_status, product_type, category_id, is_featured, is_published) VALUES (2, 'SmartGlow Ambient Light', 'smartglow-ambient-light', 'Smart LED light with 16M colors', 'Smart LED light', 'SGL-001', 59.99, 49.99, 75, 'instock', 'physical', 1, 1, 1)");
    $conn->query("INSERT IGNORE INTO products (id, name, slug, description, short_description, sku, price, sale_price, stock_quantity, stock_status, product_type, category_id, is_featured, is_published) VALUES (3, 'ProCharge Wireless Stand', 'procharge-wireless-stand', 'Fast wireless charging stand', 'Fast wireless charging', 'PCS-001', 45.00, 39.99, 100, 'instock', 'physical', 3, 1, 1)");
    
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
    $conn->query("INSERT IGNORE INTO coupons (code, description, discount_type, discount_value, minimum_amount, usage_limit, is_active) VALUES ('SAVE20', 'Save 20% on orders over $100', 'percentage', 20.00, 100.00, 500, 1)");
    
    // Insert payment gateways
    $conn->query("INSERT IGNORE INTO payment_gateways (name, slug, description, is_active, configuration) VALUES ('Credit Card', 'credit_card', 'Accept credit card payments', 1, '{}')");
    $conn->query("INSERT IGNORE INTO payment_gateways (name, slug, description, is_active, configuration) VALUES ('PayPal', 'paypal', 'Accept PayPal payments', 1, '{}')");
    
    echo "<p class='text-success'>✅ Sample data inserted successfully</p>";
    echo "</div>";
    
    updateProgress(80);
    
    // Step 7: Create upload directories
    echo "<div class='test-section test-info'>";
    echo "<h5><i class='bi bi-folder'></i> Step 7: Creating Upload Directories</h5>";
    
    $directories = [
        'uploads',
        'uploads/logos',
        'uploads/media',
        'uploads/products',
        'uploads/categories',
        'uploads/avatars',
        'uploads/files',
        'uploads/sections',
        'uploads/templates'
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                echo "<p class='text-success'>✅ Created directory: $dir</p>";
            } else {
                echo "<p class='text-warning'>⚠️ Failed to create directory: $dir</p>";
            }
        } else {
            echo "<p class='text-info'>ℹ️ Directory exists: $dir</p>";
        }
    }
    echo "</div>";
    
    updateProgress(90);
    
    // Step 8: Final verification
    echo "<div class='test-section test-info'>";
    echo "<h5><i class='bi bi-check-circle'></i> Step 8: Final Verification</h5>";
    
    // Check if admin user exists
    $admin_check = $conn->query("SELECT COUNT(*) as count FROM admin_users WHERE username = 'admin'")->fetch_assoc();
    if ($admin_check['count'] > 0) {
        echo "<p class='text-success'>✅ Admin user created successfully</p>";
    } else {
        echo "<p class='text-warning'>⚠️ Admin user not found</p>";
    }
    
    // Check if homepage exists
    $homepage_check = $conn->query("SELECT COUNT(*) as count FROM pages WHERE slug = 'home'")->fetch_assoc();
    if ($homepage_check['count'] > 0) {
        echo "<p class='text-success'>✅ Homepage created successfully</p>";
    } else {
        echo "<p class='text-warning'>⚠️ Homepage not found</p>";
    }
    
    // Check if products exist
    $products_check = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc();
    if ($products_check['count'] > 0) {
        echo "<p class='text-success'>✅ Sample products created successfully</p>";
    } else {
        echo "<p class='text-warning'>⚠️ No products found</p>";
    }
    
    echo "</div>";
    
    updateProgress(100);
    
    // Success message
    echo "<div class='test-section test-pass'>";
    echo "<h3><i class='bi bi-check-circle'></i> Setup Complete!</h3>";
    echo "<div class='row'>";
    echo "<div class='col-md-6'>";
    echo "<h5>Admin Access:</h5>";
    echo "<p><strong>URL:</strong> <a href='" . SITE_URL . "/admin/login.php'>" . SITE_URL . "/admin/login.php</a></p>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "</div>";
    echo "<div class='col-md-6'>";
    echo "<h5>Frontend Access:</h5>";
    echo "<p><strong>Homepage:</strong> <a href='" . SITE_URL . "'>" . SITE_URL . "</a></p>";
    echo "<p><strong>Products:</strong> <a href='" . SITE_URL . "/products_list.php'>" . SITE_URL . "/products_list.php</a></p>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='test-section test-fail'>";
    echo "<h3><i class='bi bi-x-circle'></i> Setup Failed!</h3>";
    echo "<p class='text-danger'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check the error message above and try again.</p>";
    echo "</div>";
}

function updateProgress($percent) {
    echo "<script>document.getElementById('setup-progress').style.width = '$percent%'; document.getElementById('setup-progress').textContent = '$percent%';</script>";
    ob_flush();
    flush();
}
?>
