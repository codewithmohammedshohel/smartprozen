<?php
/**
 * SmartProZen Deployment Test Script
 * This script tests the deployment environment and configuration
 */

// Test 1: PHP Version Check
echo "<h2>🚀 SmartProZen Deployment Test</h2>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px;'>";

echo "<h3>1. PHP Version Check</h3>";
$php_version = phpversion();
$required_version = '7.4.0';
if (version_compare($php_version, $required_version, '>=')) {
    echo "<p style='color: green;'>✅ PHP Version: $php_version (Required: $required_version+)</p>";
} else {
    echo "<p style='color: red;'>❌ PHP Version: $php_version (Required: $required_version+)</p>";
}

// Test 2: Required Extensions
echo "<h3>2. Required PHP Extensions</h3>";
$required_extensions = ['mysqli', 'gd', 'curl', 'json', 'pdo', 'openssl', 'mbstring'];
$all_extensions_ok = true;

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✅ $ext extension loaded</p>";
    } else {
        echo "<p style='color: red;'>❌ $ext extension not loaded</p>";
        $all_extensions_ok = false;
    }
}

// Test 3: File Permissions
echo "<h3>3. File Permissions</h3>";
$directories_to_check = [
    'uploads/',
    'uploads/media/',
    'uploads/files/',
    'uploads/logos/',
    'logs/'
];

foreach ($directories_to_check as $dir) {
    if (file_exists($dir)) {
        if (is_writable($dir)) {
            echo "<p style='color: green;'>✅ $dir is writable</p>";
        } else {
            echo "<p style='color: red;'>❌ $dir is not writable</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ $dir does not exist (will be created automatically)</p>";
    }
}

// Test 4: Configuration File
echo "<h3>4. Configuration File</h3>";
if (file_exists('config.php')) {
    echo "<p style='color: green;'>✅ config.php exists</p>";
    
    // Test database connection
    try {
        require_once 'config.php';
        require_once 'core/db.php';
        echo "<p style='color: green;'>✅ Database connection successful</p>";
        
        // Test if tables exist
        $tables = ['users', 'products', 'orders', 'pages', 'settings'];
        $tables_exist = true;
        
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                echo "<p style='color: green;'>✅ Table '$table' exists</p>";
            } else {
                echo "<p style='color: red;'>❌ Table '$table' does not exist</p>";
                $tables_exist = false;
            }
        }
        
        if ($tables_exist) {
            echo "<p style='color: green;'>✅ Database schema is properly set up</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Database schema needs to be imported</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ config.php does not exist</p>";
}

// Test 5: Environment Detection
echo "<h3>5. Environment Detection</h3>";
$server_name = $_SERVER['SERVER_NAME'] ?? '';
$http_host = $_SERVER['HTTP_HOST'] ?? '';

if (strpos($server_name, 'localhost') !== false || 
    strpos($http_host, 'localhost') !== false || 
    strpos($http_host, '127.0.0.1') !== false ||
    strpos($http_host, '192.168.') !== false) {
    echo "<p style='color: blue;'>🏠 Local Environment Detected (XAMPP/WAMP)</p>";
    echo "<p style='color: green;'>✅ Auto-configuration will use local settings</p>";
} elseif (strpos($server_name, '.') !== false && 
          !strpos($server_name, 'localhost') && 
          !strpos($server_name, '127.0.0.1')) {
    echo "<p style='color: blue;'>🌐 Production Environment Detected (cPanel/Shared Hosting)</p>";
    echo "<p style='color: orange;'>⚠️ Please update config.php with your production database credentials</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Environment could not be detected</p>";
}

// Test 6: Sample Data
echo "<h3>6. Sample Data Check</h3>";
if (file_exists('sample_data.sql')) {
    echo "<p style='color: green;'>✅ sample_data.sql exists</p>";
    echo "<p style='color: blue;'>💡 Import this file to add sample products, pages, and content</p>";
} else {
    echo "<p style='color: orange;'>⚠️ sample_data.sql not found</p>";
}

// Test 7: Admin Panel Access
echo "<h3>7. Admin Panel Access</h3>";
if (file_exists('admin/login.php')) {
    echo "<p style='color: green;'>✅ Admin panel files exist</p>";
    echo "<p style='color: blue;'>💡 Access admin panel at: <a href='admin/login.php'>admin/login.php</a></p>";
} else {
    echo "<p style='color: red;'>❌ Admin panel files missing</p>";
}

// Test 8: Frontend Access
echo "<h3>8. Frontend Access</h3>";
if (file_exists('index.php')) {
    echo "<p style='color: green;'>✅ Frontend files exist</p>";
    echo "<p style='color: blue;'>💡 Access frontend at: <a href='index.php'>index.php</a></p>";
} else {
    echo "<p style='color: red;'>❌ Frontend files missing</p>";
}

// Test 9: Setup Wizard
echo "<h3>9. Setup Wizard</h3>";
if (file_exists('setup.php')) {
    echo "<p style='color: green;'>✅ Setup wizard exists</p>";
    echo "<p style='color: blue;'>💡 Run setup wizard at: <a href='setup.php'>setup.php</a></p>";
} else {
    echo "<p style='color: red;'>❌ Setup wizard missing</p>";
}

// Test 10: Security Check
echo "<h3>10. Security Check</h3>";
if (file_exists('.htaccess')) {
    echo "<p style='color: green;'>✅ .htaccess file exists</p>";
} else {
    echo "<p style='color: orange;'>⚠️ .htaccess file not found (optional for some servers)</p>";
}

// Summary
echo "<h3>📋 Deployment Summary</h3>";
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";

if ($all_extensions_ok && file_exists('config.php')) {
    echo "<p style='color: green; font-weight: bold;'>🎉 Deployment looks good! Your SmartProZen installation is ready.</p>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li>Run the <a href='setup.php'>setup wizard</a> to create your admin account</li>";
    echo "<li>Import <a href='sample_data.sql'>sample data</a> for demo content</li>";
    echo "<li>Access the <a href='admin/login.php'>admin panel</a> to customize your site</li>";
    echo "<li>Visit the <a href='index.php'>frontend</a> to see your site</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red; font-weight: bold;'>⚠️ Some issues detected. Please fix them before proceeding.</p>";
    echo "<p><strong>Common Solutions:</strong></p>";
    echo "<ul>";
    echo "<li>Install missing PHP extensions</li>";
    echo "<li>Create config.php from config.php.template</li>";
    echo "<li>Set proper file permissions (chmod 755)</li>";
    echo "<li>Import the database schema</li>";
    echo "</ul>";
}

echo "</div>";

echo "<h3>📚 Documentation</h3>";
echo "<p>📖 <a href='README.md'>README.md</a> - Complete documentation</p>";
echo "<p>🚀 <a href='DEPLOYMENT_GUIDE.md'>DEPLOYMENT_GUIDE.md</a> - Detailed deployment guide</p>";

echo "</div>";
?>

<style>
body {
    margin: 0;
    padding: 20px;
    background-color: #f5f5f5;
}
h2 {
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}
h3 {
    color: #555;
    margin-top: 30px;
}
a {
    color: #007bff;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>
