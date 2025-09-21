<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>Fixing Product Images</h2>";

try {
    // Update existing products with proper image filenames
    $conn->query("UPDATE products SET featured_image = '68cfc186c96b3-front cover.jpg' WHERE id = 1 AND name = 'ZenBuds Pro 3'");
    $conn->query("UPDATE products SET featured_image = '68cfc18a0712d-front cover.jpg' WHERE id = 2 AND name = 'SmartGlow Ambient Light'");
    $conn->query("UPDATE products SET featured_image = '68cfc18baa5be-1755877095.png' WHERE id = 3 AND name = 'ProCharge Wireless Stand'");
    
    echo "<p>✅ Updated product images successfully</p>";
    
    // Check if products now have images
    $result = $conn->query("SELECT id, name, featured_image FROM products WHERE id IN (1,2,3)");
    if ($result && $result->num_rows > 0) {
        echo "<h3>Product Images Status:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Featured Image</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['featured_image'] ?? 'No image') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<p><strong>✅ Product images fixed! Now visit:</strong></p>";
    echo "<p><a href='" . SITE_URL . "/products_list.php'>Products List</a></p>";
    echo "<p><a href='" . SITE_URL . "'>Homepage</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

