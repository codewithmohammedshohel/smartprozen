<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

// Check if admin is logged in
if (!is_admin_logged_in()) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Log the logout
$admin = get_current_admin();
if ($admin) {
    error_log("Admin logout: {$admin['username']} from IP: " . $_SERVER['REMOTE_ADDR']);
}

// Perform logout
logout_admin();

// Redirect to admin login page with success message
header('Location: /smartprozen/admin/login.php?logout=success');
exit;
?>
