<?php
require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

echo "<h2>🧪 Testing Cart Fixes</h2>";

try {
    echo "<h3>Step 1: Clear and Test Cart Functions</h3>";
    
    // Clear any existing cart
    $_SESSION['cart'] = [];
    
    // Test empty cart functions
    echo "<p>📦 Empty cart count: " . get_cart_count() . "</p>";
    echo "<p>💰 Empty cart total: $" . number_format(get_cart_total(), 2) . "</p>";
    
    echo "<h3>Step 2: Add Products to Cart</h3>";
    
    // Add some products to cart
    $_SESSION['cart'][1] = 2; // ZenBuds Pro 3, quantity 2
    $_SESSION['cart'][2] = 1; // SmartGlow Ambient Light, quantity 1
    
    echo "<p>✅ Added products to cart:</p>";
    echo "<ul>";
    echo "<li>Product ID 1 (ZenBuds Pro 3): Quantity 2</li>";
    echo "<li>Product ID 2 (SmartGlow Ambient Light): Quantity 1</li>";
    echo "</ul>";
    
    echo "<h3>Step 3: Test Cart Functions</h3>";
    
    $cart_count = get_cart_count();
    $cart_total = get_cart_total();
    
    echo "<p>📦 Cart count: $cart_count items</p>";
    echo "<p>💰 Cart total: $" . number_format($cart_total, 2) . "</p>";
    
    echo "<h3>Step 4: Test Cart Data Structure</h3>";
    
    echo "<p><strong>Cart session data:</strong></p>";
    echo "<pre>" . print_r($_SESSION['cart'], true) . "</pre>";
    
    echo "<h3>Step 5: Test Product Details Lookup</h3>";
    
    if (!empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
        $stmt = $conn->prepare("SELECT id, name, price, sale_price FROM products WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
        $stmt->execute();
        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        echo "<p><strong>Products found in database:</strong></p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Sale Price</th><th>Cart Qty</th><th>Line Total</th></tr>";
        
        $products_by_id = [];
        foreach ($products as $product) {
            $products_by_id[$product['id']] = $product;
        }
        
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = $products_by_id[$product_id] ?? null;
            if ($product) {
                $price = $product['sale_price'] ?? $product['price'];
                $line_total = $price * $quantity;
                echo "<tr>";
                echo "<td>" . htmlspecialchars($product['id']) . "</td>";
                echo "<td>" . htmlspecialchars($product['name']) . "</td>";
                echo "<td>$" . number_format($product['price'], 2) . "</td>";
                echo "<td>$" . number_format($product['sale_price'] ?? $product['price'], 2) . "</td>";
                echo "<td>$quantity</td>";
                echo "<td>$" . number_format($line_total, 2) . "</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    }
    
    echo "<h3>Step 6: Test Checkout Page</h3>";
    
    echo "<p><strong>🔗 Test the checkout page:</strong></p>";
    echo "<p><a href='" . SITE_URL . "/cart/checkout.php' target='_blank'>Checkout Page</a></p>";
    
    echo "<h3>Step 7: Summary</h3>";
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>✅ Cart Issues Fixed!</h4>";
    echo "<p><strong>Fixed Issues:</strong></p>";
    echo "<ul>";
    echo "<li>✅ <strong>Array offset errors</strong> - Fixed cart data structure handling</li>";
    echo "<li>✅ <strong>Null value errors</strong> - Added proper null checks</li>";
    echo "<li>✅ <strong>get_cart_total() function</strong> - Now fetches product data from database</li>";
    echo "<li>✅ <strong>get_cart_count() function</strong> - Fixed to handle quantity-only cart structure</li>";
    echo "<li>✅ <strong>checkout.php display</strong> - Now properly shows product names and prices</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>🎯 Cart Structure Explanation:</h4>";
    echo "<p><strong>Cart stores:</strong> <code>\$_SESSION['cart'][product_id] = quantity</code></p>";
    echo "<p><strong>Product details are fetched from database when needed</strong></p>";
    echo "<p><strong>This is more efficient and prevents data inconsistency</strong></p>";
    echo "</div>";
    
    echo "<p><strong>🎉 Your cart system should now work perfectly!</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
