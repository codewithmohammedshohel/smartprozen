<?php
/**
 * Simple Setup - Ultra-fast setup for immediate use
 */

// Increase execution time and memory
set_time_limit(300);
ini_set('memory_limit', '256M');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Simple Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .progress { height: 25px; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
        .step.success { border-left-color: #28a745; background: #d4edda; }
        .step.error { border-left-color: #dc3545; background: #f8d7da; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>🚀 SmartProZen Simple Setup</h2>
        <div class="progress mb-3">
            <div class="progress-bar" role="progressbar" style="width: 0%" id="progress"></div>
        </div>
        
        <?php
        try {
            // Step 1: Load config
            echo "<div class='step'>";
            echo "<h5>Step 1: Loading Configuration</h5>";
            if (!file_exists('config.php')) {
                throw new Exception("config.php not found!");
            }
            require_once 'config.php';
            echo "<p class='text-success'>✅ Configuration loaded</p>";
            echo "<p><strong>Environment:</strong> " . (defined('ENVIRONMENT') ? ENVIRONMENT : 'Unknown') . "</p>";
            echo "<p><strong>Site URL:</strong> " . (defined('SITE_URL') ? SITE_URL : 'Not defined') . "</p>";
            echo "</div>";
            updateProgress(20);
            
            // Step 2: Database
            echo "<div class='step'>";
            echo "<h5>Step 2: Database Connection</h5>";
            if (!file_exists('core/db.php')) {
                throw new Exception("core/db.php not found!");
            }
            require_once 'core/db.php';
            echo "<p class='text-success'>✅ Database connected</p>";
            echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
            echo "</div>";
            updateProgress(40);
            
            // Step 3: Create essential tables
            echo "<div class='step'>";
            echo "<h5>Step 3: Creating Tables</h5>";
            
            // Create settings table
            $conn->query("CREATE TABLE IF NOT EXISTS `settings` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `setting_key` varchar(100) NOT NULL,
                `setting_value` text,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `setting_key` (`setting_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            // Create admin_users table
            $conn->query("CREATE TABLE IF NOT EXISTS `admin_users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `username` varchar(50) NOT NULL,
                `email` varchar(100) NOT NULL,
                `password` varchar(255) NOT NULL,
                `full_name` varchar(100) NOT NULL,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            // Create pages table
            $conn->query("CREATE TABLE IF NOT EXISTS `pages` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `slug` varchar(255) NOT NULL,
                `content` longtext,
                `is_published` tinyint(1) DEFAULT 1,
                `is_homepage` tinyint(1) DEFAULT 0,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            echo "<p class='text-success'>✅ Essential tables created</p>";
            echo "</div>";
            updateProgress(60);
            
            // Step 4: Insert data
            echo "<div class='step'>";
            echo "<h5>Step 4: Inserting Data</h5>";
            
            // Insert settings
            $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('site_name', 'SmartProZen')");
            $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('site_tagline', 'Smart Tech, Simplified Living')");
            
            // Insert admin user
            $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
            $conn->query("INSERT IGNORE INTO admin_users (username, email, password, full_name) VALUES ('admin', 'admin@smartprozen.com', '$hashed_password', 'Administrator')");
            
            // Insert homepage
            $conn->query("INSERT IGNORE INTO pages (id, title, slug, content, is_published, is_homepage) VALUES (1, 'Home', 'home', 'Welcome to SmartProZen!', 1, 1)");
            
            echo "<p class='text-success'>✅ Basic data inserted</p>";
            echo "<p><strong>Admin User:</strong> admin / admin123</p>";
            echo "</div>";
            updateProgress(80);
            
            // Step 5: Create directories
            echo "<div class='step'>";
            echo "<h5>Step 5: Creating Directories</h5>";
            
            $dirs = ['uploads', 'uploads/logos', 'uploads/media'];
            foreach ($dirs as $dir) {
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                    echo "<p class='text-info'>📁 Created: $dir</p>";
                } else {
                    echo "<p class='text-muted'>📁 Exists: $dir</p>";
                }
            }
            
            echo "<p class='text-success'>✅ Directories ready</p>";
            echo "</div>";
            updateProgress(100);
            
            // Success
            echo "<div class='step success'>";
            echo "<h3>🎉 Setup Complete!</h3>";
            echo "<div class='row'>";
            echo "<div class='col-md-6'>";
            echo "<h5>Admin Access:</h5>";
            echo "<p><strong>URL:</strong> <a href='" . SITE_URL . "/admin/login.php' target='_blank'>" . SITE_URL . "/admin/login.php</a></p>";
            echo "<p><strong>Username:</strong> admin</p>";
            echo "<p><strong>Password:</strong> admin123</p>";
            echo "</div>";
            echo "<div class='col-md-6'>";
            echo "<h5>Frontend:</h5>";
            echo "<p><strong>Homepage:</strong> <a href='" . SITE_URL . "' target='_blank'>" . SITE_URL . "</a></p>";
            echo "<p><strong>Products:</strong> <a href='" . SITE_URL . "/products_list.php' target='_blank'>" . SITE_URL . "/products_list.php</a></p>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
            
            echo "<div class='mt-4'>";
            echo "<a href='" . SITE_URL . "/admin/login.php' class='btn btn-primary btn-lg'>";
            echo "🔐 Go to Admin Panel";
            echo "</a>";
            echo "<a href='" . SITE_URL . "' class='btn btn-success btn-lg ms-2'>";
            echo "🏠 View Homepage";
            echo "</a>";
            echo "<a href='" . SITE_URL . "/master_setup.php' class='btn btn-info btn-lg ms-2'>";
            echo "⚙️ Full Setup";
            echo "</a>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<div class='step error'>";
            echo "<h3>❌ Setup Failed!</h3>";
            echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p>Please check the error message and try again.</p>";
            echo "</div>";
        }
        ?>
        
    </div>
    
    <script>
        function updateProgress(percent) {
            const progressBar = document.getElementById('progress');
            progressBar.style.width = percent + '%';
            progressBar.textContent = percent + '%';
        }
    </script>
</body>
</html>

<?php
function updateProgress($percent) {
    echo "<script>updateProgress($percent);</script>";
    ob_flush();
    flush();
}
?>
