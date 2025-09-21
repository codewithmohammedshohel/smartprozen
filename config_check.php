<?php
/**
 * Configuration Check Component for Master Setup
 */

echo "<div class='test-section test-info'>";
echo "<h4><i class='bi bi-gear'></i> Configuration Validation</h4>";
echo "<p>This validates all system configurations and settings.</p>";
echo "</div>";

$checks_passed = 0;
$checks_failed = 0;
$checks_total = 0;

function runCheck($name, $callback) {
    global $checks_passed, $checks_failed, $checks_total;
    $checks_total++;
    
    echo "<div class='test-section'>";
    echo "<h6><i class='bi bi-check-circle'></i> $name</h6>";
    
    try {
        $result = $callback();
        if ($result['status']) {
            echo "<p class='text-success'>✅ " . $result['message'] . "</p>";
            $checks_passed++;
        } else {
            echo "<p class='text-danger'>❌ " . $result['message'] . "</p>";
            $checks_failed++;
        }
        
        if (isset($result['details'])) {
            echo "<div class='code-block'><small>" . $result['details'] . "</small></div>";
        }
    } catch (Exception $e) {
        echo "<p class='text-danger'>❌ ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
        $checks_failed++;
    }
    echo "</div>";
}

// Check 1: Configuration File
runCheck("Configuration File", function() {
    if (!file_exists('config.php')) {
        return ['status' => false, 'message' => 'config.php file not found'];
    }
    
    $content = file_get_contents('config.php');
    if (strpos($content, 'SITE_URL') === false) {
        return ['status' => false, 'message' => 'SITE_URL constant not found in config.php'];
    }
    
    return ['status' => true, 'message' => 'Configuration file exists and contains required constants'];
});

// Check 2: Environment Detection
runCheck("Environment Detection", function() {
    require_once 'config.php';
    
    if (!defined('ENVIRONMENT')) {
        return ['status' => false, 'message' => 'ENVIRONMENT constant not defined'];
    }
    
    if (!in_array(ENVIRONMENT, ['local', 'production'])) {
        return ['status' => false, 'message' => 'Invalid ENVIRONMENT value: ' . ENVIRONMENT];
    }
    
    $details = "Environment: " . ENVIRONMENT . "\n";
    $details .= "Is Local: " . (defined('IS_LOCAL') ? (IS_LOCAL ? 'Yes' : 'No') : 'Not defined') . "\n";
    $details .= "Is Production: " . (defined('IS_PRODUCTION') ? (IS_PRODUCTION ? 'Yes' : 'No') : 'Not defined');
    
    return ['status' => true, 'message' => 'Environment detection working correctly', 'details' => $details];
});

// Check 3: Database Configuration
runCheck("Database Configuration", function() {
    require_once 'config.php';
    
    $required_constants = ['DB_HOST', 'DB_PORT', 'DB_USER', 'DB_PASS', 'DB_NAME'];
    $missing = [];
    
    foreach ($required_constants as $constant) {
        if (!defined($constant)) {
            $missing[] = $constant;
        }
    }
    
    if (!empty($missing)) {
        return ['status' => false, 'message' => 'Missing database constants: ' . implode(', ', $missing)];
    }
    
    $details = "Host: " . DB_HOST . ":" . DB_PORT . "\n";
    $details .= "Database: " . DB_NAME . "\n";
    $details .= "User: " . DB_USER;
    
    return ['status' => true, 'message' => 'Database configuration complete', 'details' => $details];
});

// Check 4: Database Connection
runCheck("Database Connection", function() {
    require_once 'core/db.php';
    
    if ($conn->connect_error) {
        return ['status' => false, 'message' => 'Database connection failed: ' . $conn->connect_error];
    }
    
    $details = "MySQL Version: " . $conn->server_info . "\n";
    $details .= "Connection ID: " . $conn->thread_id . "\n";
    $details .= "Character Set: " . $conn->character_set_name();
    
    return ['status' => true, 'message' => 'Database connection successful', 'details' => $details];
});

// Check 5: Site URL Configuration
runCheck("Site URL Configuration", function() {
    require_once 'config.php';
    
    if (!defined('SITE_URL') || empty(SITE_URL)) {
        return ['status' => false, 'message' => 'SITE_URL not defined or empty'];
    }
    
    // Check if URL is properly formatted
    if (!filter_var(SITE_URL, FILTER_VALIDATE_URL) && !str_starts_with(SITE_URL, '/')) {
        return ['status' => false, 'message' => 'SITE_URL format is invalid: ' . SITE_URL];
    }
    
    $details = "Site URL: " . SITE_URL . "\n";
    $details .= "HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "\n";
    $details .= "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown');
    
    return ['status' => true, 'message' => 'Site URL configuration valid', 'details' => $details];
});

// Check 6: Directory Permissions
runCheck("Directory Permissions", function() {
    $required_dirs = ['uploads', 'uploads/logos', 'uploads/media', 'uploads/products', 'logs'];
    $issues = [];
    
    foreach ($required_dirs as $dir) {
        if (!is_dir($dir)) {
            $issues[] = "$dir: Directory does not exist";
        } elseif (!is_writable($dir)) {
            $issues[] = "$dir: Not writable";
        }
    }
    
    if (!empty($issues)) {
        return ['status' => false, 'message' => 'Directory permission issues found', 'details' => implode("\n", $issues)];
    }
    
    return ['status' => true, 'message' => 'All required directories have proper permissions'];
});

