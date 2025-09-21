<?php
require_once __DIR__ . '/../includes/header.php';

$error_message = '';
$success_message = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $error_message = 'Invalid password reset token.';
} else {
    // Check if the token is valid and not expired (e.g., within 1 hour)
    $stmt = $conn->prepare("SELECT email, created_at FROM password_resets WHERE token = ? AND created_at >= NOW() - INTERVAL 1 HOUR");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($reset_request = $result->fetch_assoc()) {
        $email = $reset_request['email'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if (empty($password) || empty($password_confirm)) {
                $error_message = 'Please enter and confirm your new password.';
            } elseif ($password !== $password_confirm) {
                $error_message = 'Passwords do not match.';
            } else {
                // Hash the new password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Update the user's password
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param("ss", $hashed_password, $email);
                $stmt->execute();

                // Delete the reset token
                $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();

                $success_message = 'Your password has been reset successfully. You can now log in with your new password.';
                // Redirect to login page after a few seconds
                header('refresh:5;url=/smartprozen/auth/login.php');
            }
        }
    } else {
        $error_message = 'Invalid or expired password reset token.';
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header"><h3 class="text-center font-weight-light my-4"><?php echo __('new_password'); ?></h3></div>
                <div class="card-body">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php else: ?>
                        <form action="new_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Enter new password" required />
                                <label for="inputPassword"><?php echo __('new_password'); ?></label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputPasswordConfirm" type="password" name="password_confirm" placeholder="Confirm new password" required />
                                <label for="inputPasswordConfirm"><?php echo __('confirm_new_password'); ?></label>
                            </div>
                            <div class="d-flex align-items-center justify-content-end mt-4 mb-0">
                                <button type="submit" class="btn btn-primary"><?php echo __('set_new_password'); ?></button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>