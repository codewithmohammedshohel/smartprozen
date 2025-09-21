<?php
require_once __DIR__ . '/../includes/header.php';

$error_message = '';
$success_message = '';
$name = ''; // Initialize $name
$email = ''; // Initialize $email

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $whatsapp_number = trim($_POST['whatsapp_number'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($name) || empty($email) || empty($address) || empty($contact_number) || empty($password) || empty($password_confirm)) {
        $error_message = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif ($password !== $password_confirm) {
        $error_message = 'Passwords do not match.';
    } elseif (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[^A-Za-z0-9]/", $password)) {
        $error_message = 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
    } else {
        // Check if user already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = 'A user with this email address already exists.';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, address, contact_number, whatsapp_number) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $email, $hashed_password, $address, $contact_number, $whatsapp_number);

            if ($stmt->execute()) {
                // Automatically log in the new user
                $_SESSION['user_id'] = $stmt->insert_id;
                header('Location: /smartprozen/user/dashboard.php');
                exit;
            } else {
                $error_message = 'An error occurred during registration. Please try again.';
            }
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header"><h3 class="text-center font-weight-light my-4"><?php echo __('create_account'); ?></h3></div>
                <div class="card-body">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form action="register.php" method="POST">
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputName" type="text" name="name" placeholder="Enter your name" value="<?php echo htmlspecialchars($name); ?>" required />
                            <label for="inputName"><?php echo __('full_name'); ?></label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputEmail" type="email" name="email" placeholder="name@example.com" value="<?php echo htmlspecialchars($email); ?>" required />
                            <label for="inputEmail"><?php echo __('email_address'); ?></label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputAddress" type="text" name="address" placeholder="Enter your address" required />
                            <label for="inputAddress"><?php echo __('address'); ?></label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputContactNumber" type="text" name="contact_number" placeholder="Enter your contact number" required />
                            <label for="inputContactNumber"><?php echo __('contact_number'); ?></label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputWhatsappNumber" type="text" name="whatsapp_number" placeholder="Enter your WhatsApp number" />
                            <label for="inputWhatsappNumber"><?php echo __('whatsapp_number'); ?></label>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Create a password" required />
                                    <label for="inputPassword"><?php echo __('password'); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <input class="form-control" id="inputPasswordConfirm" type="password" name="password_confirm" placeholder="Confirm password" required />
                                    <label for="inputPasswordConfirm"><?php echo __('confirm_password'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 mb-0">
                            <div class="d-grid"><button type="submit" class="btn btn-primary btn-block"><?php echo __('create_account'); ?></button></div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small"><a href="/smartprozen/auth/login.php"><?php echo __('have_an_account_go_to_login'); ?></a></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>