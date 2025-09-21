<?php
/**
 * Debug Information Component for Master Setup
 */

echo "<div class='test-section test-info'>";
echo "<h4><i class='bi bi-info-circle'></i> System Debug Information</h4>";
echo "<p>This information helps diagnose issues and verify system configuration.</p>";
echo "</div>";

// PHP Configuration
echo "<div class='test-section test-info'>";
echo "<h5><i class='bi bi-code'></i> PHP Configuration</h5>";
echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</p>";
echo "<p><strong>Script Name:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "</p>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<p><strong>Memory Limit:</strong> " . ini_get('memory_limit') . "</p>";
echo "<p><strong>Max Execution Time:</strong> " . ini_get('max_execution_time') . " seconds</p>";
echo "<p><strong>Upload Max Size:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>Post Max Size:</strong> " . ini_get('post_max_size') . "</p>";
echo "</div>";
echo "</div>";
echo "</div>";

// Environment Information
echo "<div class='test-section test-info'>";
echo "<h5><i class='bi bi-globe'></i> Environment Information</h5>";
echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<p><strong>HTTP Host:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "</p>";
echo "<p><strong>Server Name:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'Unknown') . "</p>";
echo "<p><strong>Request URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "</p>";
echo "<p><strong>Remote Address:</strong> " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "</p>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<p><strong>User Agent:</strong> " . substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 50) . "...</p>";
echo "<p><strong>Request Method:</strong> " . ($_SERVER['REQUEST_METHOD'] ?? 'Unknown') . "</p>";
echo "<p><strong>HTTPS:</strong> " . (isset($_SERVER['HTTPS']) ? 'Yes' : 'No') . "</p>";
echo "<p><strong>Port:</strong> " . ($_SERVER['SERVER_PORT'] ?? 'Unknown') . "</p>";
echo "</div>";
echo "</div>";
echo "</div>";

// Configuration Constants
echo "<div class='test-section test-info'>";
echo "<h5><i class='bi bi-gear'></i> Configuration Constants</h5>";
echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<p><strong>SITE_URL:</strong> " . (defined('SITE_URL') ? SITE_URL : 'Not defined') . "</p>";
echo "<p><strong>ENVIRONMENT:</strong> " . (defined('ENVIRONMENT') ? ENVIRONMENT : 'Not defined') . "</p>";
echo "<p><strong>IS_LOCAL:</strong> " . (defined('IS_LOCAL') ? (IS_LOCAL ? 'Yes' : 'No') : 'Not defined') . "</p>";
echo "<p><strong>DEBUG:</strong> " . (defined('DEBUG') ? (DEBUG ? 'Enabled' : 'Disabled') : 'Not defined') . "</p>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<p><strong>DB_HOST:</strong> " . (defined('DB_HOST') ? DB_HOST : 'Not defined') . "</p>";
echo "<p><strong>DB_PORT:</strong> " . (defined('DB_PORT') ? DB_PORT : 'Not defined') . "</p>";
echo "<p><strong>DB_NAME:</strong> " . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "</p>";
echo "<p><strong>DEFAULT_LANG:</strong> " . (defined('DEFAULT_LANG') ? DEFAULT_LANG : 'Not defined') . "</p>";
echo "</div>";
echo "</div>";
echo "</div>";

