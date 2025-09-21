<?php
/**
 * Quick Database Creation Script
 * Use this if you want to create just the database without full setup
 */

require_once 'config.php';

echo "<h2>Creating SmartProZen Database...</h2>";

try {
    // Connect to MySQL without selecting a database
    $conn = new mysqli(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✓ Database '" . DB_NAME . "' created successfully!</p>";
        echo "<p>Now you can:</p>";
        echo "<ol>";
        echo "<li><a href='setup_cms.php'>Run the complete setup</a> to create tables and sample data</li>";
        echo "<li>Or <a href='admin/login.php'>Login to admin panel</a> (after running setup)</li>";
        echo "</ol>";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config.php</p>";
}

$conn->close();
?>

<style>
body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
h2 { color: #333; }
p { line-height: 1.6; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
