<?php
require_once __DIR__ . '/../includes/header.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $error_message = 'Please enter your email address.';
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Generate a token
            $token = bin2hex(random_bytes(32));

            // Store the token in the password_resets table
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?) ON DUPLICATE KEY UPDATE token = ?, created_at = CURRENT_TIMESTAMP");
            $stmt->bind_param("sss", $email, $token, $token);
            $stmt->execute();

            // (In a real application, you would email this link)
            $reset_link = SITE_URL . '/auth/new_password.php?token=' . $token;
            $success_message = "A password reset link has been generated. Please click the following link to reset your password: <a href='{$reset_link}'>{$reset_link}</a>";
        } else {
            $error_message = 'No user found with that email address.';
        }
    }
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