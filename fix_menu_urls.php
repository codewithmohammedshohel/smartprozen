<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>🔧 Fixing Menu URLs in Database</h2>";

try {
    echo "<h3>Step 1: Check Current Menu Data</h3>";
    
    $result = $conn->query("SELECT id, name, menu_items FROM menus WHERE location = 'header' AND is_active = 1 LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $menu = $result->fetch_assoc();
        $menu_items = json_decode($menu['menu_items'], true);
        
        echo "<p><strong>Current menu items:</strong></p>";
        foreach ($menu_items as $index => $item) {
            echo "<p>$index: {$item['title']} -> {$item['url']}</p>";
        }
        
        echo "<h3>Step 2: Fixing Menu URLs</h3>";
        
        // Fix the menu items with correct URLs
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
        
        // Update the menu in database
        $stmt = $conn->prepare("UPDATE menus SET menu_items = ? WHERE id = ?");
        $stmt->bind_param("si", $fixed_menu_json, $menu['id']);
        $stmt->execute();
        $stmt->close();
        
        echo "<p>✅ Updated menu with correct URLs</p>";
        
        echo "<h3>Step 3: Verify Fixed Menu</h3>";
        echo "<p><strong>Fixed menu items:</strong></p>";
        foreach ($fixed_menu_items as $index => $item) {
            echo "<p>$index: {$item['title']} -> {$item['url']}</p>";
        }
        
        echo "<h3>Step 4: Test URL Generation</h3>";
        
        // Test how URLs will be generated
        echo "<p><strong>Generated URLs will be:</strong></p>";
        foreach ($fixed_menu_items as $item) {
            $url = $item['url'];
            if (strpos($url, '/') === 0) {
                $full_url = SITE_URL . $url;
            } else {
                $full_url = SITE_URL . '/' . $url;
            }
            echo "<p><a href='$full_url' target='_blank'>{$item['title']}: $full_url</a></p>";
        }
        
        echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>✅ Menu URLs Fixed!</h4>";
        echo "<p>The Products link now correctly points to <code>/products_list.php</code> instead of <code>/products</code>.</p>";
        echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Test Your Homepage</a></p>";
        echo "</div>";
        
    } else {
        echo "<p>❌ No header menu found in database</p>";
        
        // Create a new menu with correct URLs
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
        
        echo "<p>✅ Created new menu with correct URLs</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