// Check 7: Core Files
runCheck("Core Files", function() {
    $core_files = [
        'core/db.php' => 'Database connection',
        'core/functions.php' => 'Core functions',
        'includes/header.php' => 'Header template',
        'includes/footer.php' => 'Footer template',
        'index.php' => 'Homepage'
    ];
    
    $missing = [];
    foreach ($core_files as $file => $description) {
        if (!file_exists($file)) {
            $missing[] = "$file ($description)";
        }
    }
    
    if (!empty($missing)) {
        return ['status' => false, 'message' => 'Missing core files', 'details' => implode("\n", $missing)];
    }
    
    return ['status' => true, 'message' => 'All core files present'];
});

// Check 8: Admin Panel Files
runCheck("Admin Panel Files", function() {
    $admin_files = [
        'admin/login.php' => 'Admin login',
        'admin/dashboard.php' => 'Admin dashboard',
        'admin/manage_products.php' => 'Product management',
        'admin/manage_pages.php' => 'Page management',
        'admin/settings.php' => 'System settings'
    ];
    
    $missing = [];
    foreach ($admin_files as $file => $description) {
        if (!file_exists($file)) {
            $missing[] = "$file ($description)";
        }
    }
    
    if (!empty($missing)) {
        return ['status' => false, 'message' => 'Missing admin panel files', 'details' => implode("\n", $missing)];
    }
    
    return ['status' => true, 'message' => 'All admin panel files present'];
});

// Check 9: PHP Extensions
runCheck("PHP Extensions", function() {
    $required_extensions = ['mysqli', 'json', 'session', 'curl', 'gd', 'mbstring'];
    $missing = [];
    
    foreach ($required_extensions as $ext) {
        if (!extension_loaded($ext)) {
            $missing[] = $ext;
        }
    }
    
    if (!empty($missing)) {
        return ['status' => false, 'message' => 'Missing PHP extensions: ' . implode(', ', $missing)];
    }
    
    $details = "All required extensions loaded:\n";
    $details .= implode(', ', $required_extensions);
    
    return ['status' => true, 'message' => 'All required PHP extensions loaded', 'details' => $details];
});

// Check 10: Security Settings
runCheck("Security Settings", function() {
    $security_checks = [];
    
    // Check if display_errors is off in production
    if (defined('IS_PRODUCTION') && IS_PRODUCTION && ini_get('display_errors')) {
        $security_checks[] = "display_errors should be off in production";
    }
    
    // Check session security
    if (ini_get('session.cookie_httponly') != '1') {
        $security_checks[] = "session.cookie_httponly should be enabled";
    }
    
    // Check if debug mode is off in production
    if (defined('IS_PRODUCTION') && IS_PRODUCTION && defined('DEBUG') && DEBUG) {
        $security_checks[] = "DEBUG should be false in production";
    }
    
    if (!empty($security_checks)) {
        return ['status' => false, 'message' => 'Security issues found', 'details' => implode("\n", $security_checks)];
    }
    
    $details = "Display Errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "\n";
    $details .= "Log Errors: " . (ini_get('log_errors') ? 'On' : 'Off') . "\n";
    $details .= "Session HttpOnly: " . (ini_get('session.cookie_httponly') ? 'On' : 'Off') . "\n";
    $details .= "Debug Mode: " . (defined('DEBUG') ? (DEBUG ? 'On' : 'Off') : 'Not defined');
    
    return ['status' => true, 'message' => 'Security settings are properly configured', 'details' => $details];
});

// Configuration Summary
echo "<div class='test-section " . ($checks_failed == 0 ? 'test-pass' : 'test-fail') . "'>";
echo "<h3><i class='bi bi-" . ($checks_failed == 0 ? 'check-circle' : 'x-circle') . "'></i> Configuration Check Summary</h3>";
echo "<div class='row'>";
echo "<div class='col-md-4'>";
echo "<h5>Total Checks: $checks_total</h5>";
echo "</div>";
echo "<div class='col-md-4'>";
echo "<h5 class='text-success'>Passed: $checks_passed</h5>";
echo "</div>";
echo "<div class='col-md-4'>";
echo "<h5 class='text-danger'>Failed: $checks_failed</h5>";
echo "</div>";
echo "</div>";

if ($checks_failed == 0) {
    echo "<div class='alert alert-success'>";
    echo "<h5><i class='bi bi-check-circle'></i> All Configuration Checks Passed!</h5>";
    echo "<p>Your SmartProZen system configuration is correct and ready for use.</p>";
    echo "</div>";
} else {
    echo "<div class='alert alert-danger'>";
    echo "<h5><i class='bi bi-exclamation-triangle'></i> Configuration Issues Found</h5>";
    echo "<p>Please review the failed checks above and fix any issues before using the system.</p>";
    echo "</div>";
}

echo "<div class='mt-3'>";
echo "<a href='" . SITE_URL . "/admin/login.php' class='btn btn-primary'>";
echo "<i class='bi bi-shield-lock'></i> Go to Admin Panel";
echo "</a>";
echo "<a href='?action=setup' class='btn btn-warning ms-2'>";
echo "<i class='bi bi-download'></i> Run Setup";
echo "</a>";
echo "</div>";

echo "</div>";
?>

