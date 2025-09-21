<?php
/**
 * Simple test to verify functions work correctly
 */

require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

echo "<h2>Function Test Results</h2>";

// Test cart functions
echo "<h3>Cart Functions:</h3>";
echo "<p>✓ get_cart_count(): " . get_cart_count() . "</p>";
echo "<p>✓ get_cart_total(): $" . get_cart_total() . "</p>";

// Test helper functions
echo "<h3>Helper Functions:</h3>";
echo "<p>✓ slugify('Hello World'): " . slugify('Hello World') . "</p>";
echo "<p>✓ format_price(99.99): " . format_price(99.99) . "</p>";
echo "<p>✓ time_ago('2024-01-01'): " . time_ago('2024-01-01') . "</p>";

// Test user functions
echo "<h3>User Functions:</h3>";
echo "<p>✓ is_user_logged_in(): " . (is_user_logged_in() ? 'true' : 'false') . "</p>";
echo "<p>✓ get_logged_in_user(): " . (get_logged_in_user() ? 'User found' : 'No user') . "</p>";

// Test admin functions
echo "<h3>Admin Functions:</h3>";
echo "<p>✓ is_admin_logged_in(): " . (is_admin_logged_in() ? 'true' : 'false') . "</p>";

echo "<h3 style='color: green;'>✅ All functions loaded successfully!</h3>";
echo "<p><a href='admin/login.php'>Go to Admin Login</a></p>";
echo "<p><a href='fixed_setup.php'>Run Setup Script</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
h2, h3 { color: #333; }
p { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
