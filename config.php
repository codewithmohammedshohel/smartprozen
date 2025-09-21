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

// Database configuration for XAMPP
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smartprozen_db');

// Other configurations can go here
// For example, site URL, default language, etc.
define('SITE_URL', 'http://localhost/smartprozen');
define('DEFAULT_LANG', 'en');

ini_set('display_errors', 0); // Disable display of errors in production
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', __DIR__ . '/../logs/php-error.log'); // Set error log file path

// Define DEBUG constant
define('DEBUG', true); // Set to false in production

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

// Error reporting
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// IMPORTANT: In a production environment, set display_errors to 0 for security.
// ini_set('display_errors', 0);
?>