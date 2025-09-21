<?php
require_once __DIR__ . '/../includes/header.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $error_message = 'Please enter your email address.';
    } else {
        // Always return a generic message to prevent user enumeration
        $success_message = "If an account with that email address exists, a password reset link has been sent.";

        // Check if user exists (to avoid unnecessary token generation for non-existent users)
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Check for existing active token (basic rate limiting)
            $check_token_stmt = $conn->prepare("SELECT created_at FROM password_resets WHERE email = ? ORDER BY created_at DESC LIMIT 1");
            $check_token_stmt->bind_param("s", $email);
            $check_token_stmt->execute();
            $check_token_stmt->bind_result($last_created_at);
            $check_token_stmt->fetch();
            $check_token_stmt->close();

            $rate_limit_seconds = 300; // 5 minutes
            if ($last_created_at && (time() - strtotime($last_created_at) < $rate_limit_seconds)) {
                // Too soon, do nothing but still show generic success message
                // This prevents spamming the user with emails and reduces server load
            } else {
                // Generate a cryptographically secure token
                $token = bin2hex(random_bytes(32));
                $hashed_token = password_hash($token, PASSWORD_DEFAULT);

                // Store the HASHED token in the password_resets table
                // Use INSERT ... ON DUPLICATE KEY UPDATE to handle existing entries
                $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?) ON DUPLICATE KEY UPDATE token = ?, created_at = CURRENT_TIMESTAMP");
                $stmt->bind_param("sss", $email, $hashed_token, $hashed_token);
                $stmt->execute();
                $stmt->close();

                // In a real application, you would EMAIL this link to the user.
                // For demonstration, we'll just show a message.
                $reset_link = SITE_URL . '/auth/new_password.php?token=' . $token;
                // TODO: Implement actual email sending here
                // Example: send_password_reset_email($email, $reset_link);
                
                // For debugging/testing, you might temporarily echo the link, but remove in production!
                // echo "<div class=\"alert alert-info\">DEBUG: Reset link: <a href='{$reset_link}'>{$reset_link}</a></div>";
            }
        }
        // No user found, but still show generic success message to prevent enumeration
    }
} else if (isset($_GET['token'])) {
    // This part handles the case where a user lands on this page with a token
    // This should ideally redirect to new_password.php or handle token validation here
    // For now, we'll just show a generic message if they land here with a token
    $error_message = "Please use the password reset link from your email.";
}

?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header"><h3 class="text-center font-weight-light my-4"><?php echo __('reset_password'); ?></h3></div>
                <div class="card-body">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php else: ?>
                        <form action="reset_password.php" method="POST">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputEmail" type="email" name="email" placeholder="name@example.com" required />
                                <label for="inputEmail"><?php echo __('email_address'); ?></label>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                <a class="small" href="/smartprozen/auth/login.php"><?php echo __('return_to_login'); ?></a>
                                <button type="submit" class="btn btn-primary"><?php echo __('reset_password'); ?></button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>