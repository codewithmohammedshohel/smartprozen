<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

// Redirect if already logged in
if (is_user_logged_in()) {
    $redirect_url = $_SESSION['redirect_after_login'] ?? '/smartprozen/user/dashboard.php';
    unset($_SESSION['redirect_after_login']);
    header('Location: ' . $redirect_url);
    exit;
}

$error_message = '';
$success_message = '';
$email = '';

// Handle logout message
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $success_message = 'You have been successfully logged out.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    if (empty($email) || empty($password)) {
        $error_message = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Rate limiting check (simple implementation)
        $ip = $_SERVER['REMOTE_ADDR'];
        $attempts_key = 'login_attempts_' . $ip;
        $attempts = $_SESSION[$attempts_key] ?? 0;
        
        if ($attempts >= 5) {
            $error_message = 'Too many failed login attempts. Please try again later.';
        } else {
            $stmt = $conn->prepare("SELECT id, name, email, password, is_active FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                if (!$user['is_active']) {
                    $error_message = 'Your account has been deactivated. Please contact support.';
                } elseif (password_verify($password, $user['password'])) {
                    // Reset failed attempts
                    unset($_SESSION[$attempts_key]);
                    
                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    
                    // Handle remember me
                    if ($remember_me) {
                        // Set a long-lasting cookie (30 days)
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                        
                        // Store token in database (you'd need a remember_tokens table)
                        // For now, we'll just set a longer session
                        ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60);
                    }
                    
                    // Log successful login
                    error_log("User login successful: {$email} from IP: {$ip}");
                    
                    // Redirect to intended page or dashboard
                    $redirect_url = $_SESSION['redirect_after_login'] ?? '/smartprozen/user/dashboard.php';
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $redirect_url);
                    exit;
                } else {
                    // Increment failed attempts
                    $_SESSION[$attempts_key] = $attempts + 1;
                    $error_message = 'Invalid password.';
                    error_log("Failed login attempt: {$email} from IP: {$ip}");
                }
            } else {
                // Increment failed attempts
                $_SESSION[$attempts_key] = $attempts + 1;
                $error_message = 'No user found with that email address.';
                error_log("Failed login attempt with non-existent email: {$email} from IP: {$ip}");
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
                <div class="card-header"><h3 class="text-center font-weight-light my-4"><?php echo __('login'); ?></h3></div>
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
                    
                    <form action="login.php" method="POST" id="loginForm">
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputEmail" type="email" name="email" placeholder="name@example.com" value="<?php echo htmlspecialchars($email); ?>" required />
                            <label for="inputEmail"><?php echo __('email_address'); ?></label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Password" required />
                            <label for="inputPassword"><?php echo __('password'); ?></label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" id="inputRememberMe" type="checkbox" name="remember_me" value="1" />
                            <label class="form-check-label" for="inputRememberMe"><?php echo __('remember_password'); ?></label>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small" href="/smartprozen/auth/reset_password.php"><?php echo __('forgot_password'); ?></a>
                            <button type="submit" class="btn btn-primary" id="loginBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="loginSpinner"></span>
                                <span id="loginText"><?php echo __('login'); ?></span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small"><a href="/smartprozen/auth/register.php"><?php echo __('need_an_account_sign_up'); ?></a></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('loginBtn');
    const spinner = document.getElementById('loginSpinner');
    const text = document.getElementById('loginText');
    
    // Show loading state
    btn.disabled = true;
    spinner.classList.remove('d-none');
    text.textContent = '<?php echo __('logging_in'); ?>...';
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>