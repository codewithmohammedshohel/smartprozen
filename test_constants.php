<?php
/**
 * Test configuration constants
 */

require_once 'config.php';

echo "<h2>Configuration Constants Test</h2>";

echo "<h3>Environment Detection:</h3>";
echo "<p><strong>ENVIRONMENT:</strong> " . ENVIRONMENT . "</p>";
echo "<p><strong>IS_LOCAL:</strong> " . (IS_LOCAL ? 'true' : 'false') . "</p>";
echo "<p><strong>IS_PRODUCTION:</strong> " . (IS_PRODUCTION ? 'true' : 'false') . "</p>";

echo "<h3>Database Configuration:</h3>";
echo "<p><strong>DB_HOST:</strong> " . DB_HOST . "</p>";
echo "<p><strong>DB_PORT:</strong> " . DB_PORT . "</p>";
echo "<p><strong>DB_USER:</strong> " . DB_USER . "</p>";
echo "<p><strong>DB_NAME:</strong> " . DB_NAME . "</p>";

echo "<h3>Site Configuration:</h3>";
echo "<p><strong>SITE_URL:</strong> " . SITE_URL . "</p>";
echo "<p><strong>DEBUG:</strong> " . (DEBUG ? 'true' : 'false') . "</p>";
echo "<p><strong>DEFAULT_LANG:</strong> " . DEFAULT_LANG . "</p>";
echo "<p><strong>TIMEZONE:</strong> " . TIMEZONE . "</p>";

echo "<h3>Server Information:</h3>";
echo "<p><strong>SERVER_NAME:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'Not set') . "</p>";
echo "<p><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</p>";

echo "<h3 style='color: green;'>✅ All constants are properly defined!</h3>";

echo "<p><a href='test_links.php'>← Back to Links Test</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
h2, h3 { color: #333; }
p { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
