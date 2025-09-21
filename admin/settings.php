<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_settings')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings_to_update = [
        'site_name', 'site_description', 'business_name', 'business_email', 
        'business_phone', 'business_address', 'currency', 'currency_symbol',
        'theme_skin', 'google_font', 'logo_filename', 'favicon_filename',
        'social_facebook', 'social_twitter', 'social_instagram', 'social_linkedin',
        'google_analytics', 'meta_keywords', 'maintenance_mode', 'registration_enabled',
        'email_verification_required', 'max_file_upload_size', 'allowed_file_types',
        'default_language', 'timezone', 'date_format', 'time_format',
        'items_per_page', 'enable_reviews', 'enable_wishlist', 'enable_coupons',
        'enable_guest_checkout', 'tax_rate', 'shipping_enabled', 'free_shipping_threshold',
        'order_notification_email', 'support_email'
    ];

    foreach ($settings_to_update as $setting_key) {
        if (isset($_POST[$setting_key])) {
            $setting_value = $_POST[$setting_key];
            
            // Handle file uploads
            if ($setting_key === 'logo_filename' && isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                $target_dir = __DIR__ . "/../uploads/logos/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $new_filename = uniqid() . '-' . basename($_FILES["logo"]["name"]);
                if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_dir . $new_filename)) {
                    $setting_value = $new_filename;
                }
            }
            
            if ($setting_key === 'favicon_filename' && isset($_FILES['favicon']) && $_FILES['favicon']['error'] == 0) {
                $target_dir = __DIR__ . "/../uploads/logos/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $new_filename = uniqid() . '-' . basename($_FILES["favicon"]["name"]);
                if (move_uploaded_file($_FILES["favicon"]["tmp_name"], $target_dir . $new_filename)) {
                    $setting_value = $new_filename;
                }
            }

            // Update setting in database
            $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->bind_param("ss", $setting_key, $setting_value);
            $stmt->execute();
            $stmt->close();
        }
    }

    log_activity('admin', $_SESSION['admin_id'], 'settings_update', 'Updated site settings.');
    $_SESSION['success_message'] = "Settings updated successfully!";
    header('Location: settings.php');
    exit;
}

// Get current settings
$settings = get_all_settings($conn);

