<?php
/**
 * SmartProZen CMS Setup Script
 * This script sets up the complete CMS with database and sample data
 */

require_once 'config.php';

// Check if already installed
$check_file = __DIR__ . '/.installed';
if (file_exists($check_file)) {
    die('<h2>SmartProZen CMS is already installed!</h2><p>If you want to reinstall, delete the .installed file and run this script again.</p>');
}

echo "<h1>SmartProZen CMS Setup</h1>";
echo "<p>Setting up your complete CMS system...</p>";

try {
    // Create database connection
    $conn = new mysqli(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if (!$conn->query($sql)) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    echo "<p>✓ Database created successfully</p>";
    
    // Select the database
    $conn->select_db(DB_NAME);
    
    // Read and execute schema
    $schema = file_get_contents(__DIR__ . '/database_schema.sql');
    if (!$schema) {
        throw new Exception("Could not read database schema file");
    }
    
    // Split schema into individual queries
    $queries = explode(';', $schema);
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query) && !preg_match('/^--/', $query)) {
            if (!$conn->query($query)) {
                echo "<p>Warning: Query failed: " . $conn->error . "</p>";
            }
        }
    }
    
    echo "<p>✓ Database schema created successfully</p>";
    
    // Insert sample data
    $sample_files = [
        'sample_data_part1.sql',
        'sample_data_part2.sql', 
        'sample_data_part3.sql',
        'preloaded_pages.sql'
    ];
    
    foreach ($sample_files as $file) {
        $file_path = __DIR__ . '/' . $file;
        if (file_exists($file_path)) {
            $sample_data = file_get_contents($file_path);
            if ($sample_data) {
                $queries = explode(';', $sample_data);
                foreach ($queries as $query) {
                    $query = trim($query);
                    if (!empty($query) && !preg_match('/^--/', $query)) {
                        if (!$conn->query($query)) {
                            echo "<p>Warning: Sample data query failed: " . $conn->error . "</p>";
                        }
                    }
                }
                echo "<p>✓ Sample data from $file loaded successfully</p>";
            }
        }
    }
    
    // Create uploads directories
    $upload_dirs = [
        'uploads',
        'uploads/logos',
        'uploads/media',
        'uploads/files',
        'uploads/sections',
        'uploads/products',
        'uploads/categories',
        'uploads/avatars',
        'uploads/templates'
    ];
    
    foreach ($upload_dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            echo "<p>✓ Created directory: $dir</p>";
        }
    }
    
    // Create .htaccess for uploads security
    $htaccess_content = "Options -Indexes\nDeny from all\n<Files ~ \"\\.(jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|zip|rar)$\">\nAllow from all\n</Files>";
    file_put_contents('uploads/.htaccess', $htaccess_content);
    echo "<p>✓ Created uploads security file</p>";
    
    // Create admin configuration file
    $admin_config = [
        'installed' => true,
        'install_date' => date('Y-m-d H:i:s'),
        'version' => '1.0.0',
        'admin_created' => true
    ];
    
    file_put_contents('.installed', json_encode($admin_config, JSON_PRETTY_PRINT));
    echo "<p>✓ Installation configuration saved</p>";
    
    // Display success message
    echo "<h2>🎉 SmartProZen CMS Setup Complete!</h2>";
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>Admin Login Details:</h3>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>Admin URL:</strong> <a href='admin/login.php'>" . SITE_URL . "/admin/login.php</a></p>";
    echo "</div>";
    
    echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>What's Included:</h3>";
    echo "<ul>";
    echo "<li>✓ Complete database schema with all tables</li>";
    echo "<li>✓ Sample products, categories, and users</li>";
    echo "<li>✓ Preloaded pages with customizable sections</li>";
    echo "<li>✓ Customizable header and footer components</li>";
    echo "<li>✓ Theme customization system</li>";
    echo "<li>✓ Admin panel for complete CMS management</li>";
    echo "<li>✓ E-commerce functionality (products, orders, cart)</li>";
    echo "<li>✓ Content management (pages, sections, templates)</li>";
    echo "<li>✓ User management and authentication</li>";
    echo "<li>✓ SEO and settings management</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Login to admin panel and customize your site settings</li>";
    echo "<li>Upload your logo and customize theme colors</li>";
    echo "<li>Add your products and categories</li>";
    echo "<li>Customize pages and sections using the page builder</li>";
    echo "<li>Configure payment gateways and shipping settings</li>";
    echo "<li>Test the complete functionality</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<p><strong>Important:</strong> Delete this setup file (setup_cms.php) after installation for security.</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Setup Failed!</h2>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config.php and try again.</p>";
}

$conn->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}

h1, h2, h3 {
    color: #333;
}

p {
    margin: 10px 0;
}

ul, ol {
    margin: 10px 0;
    padding-left: 20px;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
