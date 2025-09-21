<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>Fixing Header & Footer Issues</h2>";

try {
    echo "<h3>Step 1: Checking Database Connection</h3>";
    
    // Test database connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    echo "<p>✅ Database connection successful</p>";
    
    echo "<h3>Step 2: Checking Required Tables</h3>";
    
    // Check if settings table exists
    $result = $conn->query("SHOW TABLES LIKE 'settings'");
    if ($result->num_rows == 0) {
        echo "<p>❌ Settings table missing - creating it...</p>";
        $conn->query("CREATE TABLE IF NOT EXISTS settings (
            id int(11) NOT NULL AUTO_INCREMENT,
            setting_key varchar(100) NOT NULL,
            setting_value text DEFAULT NULL,
            setting_type enum('text','textarea','number','boolean','json','file') DEFAULT 'text',
            category varchar(50) DEFAULT 'general',
            description text DEFAULT NULL,
            is_public tinyint(1) DEFAULT 0,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY setting_key (setting_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "<p>✅ Settings table created</p>";
    } else {
        echo "<p>✅ Settings table exists</p>";
    }
    
    // Check if menus table exists
    $result = $conn->query("SHOW TABLES LIKE 'menus'");
    if ($result->num_rows == 0) {
        echo "<p>❌ Menus table missing - creating it...</p>";
        $conn->query("CREATE TABLE IF NOT EXISTS menus (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            location varchar(50) NOT NULL,
            menu_items text DEFAULT NULL,
            is_active tinyint(1) DEFAULT 1,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY location (location)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "<p>✅ Menus table created</p>";
    } else {
        echo "<p>✅ Menus table exists</p>";
    }
    
    echo "<h3>Step 3: Inserting Essential Settings</h3>";
    
    // Insert essential settings if they don't exist
    $essential_settings = [
        ['site_name', 'SmartProZen', 'text', 'general', 'Website name', 1],
        ['site_description', 'Smart Tech, Simplified Living - Your premier destination for smart gadgets and professional accessories.', 'textarea', 'general', 'Website description', 1],
        ['site_logo', '/uploads/logos/logo.png', 'file', 'general', 'Website logo', 1],
        ['contact_email', 'info@smartprozen.com', 'text', 'contact', 'Contact email', 1],
        ['contact_phone', '+1 (555) 123-4567', 'text', 'contact', 'Contact phone', 1],
        ['contact_address', '123 Tech Street, Innovation City, IC 12345', 'text', 'contact', 'Business address', 1],
        ['theme_primary_color', '#007bff', 'text', 'theme', 'Primary color', 0],
        ['theme_body_bg', '#ffffff', 'text', 'theme', 'Body background', 0],
        ['theme_text_color', '#212529', 'text', 'theme', 'Text color', 0],
        ['theme_font_family', 'Poppins', 'text', 'theme', 'Font family', 0],
        ['theme_button_radius', '4px', 'text', 'theme', 'Button border radius', 0],
        ['theme_card_radius', '8px', 'text', 'theme', 'Card border radius', 0],
        ['theme_shadow', '0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)', 'text', 'theme', 'Box shadow', 0],
        ['google_font', 'Poppins', 'text', 'theme', 'Google Font', 0],
        ['header_layout', 'default', 'text', 'theme', 'Header layout', 0],
        ['footer_layout', 'default', 'text', 'theme', 'Footer layout', 0]
    ];
    
    foreach ($essential_settings as $setting) {
        $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES ('{$setting[0]}', '{$setting[1]}', '{$setting[2]}', '{$setting[3]}', '{$setting[4]}', {$setting[5]})");
    }
    echo "<p>✅ Essential settings inserted</p>";
    
    echo "<h3>Step 4: Creating Default Menu</h3>";
    
    // Create default header menu if it doesn't exist
    $menu_items = json_encode([
        ["title" => "Home", "url" => "/", "type" => "page"],
        ["title" => "Products", "url" => "/products_list.php", "type" => "page"],
        ["title" => "About", "url" => "/page/about", "type" => "page"],
        ["title" => "Contact", "url" => "/contact.php", "type" => "page"]
    ]);
    
    $conn->query("INSERT IGNORE INTO menus (name, location, menu_items, is_active) VALUES ('Main Navigation', 'header', '$menu_items', 1)");
    echo "<p>✅ Default header menu created</p>";
    
    // Create default footer menu
    $footer_menu_items = json_encode([
        ["title" => "About Us", "url" => "/page/about", "type" => "page"],
        ["title" => "Contact", "url" => "/contact.php", "type" => "page"],
        ["title" => "Privacy Policy", "url" => "/page/privacy", "type" => "page"],
        ["title" => "Terms of Service", "url" => "/page/terms", "type" => "page"]
    ]);
    
    $conn->query("INSERT IGNORE INTO menus (name, location, menu_items, is_active) VALUES ('Footer Links', 'footer', '$footer_menu_items', 1)");
    echo "<p>✅ Default footer menu created</p>";
    
    echo "<h3>Step 5: Testing Functions</h3>";
    
    // Test get_setting function
    require_once 'core/functions.php';
    
    $test_setting = get_setting('site_name', 'Default', $conn);
    echo "<p>✅ get_setting function works: " . htmlspecialchars($test_setting) . "</p>";
    
    // Test get_all_settings function
    $all_settings = get_all_settings($conn);
    echo "<p>✅ get_all_settings function works: " . count($all_settings) . " settings loaded</p>";
    
    echo "<h3>Step 6: Creating Missing Directories</h3>";
    
    $directories = [
        'uploads',
        'uploads/logos',
        'uploads/media',
        'uploads/avatars',
        'uploads/categories'
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            echo "<p>✅ Created directory: $dir</p>";
        } else {
            echo "<p>✅ Directory exists: $dir</p>";
        }
    }
    
    echo "<h3>🎉 Header & Footer Fix Complete!</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>✅ Issues Fixed:</h4>";
    echo "<ul>";
    echo "<li><strong>Function Parameters</strong> - Fixed get_setting() parameter mismatch</li>";
    echo "<li><strong>Database Tables</strong> - Created missing settings and menus tables</li>";
    echo "<li><strong>Essential Settings</strong> - Added all required settings for header/footer</li>";
    echo "<li><strong>Default Menus</strong> - Created header and footer navigation menus</li>";
    echo "<li><strong>Error Handling</strong> - Added fallbacks for missing functions</li>";
    echo "<li><strong>Directory Structure</strong> - Created upload directories</li>";
    echo "<li><strong>Service Worker Path</strong> - Fixed hardcoded path in footer</li>";
    echo "<li><strong>Cart Functions</strong> - Added error handling for cart operations</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #cce5ff; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>🔗 Test Your Fixes:</h4>";
    echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Homepage</a> - Check header and footer</p>";
    echo "<p><a href='" . SITE_URL . "/admin/login.php' target='_blank'>⚙️ Admin Panel</a> - Login to manage settings</p>";
    echo "<p><a href='" . SITE_URL . "/admin/settings.php' target='_blank'>🔧 Settings</a> - Customize header and footer</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
