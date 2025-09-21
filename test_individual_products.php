<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>🧪 Testing Individual Product Pages</h2>";

try {
    echo "<h3>Step 1: Check Product Images Table</h3>";
    
    // Check if product_images table exists
    $result = $conn->query("SHOW TABLES LIKE 'product_images'");
    if ($result->num_rows > 0) {
        echo "<p>✅ product_images table exists</p>";
        
        // Check table structure
        $result = $conn->query("DESCRIBE product_images");
        echo "<p><strong>Table structure:</strong></p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check if there are any product images
        $result = $conn->query("SELECT COUNT(*) as count FROM product_images");
        $count = $result->fetch_assoc()['count'];
        echo "<p>📊 Product images in database: $count</p>";
        
    } else {
        echo "<p>❌ product_images table does NOT exist</p>";
        echo "<p><strong>Run the fix first:</strong> <a href='fix_missing_tables_and_pages.php'>fix_missing_tables_and_pages.php</a></p>";
        exit;
    }
    
    echo "<h3>Step 2: Check Products</h3>";
    
    // Get all products
    $result = $conn->query("SELECT id, name, featured_image FROM products WHERE is_published = 1 LIMIT 5");
    if ($result && $result->num_rows > 0) {
        echo "<p>📦 Products available:</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Featured Image</th><th>Test Link</th></tr>";
        
        while ($product = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($product['id']) . "</td>";
            echo "<td>" . htmlspecialchars($product['name']) . "</td>";
            echo "<td>" . htmlspecialchars($product['featured_image'] ?? 'No image') . "</td>";
            $product_url = SITE_URL . '/product.php?id=' . $product['id'];
            echo "<td><a href='$product_url' target='_blank'>Test Product Page</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p>❌ No products found in database</p>";
    }
    
    echo "<h3>Step 3: Test Product Page URLs</h3>";
    
    // Test first few products
    $result = $conn->query("SELECT id, name FROM products WHERE is_published = 1 LIMIT 3");
    if ($result && $result->num_rows > 0) {
        echo "<p><strong>Test these individual product pages:</strong></p>";
        while ($product = $result->fetch_assoc()) {
            $product_url = SITE_URL . '/product.php?id=' . $product['id'];
            echo "<p><a href='$product_url' target='_blank'>🔗 {$product['name']}: $product_url</a></p>";
        }
    }
    
    echo "<h3>Step 4: Summary</h3>";
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>✅ Individual Product Pages Should Work!</h4>";
    echo "<p><strong>How it works:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Product List:</strong> <code>" . SITE_URL . "/products_list.php</code> - Shows all products</li>";
    echo "<li><strong>Individual Products:</strong> <code>" . SITE_URL . "/product.php?id=X</code> - Shows single product details</li>";
    echo "<li><strong>Navigation:</strong> Click any product from the list or homepage to view details</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>🔗 Quick Test Links:</h4>";
    echo "<p><a href='" . SITE_URL . "/products_list.php' target='_blank'>🛍️ View All Products</a></p>";
    echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Homepage (with featured products)</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
