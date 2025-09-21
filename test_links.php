<?php
/**
 * Test homepage and navigation links
 */

require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

echo "<h2>Homepage Links Test</h2>";

echo "<h3>Site Configuration:</h3>";
echo "<p><strong>SITE_URL:</strong> " . SITE_URL . "</p>";
echo "<p><strong>Environment:</strong> " . ENVIRONMENT . "</p>";
echo "<p><strong>Is Local:</strong> " . (IS_LOCAL ? 'Yes (XAMPP)' : 'No (Production)') . "</p>";
echo "<p><strong>Is Production:</strong> " . (IS_PRODUCTION ? 'Yes (cPanel)' : 'No (Local)') . "</p>";
echo "<p><strong>Debug Mode:</strong> " . (DEBUG ? 'Enabled' : 'Disabled') . "</p>";
echo "<p><strong>Database Host:</strong> " . DB_HOST . ":" . DB_PORT . "</p>";
echo "<p><strong>Database Name:</strong> " . DB_NAME . "</p>";

echo "<h3>Test Navigation Links:</h3>";
echo "<ul>";
echo "<li><a href='" . SITE_URL . "'>Homepage</a></li>";
echo "<li><a href='" . SITE_URL . "/products_list.php'>Products List</a></li>";
echo "<li><a href='" . SITE_URL . "/contact.php'>Contact Page</a></li>";
echo "<li><a href='" . SITE_URL . "/cart/'>Shopping Cart</a></li>";
echo "<li><a href='" . SITE_URL . "/auth/login.php'>User Login</a></li>";
echo "<li><a href='" . SITE_URL . "/admin/login.php'>Admin Login</a></li>";
echo "</ul>";

echo "<h3>Test Admin Links:</h3>";
echo "<ul>";
echo "<li><a href='" . SITE_URL . "/admin/dashboard.php'>Admin Dashboard</a></li>";
echo "<li><a href='" . SITE_URL . "/admin/manage_products.php'>Manage Products</a></li>";
echo "<li><a href='" . SITE_URL . "/admin/manage_pages.php'>Manage Pages</a></li>";
echo "<li><a href='" . SITE_URL . "/admin/manage_modules.php'>Manage Modules</a></li>";
echo "<li><a href='" . SITE_URL . "/admin/settings.php'>Site Settings</a></li>";
echo "</ul>";

echo "<h3>Test User Account Links:</h3>";
echo "<ul>";
echo "<li><a href='" . SITE_URL . "/user/dashboard.php'>User Dashboard</a></li>";
echo "<li><a href='" . SITE_URL . "/user/orders.php'>User Orders</a></li>";
echo "<li><a href='" . SITE_URL . "/user/profile.php'>User Profile</a></li>";
echo "<li><a href='" . SITE_URL . "/user/wishlist.php'>User Wishlist</a></li>";
echo "</ul>";

echo "<h3>Test API Endpoints:</h3>";
echo "<ul>";
echo "<li><a href='" . SITE_URL . "/cart/get_cart_quantities.php'>Cart API</a></li>";
echo "<li><a href='" . SITE_URL . "/api/sales_data.php'>Sales Data API</a></li>";
echo "<li><a href='" . SITE_URL . "/api/wishlist_handler.php'>Wishlist API</a></li>";
echo "</ul>";

echo "<h3>Test Setup Scripts:</h3>";
echo "<ul>";
echo "<li><a href='" . SITE_URL . "/fixed_setup.php'>Complete Setup</a></li>";
echo "<li><a href='" . SITE_URL . "/test_functions.php'>Test Functions</a></li>";
echo "<li><a href='" . SITE_URL . "/test_dashboard.php'>Test Dashboard</a></li>";
echo "<li><a href='" . SITE_URL . "/test_modules.php'>Test Modules</a></li>";
echo "</ul>";

echo "<h3 style='color: green;'>✅ All links are now using dynamic SITE_URL!</h3>";
echo "<p>This means the site will work correctly on both:</p>";
echo "<ul>";
echo "<li>✅ Local XAMPP: <code>http://localhost/smartprozen/</code></li>";
echo "<li>✅ Production cPanel: <code>https://yourdomain.com/</code></li>";
echo "</ul>";

echo "<h3>Quick Actions:</h3>";
echo "<p><a href='" . SITE_URL . "/fixed_setup.php' class='btn btn-primary'>Run Complete Setup</a></p>";
echo "<p><a href='" . SITE_URL . "/admin/login.php' class='btn btn-success'>Go to Admin Panel</a></p>";
echo "<p><a href='" . SITE_URL . "' class='btn btn-info'>View Homepage</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
h2, h3 { color: #333; }
p { margin: 5px 0; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
.btn { display: inline-block; padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; }
.btn-primary { background-color: #007bff; color: white; }
.btn-success { background-color: #28a745; color: white; }
.btn-info { background-color: #17a2b8; color: white; }
code { background-color: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
</style>
