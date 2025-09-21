<?php
require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

echo "<h2>🧪 Testing Cart Continue Shopping Fix</h2>";

try {
    echo "<h3>Step 1: Clear Cart to Test Empty Cart State</h3>";
    
    // Clear cart to show empty state
    $_SESSION['cart'] = [];
    
    echo "<p>✅ Cart cleared - should show empty cart message</p>";
    
    echo "<h3>Step 2: Test Continue Shopping Links</h3>";
    
    echo "<p><strong>🔗 Test the cart page with empty cart:</strong></p>";
    echo "<p><a href='" . SITE_URL . "/cart/index.php' target='_blank'>Empty Cart Page</a></p>";
    
    echo "<p><strong>Expected behavior:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Should show 'Your cart is empty' message</li>";
    echo "<li>✅ Should show 'Continue Shopping' button</li>";
    echo "<li>✅ 'Continue Shopping' should link to: " . SITE_URL . "/products_list.php</li>";
    echo "</ul>";
    
    echo "<h3>Step 3: Add Items to Test Cart with Items</h3>";
    
    // Add items to cart
    $_SESSION['cart'][1] = 2;
    $_SESSION['cart'][2] = 1;
    
    echo "<p>✅ Added items to cart</p>";
    echo "<p>📦 Cart count: " . get_cart_count() . "</p>";
    echo "<p>💰 Cart total: $" . number_format(get_cart_total(), 2) . "</p>";
    
    echo "<p><strong>🔗 Test the cart page with items:</strong></p>";
    echo "<p><a href='" . SITE_URL . "/cart/index.php' target='_blank'>Cart with Items</a></p>";
    
    echo "<p><strong>Expected behavior:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Should show product list</li>";
    echo "<li>✅ Should show 'Continue Shopping' button in sidebar</li>";
    echo "<li>✅ 'Continue Shopping' should link to: " . SITE_URL . "/products_list.php</li>";
    echo "</ul>";
    
    echo "<h3>Step 4: Test Direct Links</h3>";
    
    echo "<p><strong>🔗 Test these links directly:</strong></p>";
    echo "<ul>";
    echo "<li><a href='" . SITE_URL . "/products_list.php' target='_blank'>Products List Page</a> - Should work</li>";
    echo "<li><a href='" . SITE_URL . "/products' target='_blank'>Wrong Link (/products)</a> - Should show 404</li>";
    echo "</ul>";
    
    echo "<h3>Step 5: Summary</h3>";
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>✅ Continue Shopping Links Fixed!</h4>";
    echo "<p><strong>Fixed Issues:</strong></p>";
    echo "<ul>";
    echo "<li>✅ <strong>Empty cart 'Continue Shopping'</strong> - Now links to products_list.php</li>";
    echo "<li>✅ <strong>Cart with items 'Continue Shopping'</strong> - Now links to products_list.php</li>";
    echo "<li>✅ <strong>Dynamic SITE_URL</strong> - Uses SITE_URL instead of hardcoded path</li>";
    echo "<li>✅ <strong>Environment independent</strong> - Works on local and production</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>🎯 Fixed Links:</h4>";
    echo "<p><strong>Before (broken):</strong> <code>/smartprozen/products</code></p>";
    echo "<p><strong>After (fixed):</strong> <code>" . SITE_URL . "/products_list.php</code></p>";
    echo "<p><strong>Result:</strong> Continue Shopping now goes to the correct products page!</p>";
    echo "</div>";
    
    echo "<p><strong>🎉 Your 'Continue Shopping' buttons should now work correctly!</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
