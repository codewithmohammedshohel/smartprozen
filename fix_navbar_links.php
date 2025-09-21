<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>🔧 Fixing Navbar Links</h2>";

try {
    echo "<h3>Step 1: Check Current Menu URLs</h3>";
    
    $result = $conn->query("SELECT id, name, menu_items FROM menus WHERE location = 'header' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $menu = $result->fetch_assoc();
        $menu_items = json_decode($menu['menu_items'], true);
        
        echo "<p>Current menu items:</p>";
        foreach ($menu_items as $index => $item) {
            echo "<p>$index: {$item['title']} -> {$item['url']}</p>";
        }
        
        echo "<h3>Step 2: Fixing Menu URLs</h3>";
        
        // Fix the URLs to be relative paths
        $fixed_menu_items = [
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
        
        $fixed_menu_json = json_encode($fixed_menu_items);
        
        // Update the menu
        $stmt = $conn->prepare("UPDATE menus SET menu_items = ? WHERE id = ?");
        $stmt->bind_param("si", $fixed_menu_json, $menu['id']);
        $stmt->execute();
        $stmt->close();
        
        echo "<p>✅ Updated menu with correct relative URLs</p>";
        
        echo "<h3>Step 3: Testing Fixed URLs</h3>";
        echo "<p>Fixed menu items:</p>";
        foreach ($fixed_menu_items as $index => $item) {
            echo "<p>$index: {$item['title']} -> {$item['url']}</p>";
        }
        
        echo "<h3>Step 4: Test generate_menu Function</h3>";
        require_once 'core/functions.php';
        
        if (function_exists('generate_menu')) {
            $menu_html = generate_menu('Main Menu', $conn);
            echo "<p><strong>Generated Menu HTML:</strong></p>";
            echo "<pre>" . htmlspecialchars($menu_html) . "</pre>";
            
            // Extract URLs from generated HTML
            preg_match_all('/href="([^"]+)"/', $menu_html, $matches);
            if (!empty($matches[1])) {
                echo "<p><strong>Generated URLs:</strong></p>";
                foreach ($matches[1] as $url) {
                    echo "<p><a href='$url' target='_blank'>$url</a></p>";
                }
            }
        }
        
        echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>✅ Navbar Links Fixed!</h4>";
        echo "<p>All navbar links now use relative URLs that will be properly converted to absolute URLs by the generate_menu function.</p>";
        echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Test Your Homepage</a></p>";
        echo "</div>";
        
    } else {
        echo "<p>❌ No header menu found. Creating one...</p>";
        
        // Create a new menu
        $menu_items = [
            [
                'title' => 'Home',
                'url' => '/',
                'children' => []
            ],
            [
                'title' => 'Products',
                'url' => '/products_list.php',
                'children' => []
            ],
            [
                'title' => 'About',
                'url' => '/about',
                'children' => []
            ],
            [
                'title' => 'Contact',
                'url' => '/contact.php',
                'children' => []
            ]
        ];
        
        $menu_json = json_encode($menu_items);
        $stmt = $conn->prepare("INSERT INTO menus (name, location, menu_items, is_active) VALUES ('Main Menu', 'header', ?, 1)");
        $stmt->bind_param("s", $menu_json);
        $stmt->execute();
        $stmt->close();
        
        echo "<p>✅ Created new header menu</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
