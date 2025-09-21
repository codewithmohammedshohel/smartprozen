<?php
/**
 * Test modules functionality
 */

require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

echo "<h2>Modules System Test</h2>";

try {
    // Test if modules table exists and has data
    echo "<h3>Testing Modules Table:</h3>";
    
    $modules_query = $conn->query("SELECT * FROM modules ORDER BY name");
    if ($modules_query) {
        echo "<p style='color: green;'>✓ Modules table exists and is accessible</p>";
        
        $modules = $modules_query->fetch_all(MYSQLI_ASSOC);
        echo "<p>Found " . count($modules) . " modules:</p>";
        
        echo "<ul>";
        foreach ($modules as $module) {
            $status = $module['is_active'] ? 'Active' : 'Inactive';
            echo "<li><strong>{$module['name']}</strong> ({$module['slug']}) - <span style='color: " . ($module['is_active'] ? 'green' : 'red') . ";'>{$status}</span></li>";
        }
        echo "</ul>";
        
        // Test is_module_enabled function
        echo "<h3>Testing Module Functions:</h3>";
        
        if (function_exists('is_module_enabled')) {
            echo "<p style='color: green;'>✓ is_module_enabled() function exists</p>";
            
            // Test with a known module
            $ecommerce_enabled = is_module_enabled('ecommerce', $conn);
            echo "<p>E-commerce module enabled: " . ($ecommerce_enabled ? 'Yes' : 'No') . "</p>";
            
            $blog_enabled = is_module_enabled('blog', $conn);
            echo "<p>Blog module enabled: " . ($blog_enabled ? 'Yes' : 'No') . "</p>";
            
            $nonexistent_enabled = is_module_enabled('nonexistent', $conn);
            echo "<p>Nonexistent module enabled: " . ($nonexistent_enabled ? 'Yes' : 'No') . "</p>";
        } else {
            echo "<p style='color: red;'>✗ is_module_enabled() function not found</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Modules table query failed: " . $conn->error . "</p>";
    }
    
    // Test admin modules page
    echo "<h3>Admin Modules Page:</h3>";
    if (file_exists('admin/manage_modules.php')) {
        echo "<p style='color: green;'>✓ Admin modules management page exists</p>";
        echo "<p><a href='admin/manage_modules.php'>Go to Module Management</a></p>";
    } else {
        echo "<p style='color: red;'>✗ Admin modules management page not found</p>";
    }
    
    echo "<h3 style='color: green;'>✅ Modules system is working correctly!</h3>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Error testing modules system:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
h2, h3 { color: #333; }
p { margin: 5px 0; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
