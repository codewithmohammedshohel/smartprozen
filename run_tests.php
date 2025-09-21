<?php
/**
 * System Tests Component for Master Setup
 */

echo "<div class='progress mb-3'><div class='progress-bar' role='progressbar' style='width: 0%' id='test-progress'></div></div>";

$tests_passed = 0;
$tests_failed = 0;
$tests_total = 0;

function runTest($name, $callback) {
    global $tests_passed, $tests_failed, $tests_total;
    $tests_total++;
    
    echo "<div class='test-section'>";
    echo "<h6><i class='bi bi-play-circle'></i> $name</h6>";
    
    try {
        $result = $callback();
        if ($result) {
            echo "<p class='text-success'>✅ PASSED</p>";
            $tests_passed++;
            echo "</div>";
            return true;
        } else {
            echo "<p class='text-danger'>❌ FAILED</p>";
            $tests_failed++;
            echo "</div>";
            return false;
        }
    } catch (Exception $e) {
        echo "<p class='text-danger'>❌ ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
        $tests_failed++;
        echo "</div>";
        return false;
    }
}

function updateTestProgress($percent) {
    echo "<script>document.getElementById('test-progress').style.width = '$percent%'; document.getElementById('test-progress').textContent = '$percent%';</script>";
    ob_flush();
    flush();
}

// Test 1: Configuration
runTest("Configuration Loading", function() {
    if (!file_exists('config.php')) {
        throw new Exception("config.php not found");
    }
    
    require_once 'config.php';
    
    if (!defined('SITE_URL') || !defined('DB_HOST') || !defined('DB_NAME')) {
        throw new Exception("Required constants not defined");
    }
    
    return true;
});

updateTestProgress(10);

// Test 2: Database Connection
runTest("Database Connection", function() {
    require_once 'core/db.php';
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    return true;
});

updateTestProgress(20);

// Test 3: Core Functions
runTest("Core Functions Loading", function() {
    require_once 'core/functions.php';
    
    if (!function_exists('is_user_logged_in') || !function_exists('get_setting')) {
        throw new Exception("Required functions not loaded");
    }
    
    return true;
});

updateTestProgress(30);

// Test 4: Database Tables
runTest("Database Tables", function() {
    global $conn;
    
    $required_tables = [
        'settings', 'admin_users', 'users', 'pages', 'page_sections',
        'products', 'product_categories', 'orders', 'order_items',
        'menus', 'testimonials', 'modules', 'coupons', 'payment_gateways'
    ];
    
    foreach ($required_tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows == 0) {
            throw new Exception("Table '$table' not found");
        }
    }
    
    return true;
});

updateTestProgress(40);

// Test 5: File Permissions
runTest("File Permissions", function() {
    $directories = ['uploads', 'uploads/logos', 'uploads/media', 'uploads/products'];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            throw new Exception("Directory '$dir' not found");
        }
        
        if (!is_writable($dir)) {
            throw new Exception("Directory '$dir' is not writable");
        }
    }
    
    return true;
});

updateTestProgress(50);

// Test 6: URL Routing
runTest("URL Routing", function() {
    if (!file_exists('.htaccess')) {
        throw new Exception(".htaccess file not found");
    }
    
    $htaccess_content = file_get_contents('.htaccess');
    if (strpos($htaccess_content, 'RewriteEngine On') === false) {
        throw new Exception("URL rewriting not enabled");
    }
    
    return true;
});

updateTestProgress(60);

// Test 7: Admin Panel Access
runTest("Admin Panel Files", function() {
    $admin_files = [
        'admin/login.php', 'admin/dashboard.php', 'admin/manage_products.php',
        'admin/manage_pages.php', 'admin/settings.php'
    ];
    
    foreach ($admin_files as $file) {
        if (!file_exists($file)) {
            throw new Exception("Admin file '$file' not found");
        }
    }
    
    return true;
});

updateTestProgress(70);

// Test 8: User Authentication
runTest("User Authentication System", function() {
    $auth_files = [
        'auth/login.php', 'auth/register.php', 'auth/logout.php',
        'user/dashboard.php', 'user/profile.php'
    ];
    
    foreach ($auth_files as $file) {
        if (!file_exists($file)) {
            throw new Exception("Auth file '$file' not found");
        }
    }
    
    return true;
});

updateTestProgress(80);

// Test 9: Cart System
runTest("Shopping Cart System", function() {
    $cart_files = [
        'cart/index.php', 'cart/add_to_cart.php', 'cart/update_cart.php',
        'cart/checkout.php', 'cart/place_order.php'
    ];
    
    foreach ($cart_files as $file) {
        if (!file_exists($file)) {
            throw new Exception("Cart file '$file' not found");
        }
    }
    
    return true;
});

updateTestProgress(90);

// Test 10: API Endpoints
runTest("API Endpoints", function() {
    $api_files = [
        'api/sales_data.php', 'api/wishlist_handler.php', 'api/review_handler.php'
    ];
    
    foreach ($api_files as $file) {
        if (!file_exists($file)) {
            throw new Exception("API file '$file' not found");
        }
    }
    
    return true;
});

updateTestProgress(100);

// Test Results Summary
echo "<div class='test-section " . ($tests_failed == 0 ? 'test-pass' : 'test-fail') . "'>";
echo "<h3><i class='bi bi-" . ($tests_failed == 0 ? 'check-circle' : 'x-circle') . "'></i> Test Results Summary</h3>";
echo "<div class='row'>";
echo "<div class='col-md-4'>";
echo "<h5>Total Tests: $tests_total</h5>";
echo "</div>";
echo "<div class='col-md-4'>";
echo "<h5 class='text-success'>Passed: $tests_passed</h5>";
echo "</div>";
echo "<div class='col-md-4'>";
echo "<h5 class='text-danger'>Failed: $tests_failed</h5>";
echo "</div>";
echo "</div>";

if ($tests_failed == 0) {
    echo "<div class='alert alert-success'>";
    echo "<h5><i class='bi bi-check-circle'></i> All Tests Passed!</h5>";
    echo "<p>Your SmartProZen system is working correctly and ready for use.</p>";
    echo "</div>";
} else {
    echo "<div class='alert alert-danger'>";
    echo "<h5><i class='bi bi-exclamation-triangle'></i> Some Tests Failed</h5>";
    echo "<p>Please review the failed tests above and fix any issues before using the system.</p>";
    echo "</div>";
}

echo "<div class='mt-3'>";
echo "<a href='" . SITE_URL . "/admin/login.php' class='btn btn-primary'>";
echo "<i class='bi bi-shield-lock'></i> Go to Admin Panel";
echo "</a>";
echo "<a href='" . SITE_URL . "' class='btn btn-success ms-2'>";
echo "<i class='bi bi-house'></i> View Homepage";
echo "</a>";
echo "</div>";

echo "</div>";
?>

