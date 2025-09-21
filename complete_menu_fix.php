<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>🔧 Complete Menu Fix</h2>";

try {
    echo "<h3>Step 1: Clear All Existing Menus</h3>";
    
    // Delete all existing menus to start fresh
    $conn->query("DELETE FROM menus WHERE location = 'header'");
    echo "<p>✅ Cleared existing header menus</p>";
    
    echo "<h3>Step 2: Create New Menu with Correct URLs</h3>";
    
    // Create the correct menu structure
    $main_menu = [
        [
            'title' => 'Home',
            'url' => '/',
            'children' => []
        ],
        [
            'title' => 'Products',
            'url' => '/products_list.php',
            'children' => [
                ['title' => 'Smart Home', 'url' => '/products_list.php?category=smart-home'],
                ['title' => 'Audio Equipment', 'url' => '/products_list.php?category=professional-audio'],
                ['title' => 'Mobile Accessories', 'url' => '/products_list.php?category=mobile-accessories'],
                ['title' => 'Wearable Tech', 'url' => '/products_list.php?category=wearable-tech'],
                ['title' => 'Gaming', 'url' => '/products_list.php?category=gaming-accessories'],
                ['title' => 'Digital Products', 'url' => '/products_list.php?category=digital-products']
            ]
        ],
        [
            'title' => 'About',
            'url' => '/about',
            'children' => []
        ],
        [
            'title' => 'Services',
            'url' => '/services',
            'children' => []
        ],
        [
            'title' => 'Support',
            'url' => '/support',
            'children' => []
        ],
        [
            'title' => 'Contact',
            'url' => '/contact.php',
            'children' => []
        ]
    ];
    
    $main_menu_json = json_encode($main_menu);
    $stmt = $conn->prepare("INSERT INTO menus (name, location, menu_items, is_active) VALUES ('Main Menu', 'header', ?, 1)");
    $stmt->bind_param("s", $main_menu_json);
    $stmt->execute();
    $stmt->close();
    
    echo "<p>✅ Created new main menu</p>";
    
    echo "<h3>Step 3: Create Footer Menu</h3>";
    
    $footer_menu = [
        ['title' => 'About Us', 'url' => '/about'],
        ['title' => 'Contact', 'url' => '/contact.php'],
        ['title' => 'Privacy Policy', 'url' => '/privacy-policy'],
        ['title' => 'Terms of Service', 'url' => '/terms-of-service'],
        ['title' => 'Support', 'url' => '/support'],
        ['title' => 'Shipping Info', 'url' => '/shipping-info'],
        ['title' => 'Returns', 'url' => '/returns'],
        ['title' => 'FAQ', 'url' => '/faq']
    ];
    
    $footer_menu_json = json_encode($footer_menu);
    $stmt = $conn->prepare("INSERT INTO menus (name, location, menu_items, is_active) VALUES ('Footer Menu', 'footer', ?, 1)");
    $stmt->bind_param("s", $footer_menu_json);
    $stmt->execute();
    $stmt->close();
    
    echo "<p>✅ Created footer menu</p>";
    
    echo "<h3>Step 4: Verify Menu Data</h3>";
    
    $result = $conn->query("SELECT name, location, menu_items FROM menus WHERE is_active = 1");
    if ($result && $result->num_rows > 0) {
        echo "<p><strong>Active menus in database:</strong></p>";
        while ($menu = $result->fetch_assoc()) {
            echo "<p><strong>{$menu['name']}</strong> ({$menu['location']}):</p>";
            $items = json_decode($menu['menu_items'], true);
            foreach ($items as $item) {
                echo "<p>  - {$item['title']}: {$item['url']}</p>";
            }
        }
    }
    
    echo "<h3>Step 5: Test URL Generation</h3>";
    
    echo "<p><strong>How URLs will be generated:</strong></p>";
    foreach ($main_menu as $item) {
        $url = $item['url'];
        if (strpos($url, '/') === 0) {
            $full_url = SITE_URL . $url;
        } else {
            $full_url = SITE_URL . '/' . $url;
        }
        echo "<p><a href='$full_url' target='_blank'>{$item['title']}: $full_url</a></p>";
    }
    
    echo "<h3>Step 6: Test generate_menu Function</h3>";
    
    require_once 'core/functions.php';
    
    if (function_exists('generate_menu')) {
        $menu_html = generate_menu('Main Menu', $conn);
        echo "<p><strong>Generated menu HTML:</strong></p>";
        echo "<pre>" . htmlspecialchars($menu_html) . "</pre>";
        
        // Extract and test URLs
        preg_match_all('/href="([^"]+)"/', $menu_html, $matches);
        if (!empty($matches[1])) {
            echo "<p><strong>Extracted URLs from generated HTML:</strong></p>";
            foreach ($matches[1] as $url) {
                echo "<p><a href='$url' target='_blank'>$url</a></p>";
            }
        }
    }
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>✅ Complete Menu Fix Applied!</h4>";
    echo "<p>All menu URLs have been corrected:</p>";
    echo "<ul>";
    echo "<li><strong>Products</strong> now points to <code>/products_list.php</code></li>";
    echo "<li><strong>Home</strong> points to <code>/</code></li>";
    echo "<li><strong>About</strong> points to <code>/about</code></li>";
    echo "<li><strong>Contact</strong> points to <code>/contact.php</code></li>";
    echo "</ul>";
    echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Test Your Homepage</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