// Database Information
echo "<div class='test-section test-info'>";
echo "<h5><i class='bi bi-database'></i> Database Information</h5>";
try {
    require_once 'config.php';
    require_once 'core/db.php';
    
    echo "<div class='row'>";
    echo "<div class='col-md-6'>";
    echo "<p><strong>Connection Status:</strong> <span class='text-success'>✅ Connected</span></p>";
    echo "<p><strong>MySQL Version:</strong> " . $conn->server_info . "</p>";
    echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
    echo "</div>";
    echo "<div class='col-md-6'>";
    echo "<p><strong>Character Set:</strong> " . $conn->character_set_name() . "</p>";
    echo "<p><strong>Connection ID:</strong> " . $conn->thread_id . "</p>";
    echo "<p><strong>Client Info:</strong> " . $conn->client_info . "</p>";
    echo "</div>";
    echo "</div>";
    
    // Table Information
    echo "<h6>Database Tables:</h6>";
    $tables_result = $conn->query("SHOW TABLES");
    if ($tables_result && $tables_result->num_rows > 0) {
        echo "<div class='table-responsive'>";
        echo "<table class='table table-sm'>";
        echo "<thead><tr><th>Table Name</th><th>Rows</th><th>Engine</th></tr></thead>";
        echo "<tbody>";
        while ($table = $tables_result->fetch_array()) {
            $table_name = $table[0];
            $count_result = $conn->query("SELECT COUNT(*) as count FROM `$table_name`");
            $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
            
            $info_result = $conn->query("SHOW TABLE STATUS LIKE '$table_name'");
            $info = $info_result ? $info_result->fetch_assoc() : null;
            $engine = $info ? $info['Engine'] : 'Unknown';
            
            echo "<tr><td>$table_name</td><td>$count</td><td>$engine</td></tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p class='text-danger'><strong>Database Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// File System Information
echo "<div class='test-section test-info'>";
echo "<h5><i class='bi bi-folder'></i> File System Information</h5>";
echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<p><strong>Current Directory:</strong> " . getcwd() . "</p>";
echo "<p><strong>Script Path:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Disk Free Space:</strong> " . formatBytes(disk_free_space('.')) . "</p>";
echo "<p><strong>Disk Total Space:</strong> " . formatBytes(disk_total_space('.')) . "</p>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<p><strong>PHP Include Path:</strong> " . ini_get('include_path') . "</p>";
echo "<p><strong>Temp Directory:</strong> " . sys_get_temp_dir() . "</p>";
echo "<p><strong>Upload Temp Dir:</strong> " . ini_get('upload_tmp_dir') . "</p>";
echo "<p><strong>Session Save Path:</strong> " . session_save_path() . "</p>";
echo "</div>";
echo "</div>";

// Directory Permissions
echo "<h6>Directory Permissions:</h6>";
$directories = ['uploads', 'uploads/logos', 'uploads/media', 'uploads/products', 'logs'];
echo "<div class='table-responsive'>";
echo "<table class='table table-sm'>";
echo "<thead><tr><th>Directory</th><th>Exists</th><th>Readable</th><th>Writable</th><th>Permissions</th></tr></thead>";
echo "<tbody>";
foreach ($directories as $dir) {
    $exists = is_dir($dir);
    $readable = $exists ? is_readable($dir) : false;
    $writable = $exists ? is_writable($dir) : false;
    $perms = $exists ? substr(sprintf('%o', fileperms($dir)), -4) : 'N/A';
    
    echo "<tr>";
    echo "<td>$dir</td>";
    echo "<td>" . ($exists ? '<span class="text-success">✅</span>' : '<span class="text-danger">❌</span>') . "</td>";
    echo "<td>" . ($readable ? '<span class="text-success">✅</span>' : '<span class="text-danger">❌</span>') . "</td>";
    echo "<td>" . ($writable ? '<span class="text-success">✅</span>' : '<span class="text-danger">❌</span>') . "</td>";
    echo "<td>$perms</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
echo "</div>";
echo "</div>";

// Error Information
echo "<div class='test-section test-info'>";
echo "<h5><i class='bi bi-exclamation-triangle'></i> Error Information</h5>";
echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<p><strong>Error Reporting:</strong> " . error_reporting() . "</p>";
echo "<p><strong>Display Errors:</strong> " . (ini_get('display_errors') ? 'On' : 'Off') . "</p>";
echo "<p><strong>Log Errors:</strong> " . (ini_get('log_errors') ? 'On' : 'Off') . "</p>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<p><strong>Error Log File:</strong> " . ini_get('error_log') . "</p>";
echo "<p><strong>Last Error:</strong> " . (error_get_last() ? json_encode(error_get_last()) : 'None') . "</p>";
echo "</div>";
echo "</div>";

// Check for error log file
$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    $log_size = filesize($error_log);
    echo "<p><strong>Error Log Size:</strong> " . formatBytes($log_size) . "</p>";
    
    if ($log_size > 0) {
        echo "<h6>Recent Error Log Entries:</h6>";
        $log_content = file_get_contents($error_log);
        $log_lines = explode("\n", $log_content);
        $recent_lines = array_slice(array_filter($log_lines), -5);
        
        echo "<div class='code-block'>";
        foreach ($recent_lines as $line) {
            echo htmlspecialchars($line) . "<br>";
        }
        echo "</div>";
    }
}
echo "</div>";

// Session Information
echo "<div class='test-section test-info'>";
echo "<h5><i class='bi bi-person'></i> Session Information</h5>";
echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<p><strong>Session Status:</strong> " . session_status() . " (" . getSessionStatusText(session_status()) . ")</p>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Name:</strong> " . session_name() . "</p>";
echo "<p><strong>Session Lifetime:</strong> " . ini_get('session.gc_maxlifetime') . " seconds</p>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<p><strong>Cookie Lifetime:</strong> " . ini_get('session.cookie_lifetime') . " seconds</p>";
echo "<p><strong>Cookie Path:</strong> " . ini_get('session.cookie_path') . "</p>";
echo "<p><strong>Cookie Domain:</strong> " . ini_get('session.cookie_domain') . "</p>";
echo "<p><strong>Cookie Secure:</strong> " . (ini_get('session.cookie_secure') ? 'Yes' : 'No') . "</p>";
echo "</div>";
echo "</div>";

if (!empty($_SESSION)) {
    echo "<h6>Session Data:</h6>";
    echo "<div class='code-block'>";
    echo "<pre>" . htmlspecialchars(print_r($_SESSION, true)) . "</pre>";
    echo "</div>";
} else {
    echo "<p class='text-muted'>No session data available.</p>";
}
echo "</div>";

// Helper Functions
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

function getSessionStatusText($status) {
    switch ($status) {
        case PHP_SESSION_DISABLED:
            return 'Disabled';
        case PHP_SESSION_NONE:
            return 'None';
        case PHP_SESSION_ACTIVE:
            return 'Active';
        default:
            return 'Unknown';
    }
}
?>

