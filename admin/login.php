<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

if (is_admin_logged_in()) {
    header('Location: /smartprozen/admin/dashboard.php');
    exit;
}

$error_message = '';
$success_message = '';

// Handle logout message
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $success_message = 'You have been successfully logged out.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = 'Please fill in all fields.';
    } else {
        // Rate limiting check
        $ip = $_SERVER['REMOTE_ADDR'];
        $attempts_key = 'admin_login_attempts_' . $ip;
        $attempts = $_SESSION[$attempts_key] ?? 0;
        
        if ($attempts >= 5) {
            $error_message = 'Too many failed login attempts. Please try again later.';
        } else {
            $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($admin = $result->fetch_assoc()) {
                if (password_verify($password, $admin['password'])) {
                    // Reset failed attempts
                    unset($_SESSION[$attempts_key]);
                    
                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    
                    // Set session variables
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_role_id'] = $admin['role_id'];
                    
                    // Log successful login
                    log_activity('admin', $admin['id'], 'admin_login', "Admin {$admin['username']} logged in successfully.");
                    error_log("Admin login successful: {$username} from IP: {$ip}");
                    
                    // Redirect to intended page or dashboard
                    $redirect_url = $_SESSION['redirect_after_login'] ?? '/smartprozen/admin/dashboard.php';
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $redirect_url);
                    exit;
                } else {
                    // Increment failed attempts
                    $_SESSION[$attempts_key] = $attempts + 1;
                    $error_message = 'Invalid password.';
                    error_log("Failed admin login attempt: {$username} from IP: {$ip}");
                }
            } else {
                // Increment failed attempts
                $_SESSION[$attempts_key] = $attempts + 1;
                $error_message = 'No admin found with that username.';
                error_log("Failed admin login attempt with non-existent username: {$username} from IP: {$ip}");
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header"><h3 class="text-center font-weight-light my-4">Admin Login</h3></div>
                <div class="card-body">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="login.php" method="POST" id="adminLoginForm">
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputUsername" type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required />
                            <label for="inputUsername">Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Password" required />
                            <label for="inputPassword">Password</label>
                        </div>
                        <div class="d-flex align-items-center justify-content-end mt-4 mb-0">
                            <button type="submit" class="btn btn-primary" id="adminLoginBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="adminLoginSpinner"></span>
                                <span id="adminLoginText">Login</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('adminLoginBtn');
    const spinner = document.getElementById('adminLoginSpinner');
    const text = document.getElementById('adminLoginText');
    
    // Show loading state
    btn.disabled = true;
    spinner.classList.remove('d-none');
    text.textContent = 'Logging in...';
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>