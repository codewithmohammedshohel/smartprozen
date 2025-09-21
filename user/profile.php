<?php
require_once __DIR__ . '/../includes/user_header.php';

if (!is_logged_in()) {
    header('Location: /smartprozen/auth/login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$user = get_user_by_id($user_id, $conn);

// Handle profile details update
if (isset($_POST['update_details'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $errors = [];

    if (empty($name)) {
        $errors[] = "Name cannot be empty.";
    }
    if (empty($email)) {
        $errors[] = "Email cannot be empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } else {
        // Check if new email is already in use by another user
        $stmt_check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt_check_email->bind_param("si", $email, $user_id);
        $stmt_check_email->execute();
        $stmt_check_email->store_result();
        if ($stmt_check_email->num_rows > 0) {
            $errors[] = "This email address is already in use by another account.";
        }
        $stmt_check_email->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Profile details updated.";
        } else {
            $_SESSION['error_message'] = "Error updating profile.";
        }
    } else {
        $_SESSION['error_message'] = implode('<br>', $errors);
    }
    header('Location: profile.php');
    exit;
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = "All password fields are required.";
    } elseif (!password_verify($current_password, $user['password'])) {
        $errors[] = "Incorrect current password.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "New passwords do not match.";
    } elseif (strlen($new_password) < 8 || !preg_match("/[A-Z]/", $new_password) || !preg_match("/[a-z]/", $new_password) || !preg_match("/[0-9]/", $new_password) || !preg_match("/[^A-Za-z0-9]/", $new_password)) {
        $errors[] = 'New password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
    }

    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        if ($stmt->execute()) {
            log_activity('user', $user_id, 'password_change', 'User changed their password.');
            $_SESSION['success_message'] = "Password changed successfully.";
        } else {
            $_SESSION['error_message'] = "Error changing password.";
        }
    } else {
        $_SESSION['error_message'] = implode('<br>', $errors);
    }
    header('Location: profile.php');
    exit;
}

?>
<div class="row">
    <?php require_once __DIR__ . '/../includes/user_sidebar.php'; ?>
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="card-title">Account Details</h1>
                <?php show_flash_messages(); ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">Update Profile</div>
                            <div class="card-body">
                                <form action="profile.php" method="POST">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name:</label>
                                        <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    <button type="submit" name="update_details" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">Change Password</div>
                            <div class="card-body">
                                <form action="profile.php" method="POST">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password:</label>
                                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password:</label>
                                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password:</label>
                                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                    </div>
                                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>