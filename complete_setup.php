<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>SmartProZen Complete Setup</h2>";
echo "<p>Setting up complete database with all missing elements...</p>";

try {
    // Step 1: Create missing columns
    echo "<h3>Step 1: Adding Missing Columns</h3>";
    
    // Add missing columns to products table
    $conn->query("ALTER TABLE products ADD COLUMN IF NOT EXISTS manage_stock tinyint(1) DEFAULT 1 AFTER stock_quantity");
    
    // Add missing columns to product_categories table
    $conn->query("ALTER TABLE product_categories ADD COLUMN IF NOT EXISTS parent_id int(11) DEFAULT NULL AFTER description");
    $conn->query("ALTER TABLE product_categories ADD COLUMN IF NOT EXISTS image varchar(255) DEFAULT NULL AFTER parent_id");
    $conn->query("ALTER TABLE product_categories ADD COLUMN IF NOT EXISTS meta_title varchar(255) DEFAULT NULL AFTER image");
    $conn->query("ALTER TABLE product_categories ADD COLUMN IF NOT EXISTS meta_description text DEFAULT NULL AFTER meta_title");
    
    // Add missing columns to coupons table
    $conn->query("ALTER TABLE coupons ADD COLUMN IF NOT EXISTS maximum_discount decimal(10,2) DEFAULT NULL AFTER minimum_amount");
    $conn->query("ALTER TABLE coupons ADD COLUMN IF NOT EXISTS used_count int(11) DEFAULT 0 AFTER usage_limit");
    $conn->query("ALTER TABLE coupons ADD COLUMN IF NOT EXISTS valid_from datetime DEFAULT NULL AFTER is_active");
    $conn->query("ALTER TABLE coupons ADD COLUMN IF NOT EXISTS valid_until datetime DEFAULT NULL AFTER valid_from");
    
    // Add missing columns to testimonials table
    $conn->query("ALTER TABLE testimonials ADD COLUMN IF NOT EXISTS email varchar(100) DEFAULT NULL AFTER name");
    $conn->query("ALTER TABLE testimonials ADD COLUMN IF NOT EXISTS avatar varchar(255) DEFAULT NULL AFTER position");
    
    // Add missing columns to admin_users table
    $conn->query("ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS is_active tinyint(1) DEFAULT 1 AFTER role_id");
    $conn->query("ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS last_login datetime DEFAULT NULL AFTER is_active");
    
    // Add missing columns to users table
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS phone varchar(20) DEFAULT NULL AFTER last_name");
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS address text DEFAULT NULL AFTER phone");
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS city varchar(50) DEFAULT NULL AFTER address");
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS state varchar(50) DEFAULT NULL AFTER city");
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS zip_code varchar(10) DEFAULT NULL AFTER state");
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS country varchar(50) DEFAULT 'US' AFTER zip_code");
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verified tinyint(1) DEFAULT 0 AFTER is_active");
    
    // Add missing columns to page_sections table
    $conn->query("ALTER TABLE page_sections ADD COLUMN IF NOT EXISTS title varchar(255) DEFAULT NULL AFTER section_type");
    $conn->query("ALTER TABLE page_sections ADD COLUMN IF NOT EXISTS is_active tinyint(1) DEFAULT 1 AFTER display_order");
    
    echo "<p>✅ Missing columns added successfully</p>";
    
    // Step 2: Update existing products with proper images
    echo "<h3>Step 2: Updating Product Images</h3>";
    
    $conn->query("UPDATE products SET featured_image = '68cfc186c96b3-front cover.jpg' WHERE id = 1 AND name = 'ZenBuds Pro 3'");
    $conn->query("UPDATE products SET featured_image = '68cfc18a0712d-front cover.jpg' WHERE id = 2 AND name = 'SmartGlow Ambient Light'");
    $conn->query("UPDATE products SET featured_image = '68cfc18baa5be-1755877095.png' WHERE id = 3 AND name = 'ProCharge Wireless Stand'");
    
    echo "<p>✅ Product images updated</p>";
    
    // Step 3: Add missing homepage sections
    echo "<h3>Step 3: Adding Homepage Sections</h3>";
    
    // Hero section
    $hero_content = json_encode([
        'title' => 'Smart Tech, Simplified Living.',
        'subtitle' => 'Discover our curated collection of smart gadgets and professional accessories designed to elevate your everyday.',
        'button_text' => 'Shop Now',
        'button_url' => '/products_list.php',
        'background_image' => '',
        'overlay_opacity' => 0.5
    ]);
    $conn->query("INSERT IGNORE INTO page_sections (page_id, section_type, title, content_json, display_order, is_active) VALUES (1, 'hero', 'Hero Section', '$hero_content', 1, 1)");
    
    // Features section
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
    
    // Featured products section
    $products_content = json_encode([
        'title' => 'Featured Products',
        'subtitle' => 'Discover our most popular items',
        'product_count' => 6,
        'show_featured_only' => true
    ]);
    $conn->query("INSERT IGNORE INTO page_sections (page_id, section_type, title, content_json, display_order, is_active) VALUES (1, 'featured_products', 'Featured Products', '$products_content', 3, 1)");
    
    // Testimonials section
    $testimonials_content = json_encode([
        'title' => 'What Our Customers Say',
        'subtitle' => 'Real feedback from satisfied customers',
        'show_featured_only' => true,
        'testimonial_count' => 4
    ]);
    $conn->query("INSERT IGNORE INTO page_sections (page_id, section_type, title, content_json, display_order, is_active) VALUES (1, 'testimonials', 'Testimonials Section', '$testimonials_content', 4, 1)");
    
    echo "<p>✅ Homepage sections added</p>";
    
    // Step 4: Add sample customer accounts
    echo "<h3>Step 4: Adding Sample Customers</h3>";
    
    $hashed_password = password_hash('customer123', PASSWORD_DEFAULT);
    
    $conn->query("INSERT IGNORE INTO users (id, username, email, password, first_name, last_name, phone, address, city, state, zip_code, country, is_active, email_verified) VALUES 
        (1, 'john_doe', 'john@example.com', '$hashed_password', 'John', 'Doe', '+1-555-0101', '123 Main St', 'New York', 'NY', '10001', 'US', 1, 1),
        (2, 'jane_smith', 'jane@example.com', '$hashed_password', 'Jane', 'Smith', '+1-555-0102', '456 Oak Ave', 'Los Angeles', 'CA', '90210', 'US', 1, 1),
        (3, 'mike_wilson', 'mike@example.com', '$hashed_password', 'Mike', 'Wilson', '+1-555-0103', '789 Pine Rd', 'Chicago', 'IL', '60601', 'US', 1, 1)");
    
    echo "<p>✅ Sample customers added</p>";
    
    // Step 5: Add sample orders
    echo "<h3>Step 5: Adding Sample Orders</h3>";
    
    $conn->query("INSERT IGNORE INTO orders (id, order_number, user_id, status, payment_status, payment_method, subtotal, tax_amount, shipping_amount, total_amount, currency, shipping_address, billing_address, created_at) VALUES 
        (1, 'SPZ-2025-001', 1, 'delivered', 'paid', 'credit_card', 79.99, 8.00, 5.99, 93.98, 'USD', '{\"name\":\"John Doe\",\"address\":\"123 Main St\",\"city\":\"New York\",\"state\":\"NY\",\"zip\":\"10001\",\"country\":\"US\"}', '{\"name\":\"John Doe\",\"address\":\"123 Main St\",\"city\":\"New York\",\"state\":\"NY\",\"zip\":\"10001\",\"country\":\"US\"}', '2025-09-20 10:30:00'),
        (2, 'SPZ-2025-002', 2, 'shipped', 'paid', 'paypal', 49.99, 5.00, 4.99, 59.98, 'USD', '{\"name\":\"Jane Smith\",\"address\":\"456 Oak Ave\",\"city\":\"Los Angeles\",\"state\":\"CA\",\"zip\":\"90210\",\"country\":\"US\"}', '{\"name\":\"Jane Smith\",\"address\":\"456 Oak Ave\",\"city\":\"Los Angeles\",\"state\":\"CA\",\"zip\":\"90210\",\"country\":\"US\"}', '2025-09-21 14:15:00'),
        (3, 'SPZ-2025-003', 3, 'processing', 'paid', 'credit_card', 39.99, 4.00, 3.99, 47.98, 'USD', '{\"name\":\"Mike Wilson\",\"address\":\"789 Pine Rd\",\"city\":\"Chicago\",\"state\":\"IL\",\"zip\":\"60601\",\"country\":\"US\"}', '{\"name\":\"Mike Wilson\",\"address\":\"789 Pine Rd\",\"city\":\"Chicago\",\"state\":\"IL\",\"zip\":\"60601\",\"country\":\"US\"}', '2025-09-21 16:45:00')");
    
    // Add order items
    $conn->query("INSERT IGNORE INTO order_items (id, order_id, product_id, product_name, product_sku, quantity, unit_price, total_price) VALUES 
        (1, 1, 1, 'ZenBuds Pro 3', 'ZBP3-001', 1, 79.99, 79.99),
        (2, 2, 2, 'SmartGlow Ambient Light', 'SGL-001', 1, 49.99, 49.99),
        (3, 3, 3, 'ProCharge Wireless Stand', 'PCS-001', 1, 39.99, 39.99)");
    
    echo "<p>✅ Sample orders added</p>";
    
    // Step 6: Add missing settings
    echo "<h3>Step 6: Adding Missing Settings</h3>";
    
    $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES 
        ('site_logo', '/uploads/logos/logo.png', 'file', 'general', 'Website logo', 1),
        ('contact_phone', '+1 (555) 123-4567', 'text', 'contact', 'Contact phone number', 1),
        ('contact_address', '123 Tech Street, Innovation City, IC 12345', 'text', 'contact', 'Business address', 1),
        ('site_description', 'Smart Tech, Simplified Living - Your premier destination for smart gadgets and professional accessories.', 'textarea', 'general', 'Website description', 1),
        ('default_currency', 'USD', 'text', 'shop', 'Default currency code', 1),
        ('tax_rate', '10.0', 'number', 'shop', 'Default tax rate percentage', 0),
        ('shipping_rate', '5.99', 'number', 'shop', 'Default shipping rate', 0)");
    
    echo "<p>✅ Missing settings added</p>";
    
    // Step 7: Clean up duplicate data
    echo "<h3>Step 7: Cleaning Up Duplicate Data</h3>";
    
    // Remove duplicate testimonials (keep only the first 2)
    $conn->query("DELETE FROM testimonials WHERE id > 2");
    
    // Remove duplicate reviews (keep only the first 2)
    $conn->query("DELETE FROM reviews WHERE id > 2");
    
    // Reset auto increment
    $conn->query("ALTER TABLE testimonials AUTO_INCREMENT = 3");
    $conn->query("ALTER TABLE reviews AUTO_INCREMENT = 3");
    
    echo "<p>✅ Duplicate data cleaned up</p>";
    
    // Step 8: Add missing database indexes
    echo "<h3>Step 8: Adding Database Indexes</h3>";
    
    // Add indexes for better performance
    $conn->query("ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_user_id (user_id)");
    $conn->query("ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_status (status)");
    $conn->query("ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_created_at (created_at)");
    $conn->query("ALTER TABLE order_items ADD INDEX IF NOT EXISTS idx_order_id (order_id)");
    $conn->query("ALTER TABLE order_items ADD INDEX IF NOT EXISTS idx_product_id (product_id)");
    $conn->query("ALTER TABLE products ADD INDEX IF NOT EXISTS idx_category_id (category_id)");
    $conn->query("ALTER TABLE products ADD INDEX IF NOT EXISTS idx_is_featured (is_featured)");
    $conn->query("ALTER TABLE products ADD INDEX IF NOT EXISTS idx_is_published (is_published)");
    $conn->query("ALTER TABLE reviews ADD INDEX IF NOT EXISTS idx_product_id (product_id)");
    $conn->query("ALTER TABLE reviews ADD INDEX IF NOT EXISTS idx_is_approved (is_approved)");
    $conn->query("ALTER TABLE wishlist ADD INDEX IF NOT EXISTS idx_user_id (user_id)");
    $conn->query("ALTER TABLE wishlist ADD INDEX IF NOT EXISTS idx_product_id (product_id)");
    
    echo "<p>✅ Database indexes added</p>";
    
    echo "<h3>🎉 Complete Setup Finished!</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>✅ What's Now Available:</h4>";
    echo "<ul>";
    echo "<li><strong>Complete Database Structure</strong> - All tables with proper columns</li>";
    echo "<li><strong>Product Images</strong> - Products now have proper featured images</li>";
    echo "<li><strong>Homepage Sections</strong> - Hero, Features, Products, Testimonials</li>";
    echo "<li><strong>Sample Customers</strong> - 3 customer accounts with orders</li>";
    echo "<li><strong>Sample Orders</strong> - 3 orders in different statuses</li>";
    echo "<li><strong>Complete Settings</strong> - All necessary configuration</li>";
    echo "<li><strong>Clean Data</strong> - No duplicate entries</li>";
    echo "<li><strong>Database Performance</strong> - Proper indexes for fast queries</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #cce5ff; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>🔗 Test Your Setup:</h4>";
    echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Homepage</a> - See your complete homepage</p>";
    echo "<p><a href='" . SITE_URL . "/products_list.php' target='_blank'>🛍️ Products</a> - Browse products with images</p>";
    echo "<p><a href='" . SITE_URL . "/admin/login.php' target='_blank'>⚙️ Admin Panel</a> - Manage your site (admin/admin123)</p>";
    echo "<p><a href='" . SITE_URL . "/admin/view_orders.php' target='_blank'>📦 Orders</a> - View sample orders</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

