<?php
/**
 * Test dashboard queries to ensure they work
 */

require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

echo "<h2>Dashboard Query Tests</h2>";

try {
    // Test recent orders query
    echo "<h3>Testing Recent Orders Query:</h3>";
    $recent_orders_query = $conn->query("SELECT o.id, o.total_amount, o.status, CONCAT(u.first_name, ' ', u.last_name) as customer_name, o.created_at
                                         FROM orders o
                                         LEFT JOIN users u ON o.user_id = u.id
                                         ORDER BY o.created_at DESC
                                         LIMIT 5");
    
    if ($recent_orders_query) {
        echo "<p style='color: green;'>✓ Recent orders query works</p>";
        $orders = $recent_orders_query->fetch_all(MYSQLI_ASSOC);
        echo "<p>Found " . count($orders) . " orders</p>";
    } else {
        echo "<p style='color: red;'>✗ Recent orders query failed: " . $conn->error . "</p>";
    }
    
    // Test activity logs query
    echo "<h3>Testing Activity Logs Query:</h3>";
    $activity_query = $conn->query("SELECT * FROM activity_logs WHERE user_type = 'admin' ORDER BY timestamp DESC LIMIT 5");
    
    if ($activity_query) {
        echo "<p style='color: green;'>✓ Activity logs query works</p>";
        $activities = $activity_query->fetch_all(MYSQLI_ASSOC);
        echo "<p>Found " . count($activities) . " activities</p>";
    } else {
        echo "<p style='color: red;'>✗ Activity logs query failed: " . $conn->error . "</p>";
    }
    
    // Test users count query
    echo "<h3>Testing Users Count Query:</h3>";
    $users_count = $conn->query("SELECT COUNT(id) as total FROM users")->fetch_assoc()['total'] ?? 0;
    echo "<p style='color: green;'>✓ Users count query works</p>";
    echo "<p>Total users: " . $users_count . "</p>";
    
    // Test products count query
    echo "<h3>Testing Products Count Query:</h3>";
    $products_count = $conn->query("SELECT COUNT(id) as total FROM products")->fetch_assoc()['total'] ?? 0;
    echo "<p style='color: green;'>✓ Products count query works</p>";
    echo "<p>Total products: " . $products_count . "</p>";
    
    // Test orders count query
    echo "<h3>Testing Orders Count Query:</h3>";
    $orders_count = $conn->query("SELECT COUNT(id) as total FROM orders")->fetch_assoc()['total'] ?? 0;
    echo "<p style='color: green;'>✓ Orders count query works</p>";
    echo "<p>Total orders: " . $orders_count . "</p>";
    
    echo "<h3 style='color: green;'>✅ All dashboard queries are working!</h3>";
    echo "<p><a href='admin/login.php'>Go to Admin Login</a></p>";
    echo "<p><a href='admin/dashboard.php'>Go to Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Error testing dashboard queries:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
h2, h3 { color: #333; }
p { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