require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Site Settings</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Settings</li>
    </ol>

    <?php show_flash_messages(); ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- General Settings -->
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-gear-fill me-2"></i>General Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="site_name" name="site_name" 
                                   value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="site_description" class="form-label">Site Description</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_name" class="form-label">Business Name</label>
                            <input type="text" class="form-control" id="business_name" name="business_name" 
                                   value="<?php echo htmlspecialchars($settings['business_name'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_email" class="form-label">Business Email</label>
                            <input type="email" class="form-control" id="business_email" name="business_email" 
                                   value="<?php echo htmlspecialchars($settings['business_email'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_phone" class="form-label">Business Phone</label>
                            <input type="text" class="form-control" id="business_phone" name="business_phone" 
                                   value="<?php echo htmlspecialchars($settings['business_phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_address" class="form-label">Business Address</label>
                            <textarea class="form-control" id="business_address" name="business_address" rows="3"><?php echo htmlspecialchars($settings['business_address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-palette-fill me-2"></i>Appearance</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="theme_skin" class="form-label">Theme Skin</label>
                            <select class="form-select" id="theme_skin" name="theme_skin">
                                <option value="enhanced.css" <?php echo ($settings['theme_skin'] ?? '') === 'enhanced.css' ? 'selected' : ''; ?>>Enhanced (Default)</option>
                                <option value="dark.css" <?php echo ($settings['theme_skin'] ?? '') === 'dark.css' ? 'selected' : ''; ?>>Dark Theme</option>
                                <option value="modern-components.css" <?php echo ($settings['theme_skin'] ?? '') === 'modern-components.css' ? 'selected' : ''; ?>>Modern Components</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="google_font" class="form-label">Google Font</label>
                            <select class="form-select" id="google_font" name="google_font">
                                <option value="Poppins" <?php echo ($settings['google_font'] ?? '') === 'Poppins' ? 'selected' : ''; ?>>Poppins</option>
                                <option value="Roboto" <?php echo ($settings['google_font'] ?? '') === 'Roboto' ? 'selected' : ''; ?>>Roboto</option>
                                <option value="Open Sans" <?php echo ($settings['google_font'] ?? '') === 'Open Sans' ? 'selected' : ''; ?>>Open Sans</option>
                                <option value="Lato" <?php echo ($settings['google_font'] ?? '') === 'Lato' ? 'selected' : ''; ?>>Lato</option>
                                <option value="Montserrat" <?php echo ($settings['google_font'] ?? '') === 'Montserrat' ? 'selected' : ''; ?>>Montserrat</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            <?php if (!empty($settings['logo_filename'])): ?>
                                <div class="mt-2">
                                    <img src="<?php echo SITE_URL . '/uploads/logos/' . htmlspecialchars($settings['logo_filename']); ?>" 
                                         alt="Current Logo" width="100" class="img-thumbnail">
                                    <input type="hidden" name="logo_filename" value="<?php echo htmlspecialchars($settings['logo_filename']); ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="favicon" class="form-label">Favicon</label>
                            <input type="file" class="form-control" id="favicon" name="favicon" accept="image/*">
                            <?php if (!empty($settings['favicon_filename'])): ?>
                                <div class="mt-2">
                                    <img src="<?php echo SITE_URL . '/uploads/logos/' . htmlspecialchars($settings['favicon_filename']); ?>" 
                                         alt="Current Favicon" width="32" class="img-thumbnail">
                                    <input type="hidden" name="favicon_filename" value="<?php echo htmlspecialchars($settings['favicon_filename']); ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- E-commerce Settings -->
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-cart-fill me-2"></i>E-commerce Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="currency" class="form-label">Currency</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="USD" <?php echo ($settings['currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>USD - US Dollar</option>
                                <option value="EUR" <?php echo ($settings['currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>EUR - Euro</option>
                                <option value="GBP" <?php echo ($settings['currency'] ?? '') === 'GBP' ? 'selected' : ''; ?>>GBP - British Pound</option>
                                <option value="CAD" <?php echo ($settings['currency'] ?? '') === 'CAD' ? 'selected' : ''; ?>>CAD - Canadian Dollar</option>
                                <option value="AUD" <?php echo ($settings['currency'] ?? '') === 'AUD' ? 'selected' : ''; ?>>AUD - Australian Dollar</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="currency_symbol" class="form-label">Currency Symbol</label>
                            <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" 
                                   value="<?php echo htmlspecialchars($settings['currency_symbol'] ?? '$'); ?>" maxlength="5">
                        </div>
                        
                        <div class="mb-3">
                            <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                            <input type="number" step="0.01" class="form-control" id="tax_rate" name="tax_rate" 
                                   value="<?php echo htmlspecialchars($settings['tax_rate'] ?? '0.00'); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="shipping_enabled" name="shipping_enabled" 
                                       value="1" <?php echo ($settings['shipping_enabled'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="shipping_enabled">Enable Shipping</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="free_shipping_threshold" class="form-label">Free Shipping Threshold</label>
                            <input type="number" step="0.01" class="form-control" id="free_shipping_threshold" name="free_shipping_threshold" 
                                   value="<?php echo htmlspecialchars($settings['free_shipping_threshold'] ?? '0.00'); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enable_guest_checkout" name="enable_guest_checkout" 
                                       value="1" <?php echo ($settings['enable_guest_checkout'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enable_guest_checkout">Enable Guest Checkout</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enable_coupons" name="enable_coupons" 
                                       value="1" <?php echo ($settings['enable_coupons'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enable_coupons">Enable Coupons</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Settings -->
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-toggle-on me-2"></i>Features</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enable_reviews" name="enable_reviews" 
                                       value="1" <?php echo ($settings['enable_reviews'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enable_reviews">Enable Product Reviews</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enable_wishlist" name="enable_wishlist" 
                                       value="1" <?php echo ($settings['enable_wishlist'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enable_wishlist">Enable Wishlist</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="registration_enabled" name="registration_enabled" 
                                       value="1" <?php echo ($settings['registration_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="registration_enabled">Enable User Registration</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="email_verification_required" name="email_verification_required" 
                                       value="1" <?php echo ($settings['email_verification_required'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="email_verification_required">Require Email Verification</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                       value="1" <?php echo ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="items_per_page" class="form-label">Items Per Page</label>
                            <input type="number" class="form-control" id="items_per_page" name="items_per_page" 
                                   value="<?php echo htmlspecialchars($settings['items_per_page'] ?? '12'); ?>" min="1" max="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Social Media -->
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-share-fill me-2"></i>Social Media</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="social_facebook" class="form-label">Facebook URL</label>
                            <input type="url" class="form-control" id="social_facebook" name="social_facebook" 
                                   value="<?php echo htmlspecialchars($settings['social_facebook'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="social_twitter" class="form-label">Twitter URL</label>
                            <input type="url" class="form-control" id="social_twitter" name="social_twitter" 
                                   value="<?php echo htmlspecialchars($settings['social_twitter'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="social_instagram" class="form-label">Instagram URL</label>
                            <input type="url" class="form-control" id="social_instagram" name="social_instagram" 
                                   value="<?php echo htmlspecialchars($settings['social_instagram'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="social_linkedin" class="form-label">LinkedIn URL</label>
                            <input type="url" class="form-control" id="social_linkedin" name="social_linkedin" 
                                   value="<?php echo htmlspecialchars($settings['social_linkedin'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO & Analytics -->
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-search me-2"></i>SEO & Analytics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                            <textarea class="form-control" id="meta_keywords" name="meta_keywords" rows="3"><?php echo htmlspecialchars($settings['meta_keywords'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="google_analytics" class="form-label">Google Analytics ID</label>
                            <input type="text" class="form-control" id="google_analytics" name="google_analytics" 
                                   value="<?php echo htmlspecialchars($settings['google_analytics'] ?? ''); ?>" 
                                   placeholder="GA-XXXXXXXXX or G-XXXXXXXXXX">
                        </div>
                        
                        <div class="mb-3">
                            <label for="order_notification_email" class="form-label">Order Notification Email</label>
                            <input type="email" class="form-control" id="order_notification_email" name="order_notification_email" 
                                   value="<?php echo htmlspecialchars($settings['order_notification_email'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="support_email" class="form-label">Support Email</label>
                            <input type="email" class="form-control" id="support_email" name="support_email" 
                                   value="<?php echo htmlspecialchars($settings['support_email'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save me-2"></i>Save All Settings
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary btn-lg ms-2">
                            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Theme preview functionality
document.getElementById('theme_skin').addEventListener('change', function() {
    const theme = this.value;
    const link = document.querySelector('link[href*="css/"]');
    if (link) {
        link.href = '<?php echo SITE_URL; ?>/css/' + theme;
    }
});

// Font preview functionality
document.getElementById('google_font').addEventListener('change', function() {
    const font = this.value;
    const link = document.createElement('link');
    link.href = 'https://fonts.googleapis.com/css2?family=' + font.replace(' ', '+') + ':wght@300;400;500;600;700&display=swap';
    link.rel = 'stylesheet';
    
    // Remove existing font link
    const existingLink = document.querySelector('link[href*="fonts.googleapis.com"]');
    if (existingLink) {
        existingLink.remove();
    }
    
    document.head.appendChild(link);
    
    // Apply font to body
    document.body.style.fontFamily = font + ', sans-serif';
});
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>