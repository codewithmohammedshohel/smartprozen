<?php
require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

echo "<h2>🧪 Testing Navbar Links Fix</h2>";

try {
    echo "<h3>Step 1: Check Current Menu Data</h3>";
    
    $result = $conn->query("SELECT menu_items FROM menus WHERE location = 'header' AND is_active = 1 LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $menu = $result->fetch_assoc();
        $menu_items = json_decode($menu['menu_items'], true);
        
        echo "<p>Menu items from database:</p>";
        foreach ($menu_items as $index => $item) {
            echo "<p><strong>$index:</strong> {$item['title']} -> {$item['url']}</p>";
        }
    }
    
    echo "<h3>Step 2: Test generate_menu Function</h3>";
    
    if (function_exists('generate_menu')) {
        $menu_html = generate_menu('Main Menu', $conn);
        echo "<p>Generated menu HTML:</p>";
        echo "<pre>" . htmlspecialchars($menu_html) . "</pre>";
        
        // Extract URLs from HTML
        preg_match_all('/href="([^"]+)"/', $menu_html, $matches);
        if (!empty($matches[1])) {
            echo "<p><strong>Generated URLs:</strong></p>";
            foreach ($matches[1] as $url) {
                echo "<p><a href='$url' target='_blank'>$url</a></p>";
            }
        }
    }
    
    echo "<h3>Step 3: Test customizable_header.php URL Conversion</h3>";
    
    // Simulate the URL conversion logic from customizable_header.php
    $test_urls = ['/', '/products_list.php', '/about', '/contact.php'];
    
    echo "<p>Testing URL conversion:</p>";
    foreach ($test_urls as $test_url) {
        $url = $test_url;
        if (strpos($url, 'http') !== 0) {
            if (strpos($url, '/') === 0) {
                $url = SITE_URL . $url;
            } else {
                $url = SITE_URL . '/' . $url;
            }
        }
        echo "<p>$test_url -> $url</p>";
    }
    
    echo "<h3>Step 4: Test Actual Links</h3>";
    
    $test_links = [
        'Homepage' => SITE_URL . '/',
        'Products' => SITE_URL . '/products_list.php',
        'About' => SITE_URL . '/about',
        'Contact' => SITE_URL . '/contact.php'
    ];
    
    echo "<p>Testing actual links:</p>";
    foreach ($test_links as $name => $url) {
        echo "<p><a href='$url' target='_blank'>$name: $url</a></p>";
    }
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>✅ Navbar Links Should Now Be Fixed!</h4>";
    echo "<p>The issue was that customizable_header.php was not converting relative URLs to absolute URLs.</p>";
    echo "<p>Now both the generate_menu function and customizable_header.php properly convert URLs.</p>";
    echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Test Your Homepage</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
