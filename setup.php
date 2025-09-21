<?php
define('INSTALL_LOCK_FILE', __DIR__ . '/installed.lock');

// Check if the application is already installed
if (file_exists(INSTALL_LOCK_FILE)) {
    header('Location: index.php');
    exit();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/core/functions.php';

$message = '';
$error = '';

// --- Pre-setup Checks ---
// Check for mysqli extension
if (!extension_loaded('mysqli')) {
    die("<p class='text-danger'>Error: The 'mysqli' PHP extension is not enabled. Please enable it to proceed with the setup.</p>");
}

// Check if config.php exists and is readable
if (!file_exists(__DIR__ . '/config.php')) {
    die("<p class='text-danger'>Error: 'config.php' not found. Please create 'config.php' based on 'config.php.template' and configure your database settings.</p>");
}

// Check if schema.sql exists and is readable
if (!file_exists(__DIR__ . '/schema.sql')) {
    die("<p class='text-danger'>Error: 'schema.sql' not found. This file is required to set up the database structure.</p>");
}
// --- End Pre-setup Checks ---

// Database connection for setup (using credentials from config.php)
// This connection is initially without selecting a specific DB to allow CREATE DATABASE
$setup_conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($setup_conn->connect_error) {
    die("Connection failed: " . $setup_conn->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'drop_create_db':
                // Drop database
                if ($setup_conn->query("DROP DATABASE IF EXISTS `" . DB_NAME . "`")) {
                    $message .= "<p class='text-success'>Database '" . DB_NAME . "' dropped successfully (if it existed).</p>";
                } else {
                    $error .= "<p class='text-danger'>Error dropping database: " . $setup_conn->error . "</p>";
                }

                // Create database
                if ($setup_conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`")) {
                    $message .= "<p class='text-success'>Database '" . DB_NAME . "' created successfully.</p>";
                } else {
                    $error .= "<p class='text-danger'>Error creating database: " . $setup_conn->error . "</p>";
                }
                break;

            case 'import_schema':
                $schema_sql = file_get_contents(__DIR__ . '/schema.sql');
                if ($schema_sql === false) {
                    $error .= "<p class='text-danger'>Error: schema.sql not found or unreadable.</p>";
                } else {
                    // Select the database before importing schema
                    if (!$setup_conn->select_db(DB_NAME)) {
                        $error .= "<p class='text-danger'>Error selecting database '" . DB_NAME . "': " . $setup_conn->error . "</p>";
                    } else {
                        // Check if tables already exist and drop them
                        $result = $setup_conn->query("SHOW TABLES");
                        if ($result && $result->num_rows > 0) {
                            $message .= "<p class='text-warning'>Existing tables found. Dropping and recreating...</p>";
                            
                            // Disable foreign key checks temporarily
                            $setup_conn->query("SET FOREIGN_KEY_CHECKS = 0");
                            
                            // First, drop all tables if they exist (in reverse order to handle foreign keys)
                            $drop_tables = [
                                'page_sections', 'section_templates', 'menus', 'settings', 'contact_messages', 
                                'subscribers', 'modules', 'email_templates', 'activity_logs', 'payment_gateways', 
                                'coupons', 'media_library', 'posts', 'pages', 'testimonials', 'reviews', 
                                'seo_metadata', 'downloads', 'wishlist', 'order_items', 'orders', 
                                'products', 'product_categories', 'users', 'admin_users', 'roles'
                            ];
                            
                            foreach ($drop_tables as $table) {
                                $setup_conn->query("DROP TABLE IF EXISTS `$table`");
                            }
                            
                            // Re-enable foreign key checks
                            $setup_conn->query("SET FOREIGN_KEY_CHECKS = 1");
                        }
                        
                        // Execute multi-query for schema.sql
                        if ($setup_conn->multi_query($schema_sql)) {
                            do {
                                // Store first result set
                                if ($result = $setup_conn->store_result()) {
                                    $result->free();
                                }
                                // While there are more results, go to next
                            } while ($setup_conn->more_results() && $setup_conn->next_result());

                            if ($setup_conn->errno) {
                                $error .= "<p class='text-danger'>Error importing schema: " . $setup_conn->error . "</p>";
                                $error .= "<p class='text-danger'>Error Code: " . $setup_conn->errno . "</p>";
                                $error .= "<p class='text-danger'>SQL State: " . $setup_conn->sqlstate . "</p>";
                            } else {
                                $message .= "<p class='text-success'>Schema imported successfully.</p>";
                                // Run additional alteration scripts
                                require_once __DIR__ . '/alter_modules_table.php';
                            }
                        } else {
                            $error .= "<p class='text-danger'>Error executing schema: " . $setup_conn->error . "</p>";
                            $error .= "<p class='text-danger'>Error Code: " . $setup_conn->errno . "</p>";
                            $error .= "<p class='text-danger'>SQL State: " . $setup_conn->sqlstate . "</p>";
                        }
                    }
                }
                break;

            case 'setup_admin':
                $admin_username = trim($_POST['admin_username']);
                $admin_email = trim($_POST['admin_email']);
                $admin_plain_password = $_POST['admin_password'];

                if (empty($admin_username) || empty($admin_email) || empty($admin_plain_password)) {
                    $error .= "<p class='text-danger'>All admin fields are required.</p>";
                    break;
                }
                if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
                    $error .= "<p class='text-danger'>Invalid admin email format.</p>";
                    break;
                }

                $hashed_password = password_hash($admin_plain_password, PASSWORD_DEFAULT);

                // Re-establish connection to the specific database for admin user operations
                $admin_conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                if ($admin_conn->connect_error) {
                    $error .= "<p class='text-danger'>Database connection failed for admin setup: " . $admin_conn->connect_error . "</p>";
                    break;
                }

                // Check if an admin user with role_id 1 (Super Admin) already exists
                $stmt = $admin_conn->prepare("SELECT id FROM admin_users WHERE role_id = 1 LIMIT 1");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Admin user with role_id 1 exists, update their credentials
                    $row = $result->fetch_assoc();
                    $admin_id = $row['id'];

                    $update_stmt = $admin_conn->prepare("UPDATE admin_users SET username = ?, email = ?, password = ? WHERE id = ?");
                    $update_stmt->bind_param("sssi", $admin_username, $admin_email, $hashed_password, $admin_id);

                    if ($update_stmt->execute()) {
                        $message .= "<p class='text-success'>Admin user (ID: {$admin_id}) credentials updated successfully!</p>";
                    } else {
                        $error .= "<p class='text-danger'>Error updating admin user credentials: " . $admin_conn->error . "</p>";
                    }
                    $update_stmt->close();
                } else {
                    // No admin user with role_id 1 exists, insert a new one
                    $insert_stmt = $admin_conn->prepare("INSERT INTO admin_users (username, email, password, role_id, is_active) VALUES (?, ?, ?, 1, 1)");
                    $insert_stmt->bind_param("sss", $admin_username, $admin_email, $hashed_password);

                    if ($insert_stmt->execute()) {
                        $message .= "<p class='text-success'>New admin user created successfully!</p>";
                    } else {
                        $error .= "<p class='text-danger'>Error creating new admin user: " . $admin_conn->error . "</p>";
                    }
                    $insert_stmt->close();
                }
                $stmt->close();
                $admin_conn->close();

                // Create lock file to prevent re-running setup
                file_put_contents(INSTALL_LOCK_FILE, time());
                $message .= "<p class='text-success'>Setup complete! You will be redirected to the homepage shortly.</p>";
                header('Refresh: 3; URL=index.php'); // Redirect after 3 seconds
                break;
        }
    }
}

$setup_conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartProzen Setup Wizard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; margin-top: 50px; }
        .card { border: none; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15); }
        .card-header { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h2>SmartProzen Setup Wizard</h2>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <h3 class="mb-3">Step 1: Database Setup</h3>
                <p class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    **WARNING:** Clicking "Drop & Create Database" will **PERMANENTLY DELETE ALL DATA** in the `<?php echo DB_NAME; ?>` database if it exists. Proceed with caution!
                </p>

                <form method="POST" class="mb-4">
                    <button type="submit" name="action" value="drop_create_db" class="btn btn-danger me-2">
                        Drop & Create Database
                    </button>
                    <button type="submit" name="action" value="import_schema" class="btn btn-primary">
                        Import Schema
                    </button>
                </form>

                <hr class="my-4">

                <h3 class="mb-3">Step 2: Admin User Setup</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label for="admin_username" class="form-label">Admin Username</label>
                        <input type="text" class="form-control" id="admin_username" name="admin_username" required>
                    </div>
                    <div class="mb-3">
                        <label for="admin_email" class="form-label">Admin Email</label>
                        <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="admin_password" class="form-label">Admin Password</label>
                        <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                    </div>
                    <button type="submit" name="action" value="setup_admin" class="btn btn-success">
                        Set Admin Credentials
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>