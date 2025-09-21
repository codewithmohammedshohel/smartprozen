<?php
// Set a higher time limit for potentially long report generation
set_time_limit(300);

require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

// Authenticate and authorize admin
if (!is_admin_logged_in() || !has_permission('manage_reports')) {
    die("Access Denied.");
}

// Get POST data
$report_type = $_POST['report_type'] ?? 'sales';
$format = $_POST['format'] ?? 'pdf';
$start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

// --- Build Date Range WHERE clause for SQL queries ---
$date