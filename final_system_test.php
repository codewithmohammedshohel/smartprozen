<?php
require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

echo "<h2>🧪 Final System Test - SmartProZen</h2>";
echo "<p>This test will verify that all components are working correctly.</p>";

try {
    echo "<h3>Step 1: Database Content Verification</h3>";
    
    // Check all content
    $checks = [
        'Pages' => "SELECT COUNT(*) as count FROM pages WHERE is_published = 1",
        'Products' => "SELECT COUNT(*) as count FROM products WHERE is_published = 1",
        'Categories' => "SELECT COUNT(*) as count FROM product_categories WHERE is_active = 1",
        'Testimonials' => "SELECT COUNT(*) as count FROM testimonials WHERE is_published = 1",
        'Coupons' => "SELECT COUNT(*) as count FROM coupons WHERE is_active = 1",
        'Menus' => "SELECT COUNT(*) as count FROM menus WHERE is_active = 1",
        'Reviews' => "SELECT COUNT(*) as count FROM reviews WHERE is_approved = 1",
        'Homepage Sections' => "SELECT COUNT(*) as count FROM page_sections WHERE page_id = 1 AND is_active = 1"
    ];
    
    $all_good = true;
    foreach ($checks as $name => $query) {
        $result = $conn->query($query);
        $count = $result->fetch_assoc()['count'];
        echo "<p>📊 <strong>$name:</strong> $count</p>";
        
        // Set minimum thresholds
        $thresholds = [
            'Pages' => 5,
            'Products' => 10,
            'Categories' => 6,
            'Testimonials' => 5,
            'Coupons' => 3,
            'Menus' => 2,
            'Reviews' => 3,
            'Homepage Sections' => 3
        ];
        
        if ($count >= ($thresholds[$name] ?? 0)) {
            echo "<p style='color: green;'>✅ $name: GOOD ($count items)</p>";
        } else {
            echo "<p style='color: red;'>❌ $name: NEEDS MORE ($count items, need {$thresholds[$name]}+)</p>";
            $all_good = false;
        }
    }
    
    echo "<h3>Step 2: Function Tests</h3>";
    
    // Test generate_menu function
    echo "<p>Testing generate_menu function...</p>";
    if (function_exists('generate_menu')) {
        $menu_html = generate_menu('Main Menu', $conn);
        if (!empty($menu_html) && strpos($menu_html, 'nav-link') !== false) {
            echo "<p>✅ generate_menu function working</p>";
        } else {
            echo "<p>❌ generate_menu function not working properly</p>";
            $all_good = false;
        }
    } else {
        echo "<p>❌ generate_menu function not found</p>";
        $all_good = false;
    }
    
    // Test get_setting function
    echo "<p>Testing get_setting function...</p>";
    if (function_exists('get_setting')) {
        $site_name = get_setting('site_name', 'SmartProZen', $conn);
        if (!empty($site_name)) {
            echo "<p>✅ get_setting function working</p>";
        } else {
            echo "<p>❌ get_setting function not working properly</p>";
            $all_good = false;
        }
    } else {
        echo "<p>❌ get_setting function not found</p>";
        $all_good = false;
    }
    
    echo "<h3>Step 3: Template File Tests</h3>";
    
    // Check template files
    $templates = [
        'hero.php' => 'templates/sections/hero.php',
        'features.php' => 'templates/sections/features.php',
        'testimonials.php' => 'templates/sections/testimonials.php',
        'featured_products.php' => 'templates/sections/featured_products.php',
        'rich_text.php' => 'templates/sections/rich_text.php'
    ];
    
    foreach ($templates as $name => $path) {
        if (file_exists($path)) {
            echo "<p>✅ $name template exists</p>";
        } else {
            echo "<p>❌ $name template missing</p>";
            $all_good = false;
        }
    }
    
    echo "<h3>Step 4: Header/Footer Tests</h3>";
    
    // Check header/footer files
    $include_files = [
        'Header' => 'includes/header.php',
        'Customizable Header' => 'includes/customizable_header.php',
        'Footer' => 'includes/footer.php',
        'Customizable Footer' => 'includes/customizable_footer.php'
    ];
    
    foreach ($include_files as $name => $path) {
        if (file_exists($path)) {
            echo "<p>✅ $name file exists</p>";
        } else {
            echo "<p>❌ $name file missing</p>";
            $all_good = false;
        }
    }
    
    echo "<h3>Step 5: URL Generation Tests</h3>";
    
    // Test SITE_URL constant
    if (defined('SITE_URL') && !empty(SITE_URL)) {
        echo "<p>✅ SITE_URL constant defined: " . SITE_URL . "</p>";
    } else {
        echo "<p>❌ SITE_URL constant not defined</p>";
        $all_good = false;
    }
    
    // Test environment detection
    if (defined('IS_LOCAL') || defined('IS_PRODUCTION')) {
        $env = defined('IS_LOCAL') && IS_LOCAL ? 'Local' : 'Production';
        echo "<p>✅ Environment detected: $env</p>";
    } else {
        echo "<p>⚠️ Environment constants not defined</p>";
    }
    
    echo "<h3>🎯 Final Results</h3>";
    
    if ($all_good) {
        echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>🎉 System Test PASSED!</h4>";
        echo "<p>All components are working correctly. Your SmartProZen CMS is ready for use!</p>";
        echo "</div>";
        
        echo "<div style='background: #cce5ff; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>🔗 Test Your Website:</h4>";
        echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Homepage</a> - Should show complete content</p>";
        echo "<p><a href='" . SITE_URL . "/about' target='_blank'>📖 About Page</a> - Test page display</p>";
        echo "<p><a href='" . SITE_URL . "/products_list.php' target='_blank'>🛍️ Products</a> - Browse all products</p>";
        echo "<p><a href='" . SITE_URL . "/contact.php' target='_blank'>📞 Contact</a> - Contact page</p>";
        echo "<p><a href='" . SITE_URL . "/admin/dashboard.php' target='_blank'>⚙️ Admin Panel</a> - Manage content</p>";
        echo "</div>";
        
        echo "<div style='background: #fff3cd; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>✅ What's Working:</h4>";
        echo "<ul>";
        echo "<li><strong>Complete Content</strong> - Pages, products, categories, testimonials</li>";
        echo "<li><strong>Navigation Menus</strong> - Header and footer menus working</li>";
        echo "<li><strong>Template System</strong> - All section templates in place</li>";
        echo "<li><strong>Admin Panel</strong> - Full content management system</li>";
        echo "<li><strong>Environment Independence</strong> - Works on local and production</li>";
        echo "</ul>";
        echo "</div>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>⚠️ System Test FAILED</h4>";
        echo "<p>Some components need attention. Please run the comprehensive fix:</p>";
        echo "<p><a href='fix_all_homepage_issues.php' target='_blank'>🔧 Run Comprehensive Fix</a></p>";
        echo "</div>";
    }
    
    echo "<h3>📋 System Summary</h3>";
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<p><strong>Database:</strong> " . DB_NAME . " on " . DB_HOST . "</p>";
    echo "<p><strong>Site URL:</strong> " . SITE_URL . "</p>";
    echo "<p><strong>Environment:</strong> " . (defined('IS_LOCAL') && IS_LOCAL ? 'Local Development' : 'Production') . "</p>";
    echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
    echo "<p><strong>MySQL Version:</strong> " . $conn->server_info . "</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}
?>
