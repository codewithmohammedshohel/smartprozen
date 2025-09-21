<?php
require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

echo "<h2>🔍 Debugging Navbar Links</h2>";

try {
    echo "<h3>Step 1: Check Menu Data in Database</h3>";
    
    $result = $conn->query("SELECT name, location, menu_items FROM menus WHERE location = 'header' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $menu = $result->fetch_assoc();
        echo "<p><strong>Menu Name:</strong> " . htmlspecialchars($menu['name']) . "</p>";
        echo "<p><strong>Location:</strong> " . htmlspecialchars($menu['location']) . "</p>";
        
        $menu_items = json_decode($menu['menu_items'], true);
        echo "<p><strong>Raw Menu Items:</strong></p>";
        echo "<pre>" . htmlspecialchars(print_r($menu_items, true)) . "</pre>";
        
        echo "<h3>Step 2: Test generate_menu Function</h3>";
        if (function_exists('generate_menu')) {
            $menu_html = generate_menu('Main Menu', $conn);
            echo "<p><strong>Generated Menu HTML:</strong></p>";
            echo "<pre>" . htmlspecialchars($menu_html) . "</pre>";
        } else {
            echo "<p>❌ generate_menu function not found</p>";
        }
        
        echo "<h3>Step 3: URL Analysis</h3>";
        echo "<p><strong>SITE_URL:</strong> " . SITE_URL . "</p>";
        
        foreach ($menu_items as $index => $item) {
            echo "<p><strong>Item $index:</strong></p>";
            echo "<ul>";
            echo "<li>Title: " . htmlspecialchars($item['title'] ?? 'N/A') . "</li>";
            echo "<li>URL: " . htmlspecialchars($item['url'] ?? 'N/A') . "</li>";
            
            $url = $item['url'];
            if (strpos($url, 'http') !== 0) {
                if (strpos($url, '/') === 0) {
                    $converted_url = SITE_URL . $url;
                    echo "<li>Converted URL: " . htmlspecialchars($converted_url) . "</li>";
                } else {
                    $converted_url = SITE_URL . '/' . $url;
                    echo "<li>Converted URL: " . htmlspecialchars($converted_url) . "</li>";
                }
            } else {
                echo "<li>URL is already absolute</li>";
            }
            echo "</ul>";
        }
        
    } else {
        echo "<p>❌ No header menu found in database</p>";
    }
    
    echo "<h3>Step 4: Fix Menu URLs</h3>";
    
    // Check if we need to fix the menu URLs
    $result = $conn->query("SELECT id, menu_items FROM menus WHERE location = 'header' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $menu = $result->fetch_assoc();
        $menu_items = json_decode($menu['menu_items'], true);
        
        $needs_fix = false;
        foreach ($menu_items as $item) {
            if (isset($item['url']) && strpos($item['url'], 'http') === 0) {
                $needs_fix = true;
                break;
            }
        }
        
        if ($needs_fix) {
            echo "<p>⚠️ Menu URLs need to be fixed - they contain absolute URLs</p>";
            
            // Fix the menu URLs
            foreach ($menu_items as &$item) {
                if (isset($item['url'])) {
                    $url = $item['url'];
                    if (strpos($url, 'http') === 0) {
                        // Convert absolute URL to relative
                        if (strpos($url, SITE_URL) === 0) {
                            $item['url'] = str_replace(SITE_URL, '', $url);
                        }
                    }
                }
            }
            
            // Update the menu in database
            $fixed_menu_items = json_encode($menu_items);
            $stmt = $conn->prepare("UPDATE menus SET menu_items = ? WHERE id = ?");
            $stmt->bind_param("si", $fixed_menu_items, $menu['id']);
            $stmt->execute();
            $stmt->close();
            
            echo "<p>✅ Fixed menu URLs in database</p>";
            echo "<p><strong>Fixed Menu Items:</strong></p>";
            echo "<pre>" . htmlspecialchars(print_r($menu_items, true)) . "</pre>";
            
        } else {
            echo "<p>✅ Menu URLs look correct</p>";
        }
    }
    
    echo "<h3>Step 5: Test Fixed Menu</h3>";
    if (function_exists('generate_menu')) {
        $menu_html = generate_menu('Main Menu', $conn);
        echo "<p><strong>Fixed Menu HTML:</strong></p>";
        echo "<pre>" . htmlspecialchars($menu_html) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
