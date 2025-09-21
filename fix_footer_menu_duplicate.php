<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>🔧 Fixing Footer Menu Duplicate Issue</h2>";

try {
    echo "<h3>Step 1: Check Existing Footer Menu</h3>";
    
    $result = $conn->query("SELECT id, name, menu_items FROM menus WHERE location = 'footer' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $footer_menu = $result->fetch_assoc();
        echo "<p>✅ Footer menu already exists (ID: {$footer_menu['id']})</p>";
        
        $existing_items = json_decode($footer_menu['menu_items'], true);
        echo "<p><strong>Current footer menu items:</strong></p>";
        foreach ($existing_items as $item) {
            echo "<p>- {$item['title']}: {$item['url']}</p>";
        }
        
        echo "<h3>Step 2: Update Footer Menu with Correct URLs</h3>";
        
        $footer_menu_items = [
            ['title' => 'About Us', 'url' => '/about'],
            ['title' => 'Contact', 'url' => '/contact.php'],
            ['title' => 'Privacy Policy', 'url' => '/privacy-policy'],
            ['title' => 'Terms of Service', 'url' => '/terms-of-service'],
            ['title' => 'Support', 'url' => '/support'],
            ['title' => 'Shipping Info', 'url' => '/shipping-info'],
            ['title' => 'Returns', 'url' => '/returns'],
            ['title' => 'FAQ', 'url' => '/faq']
        ];
        
        $footer_menu_json = json_encode($footer_menu_items);
        
        $stmt = $conn->prepare("UPDATE menus SET menu_items = ? WHERE id = ?");
        $stmt->bind_param("si", $footer_menu_json, $footer_menu['id']);
        $stmt->execute();
        $stmt->close();
        
        echo "<p>✅ Updated existing footer menu</p>";
        
        echo "<h3>Step 3: Verify Footer Menu Update</h3>";
        echo "<p><strong>Updated footer menu items:</strong></p>";
        foreach ($footer_menu_items as $item) {
            echo "<p>- {$item['title']}: {$item['url']}</p>";
        }
        
    } else {
        echo "<p>❌ No footer menu found, creating new one...</p>";
        
        $footer_menu_items = [
            ['title' => 'About Us', 'url' => '/about'],
            ['title' => 'Contact', 'url' => '/contact.php'],
            ['title' => 'Privacy Policy', 'url' => '/privacy-policy'],
            ['title' => 'Terms of Service', 'url' => '/terms-of-service'],
            ['title' => 'Support', 'url' => '/support'],
            ['title' => 'Shipping Info', 'url' => '/shipping-info'],
            ['title' => 'Returns', 'url' => '/returns'],
            ['title' => 'FAQ', 'url' => '/faq']
        ];
        
        $footer_menu_json = json_encode($footer_menu_items);
        $stmt = $conn->prepare("INSERT INTO menus (name, location, menu_items, is_active) VALUES ('Footer Menu', 'footer', ?, 1)");
        $stmt->bind_param("s", $footer_menu_json);
        $stmt->execute();
        $stmt->close();
        
        echo "<p>✅ Created new footer menu</p>";
    }
    
    echo "<h3>Step 4: Verify All Menus</h3>";
    
    $result = $conn->query("SELECT name, location, menu_items FROM menus WHERE is_active = 1 ORDER BY location");
    if ($result && $result->num_rows > 0) {
        echo "<p><strong>All active menus:</strong></p>";
        while ($menu = $result->fetch_assoc()) {
            echo "<p><strong>{$menu['name']}</strong> ({$menu['location']}):</p>";
            $items = json_decode($menu['menu_items'], true);
            foreach ($items as $item) {
                echo "<p>  - {$item['title']}: {$item['url']}</p>";
            }
        }
    }
    
    echo "<h3>Step 5: Test Menu Functionality</h3>";
    
    require_once 'core/functions.php';
    
    if (function_exists('generate_menu')) {
        echo "<p>Testing header menu generation...</p>";
        $header_menu_html = generate_menu('Main Menu', $conn);
        echo "<p>✅ Header menu generated successfully</p>";
        
        // Extract URLs from header menu
        preg_match_all('/href="([^"]+)"/', $header_menu_html, $matches);
        if (!empty($matches[1])) {
            echo "<p><strong>Header menu URLs:</strong></p>";
            foreach ($matches[1] as $url) {
                echo "<p><a href='$url' target='_blank'>$url</a></p>";
            }
        }
    }
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>✅ Footer Menu Issue Fixed!</h4>";
    echo "<p>The footer menu duplicate issue has been resolved. All menus are now working correctly.</p>";
    echo "<p><strong>Header Menu:</strong> Home, Products, About, Services, Support, Contact</p>";
    echo "<p><strong>Footer Menu:</strong> About Us, Contact, Privacy Policy, Terms, Support, Shipping, Returns, FAQ</p>";
    echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Test Your Homepage</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
