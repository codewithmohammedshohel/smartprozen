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
    $theme_settings = [
        'theme_primary_color' => $_POST['theme_primary_color'] ?? '#007bff',
        'theme_body_bg' => $_POST['theme_body_bg'] ?? '#ffffff',
        'theme_text_color' => $_POST['theme_text_color'] ?? '#212529',
        'theme_font_family' => $_POST['theme_font_family'] ?? 'Poppins',
        'theme_button_radius' => $_POST['theme_button_radius'] ?? '4px',
        'theme_card_radius' => $_POST['theme_card_radius'] ?? '8px'
    ];

    foreach ($theme_settings as $key => $value) {
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $stmt->bind_param("ss", $key, $value);
        $stmt->execute();
        $stmt->close();
    }

    log_activity('admin', $_SESSION['admin_id'], 'theme_update', 'Updated theme settings.');
    $_SESSION['success_message'] = "Theme settings updated successfully!";
    header('Location: theme_settings.php');
    exit;
}

// Get current theme settings
$settings = get_all_settings($conn);

require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Theme Settings</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Theme Settings</li>
    </ol>

    <?php show_flash_messages(); ?>

    <form method="POST" id="theme-form">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-palette-fill me-2"></i>Theme Customization</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="theme_primary_color" class="form-label">Primary Color</label>
                                <input type="color" class="form-control form-control-color" id="theme_primary_color" 
                                       name="theme_primary_color" value="<?php echo htmlspecialchars($settings['theme_primary_color'] ?? '#007bff'); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="theme_body_bg" class="form-label">Background Color</label>
                                <input type="color" class="form-control form-control-color" id="theme_body_bg" 
                                       name="theme_body_bg" value="<?php echo htmlspecialchars($settings['theme_body_bg'] ?? '#ffffff'); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="theme_text_color" class="form-label">Text Color</label>
                                <input type="color" class="form-control form-control-color" id="theme_text_color" 
                                       name="theme_text_color" value="<?php echo htmlspecialchars($settings['theme_text_color'] ?? '#212529'); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="theme_font_family" class="form-label">Font Family</label>
                                <select class="form-select" id="theme_font_family" name="theme_font_family">
                                    <option value="Poppins" <?php echo ($settings['theme_font_family'] ?? 'Poppins') === 'Poppins' ? 'selected' : ''; ?>>Poppins</option>
                                    <option value="Roboto" <?php echo ($settings['theme_font_family'] ?? 'Poppins') === 'Roboto' ? 'selected' : ''; ?>>Roboto</option>
                                    <option value="Open Sans" <?php echo ($settings['theme_font_family'] ?? 'Poppins') === 'Open Sans' ? 'selected' : ''; ?>>Open Sans</option>
                                    <option value="Lato" <?php echo ($settings['theme_font_family'] ?? 'Poppins') === 'Lato' ? 'selected' : ''; ?>>Lato</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="theme_button_radius" class="form-label">Button Border Radius</label>
                                <select class="form-select" id="theme_button_radius" name="theme_button_radius">
                                    <option value="0px" <?php echo ($settings['theme_button_radius'] ?? '4px') === '0px' ? 'selected' : ''; ?>>0px (Square)</option>
                                    <option value="4px" <?php echo ($settings['theme_button_radius'] ?? '4px') === '4px' ? 'selected' : ''; ?>>4px (Rounded)</option>
                                    <option value="8px" <?php echo ($settings['theme_button_radius'] ?? '4px') === '8px' ? 'selected' : ''; ?>>8px (More Rounded)</option>
                                    <option value="20px" <?php echo ($settings['theme_button_radius'] ?? '4px') === '20px' ? 'selected' : ''; ?>>20px (Pill)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="theme_card_radius" class="form-label">Card Border Radius</label>
                                <select class="form-select" id="theme_card_radius" name="theme_card_radius">
                                    <option value="0px" <?php echo ($settings['theme_card_radius'] ?? '8px') === '0px' ? 'selected' : ''; ?>>0px (Square)</option>
                                    <option value="8px" <?php echo ($settings['theme_card_radius'] ?? '8px') === '8px' ? 'selected' : ''; ?>>8px (Rounded)</option>
                                    <option value="12px" <?php echo ($settings['theme_card_radius'] ?? '8px') === '12px' ? 'selected' : ''; ?>>12px (More Rounded)</option>
                                    <option value="16px" <?php echo ($settings['theme_card_radius'] ?? '8px') === '16px' ? 'selected' : ''; ?>>16px (Very Rounded)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-eye-fill me-2"></i>Live Preview</h5>
                    </div>
                    <div class="card-body">
                        <div id="theme-preview" style="border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; background: #ffffff;">
                            <h4 style="color: #007bff; margin-bottom: 15px;">Sample Heading</h4>
                            <p style="color: #212529; margin-bottom: 15px;">This is a sample paragraph to demonstrate your theme.</p>
                            <button class="btn btn-primary me-2" style="border-radius: 4px;">Primary Button</button>
                            <button class="btn btn-secondary" style="border-radius: 4px;">Secondary Button</button>
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
                            <i class="bi bi-save me-2"></i>Save Theme Settings
                        </button>
                        <a href="settings.php" class="btn btn-secondary btn-lg ms-2">
                            <i class="bi bi-arrow-left me-2"></i>Back to Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function updatePreview() {
    const preview = document.getElementById('theme-preview');
    const primaryColor = document.getElementById('theme_primary_color').value;
    const textColor = document.getElementById('theme_text_color').value;
    const bodyBg = document.getElementById('theme_body_bg').value;
    const fontFamily = document.getElementById('theme_font_family').value;
    const buttonRadius = document.getElementById('theme_button_radius').value;
    const cardRadius = document.getElementById('theme_card_radius').value;
    
    preview.style.backgroundColor = bodyBg;
    preview.style.color = textColor;
    preview.style.fontFamily = fontFamily + ', sans-serif';
    preview.style.borderRadius = cardRadius;
    
    const heading = preview.querySelector('h4');
    heading.style.color = primaryColor;
    
    const buttons = preview.querySelectorAll('button');
    buttons.forEach(button => {
        button.style.borderRadius = buttonRadius;
    });
}

document.querySelectorAll('input[type="color"], select').forEach(function(input) {
    input.addEventListener('change', updatePreview);
});

updatePreview();
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>