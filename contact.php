<?php
require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

$page_title = __('contact_us');
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = __('all_fields_required');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = __('invalid_email_format');
    } else {
        // Insert contact message into database
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        
        if ($stmt->execute()) {
            $success_message = __('message_sent_successfully');
            // Clear form data
            $name = $email = $subject = $message = '';
        } else {
            $error_message = __('error_sending_message');
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-gradient"><?php echo __('get_in_touch'); ?></h1>
                <p class="lead text-muted"><?php echo __('contact_us_description'); ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-medium">
                        <div class="card-body p-5">
                            <?php if ($success_message): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $success_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $error_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="contact.php">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label"><?php echo __('full_name'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" value="" placeholder="<?php echo htmlspecialchars(__('full_name')); ?>" autocomplete="off" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label"><?php echo __('email_address'); ?> <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subject" class="form-label"><?php echo __('subject'); ?></label>
                                    <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="message" class="form-label"><?php echo __('message'); ?> <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-send"></i> <?php echo __('send_message'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow-medium h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4"><?php echo __('contact_information'); ?></h5>
                            
                            <div class="contact-info">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="contact-icon me-3">
                                        <i class="bi bi-envelope-fill text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo __('email'); ?></h6>
                                        <p class="text-muted mb-0"><?php echo get_setting('business_email', 'info@smartprozen.com', $conn); ?></p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center mb-3">
                                    <div class="contact-icon me-3">
                                        <i class="bi bi-telephone-fill text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo __('phone'); ?></h6>
                                        <p class="text-muted mb-0"><?php echo get_setting('business_phone', '+1-555-0123', $conn); ?></p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center mb-3">
                                    <div class="contact-icon me-3">
                                        <i class="bi bi-geo-alt-fill text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo __('address'); ?></h6>
                                        <p class="text-muted mb-0"><?php echo get_translated_text(get_setting('business_address', '123 Business Street, City, State 12345', $conn), 'business_address'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center mb-3">
                                    <div class="contact-icon me-3">
                                        <i class="bi bi-clock-fill text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo __('business_hours'); ?></h6>
                                        <p class="text-muted mb-0"><?php echo __('24_7_support'); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h6 class="mb-3"><?php echo __('follow_us'); ?></h6>
                            <div class="social-links">
                                <?php 
                                $social_facebook = get_setting('social_facebook', '', $conn);
                                if (!empty($social_facebook)): ?>
                                    <a href="<?php echo htmlspecialchars($social_facebook); ?>" class="btn btn-outline-primary btn-sm me-2 mb-2" target="_blank">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php 
                                $social_twitter = get_setting('social_twitter', '', $conn);
                                if (!empty($social_twitter)): ?>
                                    <a href="<?php echo htmlspecialchars($social_twitter); ?>" class="btn btn-outline-info btn-sm me-2 mb-2" target="_blank">
                                        <i class="bi bi-twitter"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php 
                                $social_instagram = get_setting('social_instagram', '', $conn);
                                if (!empty($social_instagram)): ?>
                                    <a href="<?php echo htmlspecialchars($social_instagram); ?>" class="btn btn-outline-danger btn-sm me-2 mb-2" target="_blank">
                                        <i class="bi bi-instagram"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php 
                                $social_linkedin = get_setting('social_linkedin', '', $conn);
                                if (!empty($social_linkedin)): ?>
                                    <a href="<?php echo htmlspecialchars($social_linkedin); ?>" class="btn btn-outline-primary btn-sm me-2 mb-2" target="_blank">
                                        <i class="bi bi-linkedin"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.contact-icon {
    width: 55px;
    height: 55px;
    background: rgba(0, 123, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.contact-info > div {
    transition: all 0.3s ease;
    padding: 10px;
    border-radius: 8px;
}

.contact-info > div:hover {
    background-color: #f8f9fa;
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.social-links a {
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.social-links a:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}
</style>

<?php include 'includes/footer.php'; ?>