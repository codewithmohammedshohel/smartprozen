<?php
// Session Configuration for Security - MUST be at the very top before session_start()
ini_set('session.gc_maxlifetime', 1440); // 24 minutes (default is 1440 seconds = 24 minutes)
ini_set('session.cookie_lifetime', 0); // Session cookie expires when browser closes
ini_set('session.use_strict_mode', 1); // Prevent session fixation
ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookie
// ini_set('session.cookie_secure', 1); // Uncomment if using HTTPS

// Start session after ini_set calls
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Checks for an available MySQL port to avoid manual configuration changes.
 *
 * @return int The open port number (3307 or 3306).
 */
function getAvailableMySqlPort() {
    // Array of ports to check, with your current port (3307) as the priority.
    $ports = [3307, 3306];
    $host = '127.0.0.1';
    
    foreach ($ports as $port) {
        // Use a very short timeout (0.1 seconds) to avoid slowing down the page.
        // The '@' suppresses connection error warnings, as we expect them to fail.
        $connection = @fsockopen($host, $port, $errno, $errstr, 0.1);

        // If the connection succeeds, we've found our port.
        if (is_resource($connection)) {
            fclose($connection); // Close the test connection immediately.
            return $port;
        }
    }

    // If neither port is open, return the default as a fallback.
    return 3306;
}

// Auto-detect environment and configure accordingly
function detectEnvironment() {
    $server_name = $_SERVER['SERVER_NAME'] ?? '';
    $http_host = $_SERVER['HTTP_HOST'] ?? '';
    
    // Check if we're on localhost (XAMPP/WAMP)
    if (strpos($server_name, 'localhost') !== false || 
        strpos($http_host, 'localhost') !== false || 
        strpos($http_host, '127.0.0.1') !== false ||
        strpos($http_host, '192.168.') !== false) {
        return 'local';
    }
    
    // Check if we're on a live domain (cPanel/shared hosting)
    if (strpos($server_name, '.') !== false && 
        !strpos($server_name, 'localhost') && 
        !strpos($server_name, '127.0.0.1')) {
        return 'production';
    }
    
    return 'local'; // Default to local
}

$environment = detectEnvironment();

// Database configuration based on environment
if ($environment === 'local') {
    // XAMPP/WAMP Local Development Configuration
    define('DB_HOST', 'localhost');
    define('DB_PORT', getAvailableMySqlPort()); // This now auto-detects the port
    define('DB_USER', 'root');
    define('DB_PASS', 'admin123');
    define('DB_NAME', 'smartprozen_db');
    
    // Local site URL
    define('SITE_URL', 'http://localhost/smartprozen');
    
    // Enable debug mode for local development
    define('DEBUG', true);
    
} else {
    // Production/cPanel Configuration
    // These should be updated for your specific hosting environment
    define('DB_HOST', 'localhost'); // Usually localhost on shared hosting
    define('DB_PORT', '3306');
    define('DB_USER', 'your_cpanel_db_user'); // Update this
    define('DB_PASS', 'your_cpanel_db_password'); // Update this
    define('DB_NAME', 'your_cpanel_db_name'); // Update this
    
    // Production site URL - Update this to your domain
    define('SITE_URL', 'https://yourdomain.com'); // Update this
    
    // Disable debug mode for production
    define('DEBUG', false);
}

// Other configurations
define('DEFAULT_LANG', 'en');
define('TIMEZONE', 'UTC');

ini_set('display_errors', 0); // Disable display of errors in production
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', __DIR__ . '/../logs/php-error.log'); // Set error log file path

if (DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
}

// Custom Error Handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return false;
    }
    $error_message = "[PHP Error] [" . date("Y-m-d H:i:s") . "] Error $errno: $errstr in $errfile on line $errline\n";
    error_log($error_message);

    if (DEBUG) {
        echo "<div style=\"border: 1px solid red; padding: 10px; margin: 10px 0; background-color: #ffecec;\">";
        echo "<strong>Error:</strong> $errstr<br>";
        echo "<strong>File:</strong> $errfile<br>";
        echo "<strong>Line:</strong> $errline<br>";
        echo "</div>";
    } else {
        // In production, show a generic error message or redirect to an error page
        // For now, we'll just show a generic message.
        // header('Location: /error.php'); exit;
        echo "<div style=\"border: 1px solid red; padding: 10px; margin: 10px 0; background-color: #ffecec;\">An unexpected error occurred. Please try again later.</div>";
    }
    return true;
}

// Custom Exception Handler
function customExceptionHandler($exception) {
    $error_message = "[PHP Exception] [" . date("Y-m-d H:i:s") . "] Uncaught exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    error_log($error_message);

    if (DEBUG) {
        echo "<div style=\"border: 1px solid red; padding: 10px; margin: 10px 0; background-color: #ffecec;\">";
        echo "<strong>Uncaught Exception:</strong> " . $exception->getMessage() . "<br>";
        echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
        echo "<strong>Line:</strong> " . $exception->getLine() . "<br>";
        echo "</div>";
    } else {
        echo "<div style=\"border: 1px solid red; padding: 10px; margin: 10px 0; background-color: #ffecec;\">An unexpected error occurred. Please try again later.</div>";
    }
    exit();
}

set_error_handler("customErrorHandler");
set_exception_handler("customExceptionHandler");
?>