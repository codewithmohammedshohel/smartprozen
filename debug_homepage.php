<?php
/**
 * Debug homepage redirect issue
 */

echo "<h2>Homepage Debug Information</h2>";

echo "<h3>Current URL Information:</h3>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</p>";
echo "<p><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "</p>";
echo "<p><strong>PHP_SELF:</strong> " . ($_SERVER['PHP_SELF'] ?? 'Not set') . "</p>";

echo "<h3>Session Information:</h3>";
session_start();
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Data:</strong> " . print_r($_SESSION, true) . "</p>";

echo "<h3>Configuration Test:</h3>";
require_once 'config.php';
echo "<p><strong>SITE_URL:</strong> " . SITE_URL . "</p>";
echo "<p><strong>ENVIRONMENT:</strong> " . ENVIRONMENT . "</p>";

echo "<h3>Database Connection Test:</h3>";
try {
    require_once 'core/db.php';
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // Test if pages table exists and has data
    $result = $conn->query("SELECT COUNT(*) as count FROM pages");
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "<p><strong>Pages in database:</strong> " . $count . "</p>";
        
        // Check if home page exists
        $home_result = $conn->query("SELECT * FROM pages WHERE slug = 'home'");
        if ($home_result && $home_result->num_rows > 0) {
            $home_page = $home_result->fetch_assoc();
            echo "<p style='color: green;'>✅ Home page exists in database</p>";
            echo "<p><strong>Home page title:</strong> " . htmlspecialchars($home_page['title']) . "</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ No home page found in database</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Pages table query failed: " . $conn->error . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<h3>File Existence Check:</h3>";
$files_to_check = [
    'index.php',
    'page.php',
    'includes/header.php',
    'includes/customizable_header.php',
    '.htaccess'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file exists</p>";
    } else {
        echo "<p style='color: red;'>❌ $file missing</p>";
    }
}

echo "<h3>Redirect Test:</h3>";
echo "<p><a href='" . SITE_URL . "'>Click here to test homepage</a></p>";
echo "<p><a href='" . SITE_URL . "/debug_homepage.php'>Refresh this debug page</a></p>";

echo "<h3>Manual Homepage Test:</h3>";
echo "<p>Testing what happens when we include index.php...</p>";
echo "<hr>";

// Try to capture any output from including index.php
ob_start();
try {
    include 'index.php';
    $output = ob_get_clean();
    echo "<p style='color: green;'>✅ index.php executed without redirect</p>";
    echo "<p><strong>Output length:</strong> " . strlen($output) . " characters</p>";
} catch (Exception $e) {
    ob_end_clean();
    echo "<p style='color: red;'>❌ Error including index.php: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 1000px; margin: 50px auto; padding: 20px; }
h2, h3 { color: #333; }
p { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
hr { margin: 20px 0; }
</style>
