<?php
require_once __DIR__ . '/../config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    // In production, log this error instead of displaying it
    error_log("Database Connection Failed: " . $conn->connect_error); // Log the error
    die("A critical database error occurred. Please try again later."); // Generic message for users
}
$conn->set_charset("utf8mb4");
?>