<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    header('Location: /smartprozen/');
    exit;
}

// Log the logout
$user = get_logged_in_user();
if ($user) {
    error_log("User logout: {$user['email']} from IP: " . $_SERVER['REMOTE_ADDR']);
}

// Perform logout
logout_user();

// Clear remember me cookie if exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
}

// Redirect to login page with success message
header('Location: /smartprozen/auth/login.php?logout=success');
exit;
?>