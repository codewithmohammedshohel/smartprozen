<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>🎫 Adding Missing Coupon</h2>";

try {
    // Add one more coupon to meet the 3+ requirement
    $stmt = $conn->prepare("INSERT IGNORE INTO coupons (code, description, discount_type, discount_value, minimum_amount, maximum_discount, usage_limit, valid_until, used_count, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $code = 'FREESHIP';
    $description = 'Free Shipping';
    $discount_type = 'fixed';
    $discount_value = null;
    $minimum_amount = 75;
    $maximum_discount = 15;
    $usage_limit = 200;
    $valid_until = '2025-12-31';
    $used_count = 0;
    $is_active = 1;
    
    $stmt->bind_param("sssdddssii", $code, $description, $discount_type, $discount_value, $minimum_amount, $maximum_discount, $usage_limit, $valid_until, $used_count, $is_active);
    $stmt->execute();
    $stmt->close();
    
    echo "<p>✅ Added coupon: FREESHIP - Free Shipping</p>";
    
    // Verify coupon count
    $result = $conn->query("SELECT COUNT(*) as count FROM coupons WHERE is_active = 1");
    $coupon_count = $result->fetch_assoc()['count'];
    
    echo "<p>📊 Total active coupons: $coupon_count</p>";
    
    if ($coupon_count >= 3) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>🎉 Perfect! System Now Complete</h4>";
        echo "<p>You now have $coupon_count coupons, which meets the requirement of 3+.</p>";
        echo "<p><a href='final_system_test.php' target='_blank'>🧪 Run Final System Test Again</a></p>";
        echo "</div>";
    } else {
        echo "<p>❌ Still need more coupons. Current count: $coupon_count</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
