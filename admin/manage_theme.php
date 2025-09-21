<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_theme')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Handle theme settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_theme_settings') {
    $theme_settings = [
        'header_layout' => $_POST['header_layout'] ?? 'default',
        'footer_layout' => $_POST['footer_layout'] ?? 'default',
        'color_scheme' => $_POST['color_scheme'] ?? 'default',
        'primary_color' => $_POST['primary_color'] ?? '#007bff',
        'secondary_color' => $_POST['secondary_color'] ?? '#6c757d',
        'font_family' => $_POST['font_family'] ?? 'system',
        'custom_css' => $_POST['custom_css'] ?? '',
        'header_code' => $_POST['header_code'] ?? '',
        'footer_code' => $_POST['footer_code'] ?? ''
    ];
    
    // Update settings in database
    foreach ($theme_settings as $key => $value) {
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, category) VALUES (?, ?, 'text', 'theme') ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->bind_param("sss", $key, $value, $value);
        $stmt->execute();
    }
    
    $_SESSION['success_message'] = "Theme settings updated successfully.";
    header('Location: manage_theme.php');
    exit;
}

// Get current theme settings
$theme_settings = [];
$settings_result = $conn->query("SELECT setting_key, setting_value FROM settings WHERE category = 'theme'");
while ($row = $settings_result->fetch_assoc()) {
    $theme_settings[$row['setting_key']] = $row['setting_value'];
}

// Default values
$defaults = [
    'header_layout' => 'default',
    'footer_layout' => 'default',
    'color_scheme' => 'default',
    'primary_color' => '#007bff',
    'secondary_color' => '#6c757d',
    'font_family' => 'system',
    'custom_css' => '',
    'header_code' => '',
    'footer_code' => ''
];

$theme_settings = array_merge($defaults, $theme_settings);

require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Theme Customization</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="previewTheme()">
                <i class="bi bi-eye"></i> Preview
            </button>
            <button type="button" class="btn btn-sm btn-primary" onclick="saveThemeSettings()">
                <i class="bi bi-save"></i> Save Changes
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form id="themeForm" method="POST">
                <input type="hidden" name="action" value="update_theme_settings">
                
                <!-- Header Layout -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Header Layout</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="header_layout" value="default" id="header_default" <?php echo $theme_settings['header_layout'] === 'default' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="header_default">
                                        <img src="/uploads/templates/header-default.jpg" class="img-thumbnail" style="width: 100%; height: 80px; object-fit: cover;" alt="Default Header">
                                        <div class="text-center mt-2">Default</div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="header_layout" value="centered" id="header_centered" <?php echo $theme_settings['header_layout'] === 'centered' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="header_centered">
                                        <img src="/uploads/templates/header-centered.jpg" class="img-thumbnail" style="width: 100%; height: 80px; object-fit: cover;" alt="Centered Header">
                                        <div class="text-center mt-2">Centered</div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="header_layout" value="minimal" id="header_minimal" <?php echo $theme_settings['header_layout'] === 'minimal' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="header_minimal">
                                        <img src="/uploads/templates/header-minimal.jpg" class="img-thumbnail" style="width: 100%; height: 80px; object-fit: cover;" alt="Minimal Header">
                                        <div class="text-center mt-2">Minimal</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Color Scheme -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Color Scheme</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="primary_color" class="form-label">Primary Color</label>
                                <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="<?php echo htmlspecialchars($theme_settings['primary_color']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="secondary_color" class="form-label">Secondary Color</label>
                                <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="<?php echo htmlspecialchars($theme_settings['secondary_color']); ?>">
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <label class="form-label">Color Presets</label>
                            <div class="row">
                                <div class="col-auto">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setColorScheme('#007bff', '#6c757d')">Blue</button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="setColorScheme('#28a745', '#6c757d')">Green</button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="setColorScheme('#dc3545', '#6c757d')">Red</button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="setColorScheme('#ffc107', '#6c757d')">Orange</button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="setColorScheme('#343a40', '#6c757d')">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typography -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Typography</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="font_family" class="form-label">Font Family</label>
                            <select class="form-select" id="font_family" name="font_family">
                                <option value="system" <?php echo $theme_settings['font_family'] === 'system' ? 'selected' : ''; ?>>System Fonts</option>
                                <option value="roboto" <?php echo $theme_settings['font_family'] === 'roboto' ? 'selected' : ''; ?>>Roboto</option>
                                <option value="opensans" <?php echo $theme_settings['font_family'] === 'opensans' ? 'selected' : ''; ?>>Open Sans</option>
                                <option value="lato" <?php echo $theme_settings['font_family'] === 'lato' ? 'selected' : ''; ?>>Lato</option>
                                <option value="montserrat" <?php echo $theme_settings['font_family'] === 'montserrat' ? 'selected' : ''; ?>>Montserrat</option>
                                <option value="poppins" <?php echo $theme_settings['font_family'] === 'poppins' ? 'selected' : ''; ?>>Poppins</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Custom CSS -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Custom CSS</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" id="custom_css" name="custom_css" rows="10" placeholder="/* Add your custom CSS here */"><?php echo htmlspecialchars($theme_settings['custom_css']); ?></textarea>
                        <div class="form-text">Add custom CSS to override default styles. Use !important if needed.</div>
                    </div>
                </div>

                <!-- Header & Footer Code -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Custom Code</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="header_code" class="form-label">Header Code (HTML/JavaScript)</label>
                            <textarea class="form-control" id="header_code" name="header_code" rows="5" placeholder="<!-- Add custom code for header -->"><?php echo htmlspecialchars($theme_settings['header_code']); ?></textarea>
                            <div class="form-text">Code added before closing &lt;/head&gt; tag</div>
                        </div>
                        <div class="mb-3">
                            <label for="footer_code" class="form-label">Footer Code (HTML/JavaScript)</label>
                            <textarea class="form-control" id="footer_code" name="footer_code" rows="5" placeholder="<!-- Add custom code for footer -->"><?php echo htmlspecialchars($theme_settings['footer_code']); ?></textarea>
                            <div class="form-text">Code added before closing &lt;/body&gt; tag</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-lg-4">
            <!-- Live Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Live Preview</h5>
                </div>
                <div class="card-body">
                    <div id="themePreview" class="border rounded p-3" style="min-height: 300px;">
                        <div class="text-center">
                            <h6>Theme Preview</h6>
                            <p class="text-muted">Changes will appear here as you make them</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2 w-100" onclick="updatePreview()">Update Preview</button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="resetToDefaults()">
                            <i class="bi bi-arrow-clockwise"></i> Reset to Defaults
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="exportTheme()">
                            <i class="bi bi-download"></i> Export Theme
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="importTheme()">
                            <i class="bi bi-upload"></i> Import Theme
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setColorScheme(primary, secondary) {
    document.getElementById('primary_color').value = primary;
    document.getElementById('secondary_color').value = secondary;
    updatePreview();
}

function updatePreview() {
    const primaryColor = document.getElementById('primary_color').value;
    const secondaryColor = document.getElementById('secondary_color').value;
    const fontFamily = document.getElementById('font_family').value;
    const customCSS = document.getElementById('custom_css').value;
    
    const preview = document.getElementById('themePreview');
    preview.innerHTML = `
        <div style="font-family: ${fontFamily};">
            <div class="bg-light p-2 mb-2" style="border-left: 4px solid ${primaryColor};">
                <strong style="color: ${primaryColor};">Sample Header</strong>
            </div>
            <p style="color: ${secondaryColor};">This is a sample text with your chosen colors and font.</p>
            <button class="btn btn-sm" style="background-color: ${primaryColor}; color: white;">Sample Button</button>
        </div>
    `;
}

function saveThemeSettings() {
    document.getElementById('themeForm').submit();
}

function resetToDefaults() {
    if (confirm('Are you sure you want to reset all theme settings to defaults?')) {
        document.getElementById('primary_color').value = '#007bff';
        document.getElementById('secondary_color').value = '#6c757d';
        document.getElementById('font_family').value = 'system';
        document.getElementById('custom_css').value = '';
        document.getElementById('header_code').value = '';
        document.getElementById('footer_code').value = '';
        document.querySelector('input[name="header_layout"][value="default"]').checked = true;
        updatePreview();
    }
}

function exportTheme() {
    const themeData = {
        header_layout: document.querySelector('input[name="header_layout"]:checked').value,
        primary_color: document.getElementById('primary_color').value,
        secondary_color: document.getElementById('secondary_color').value,
        font_family: document.getElementById('font_family').value,
        custom_css: document.getElementById('custom_css').value,
        header_code: document.getElementById('header_code').value,
        footer_code: document.getElementById('footer_code').value
    };
    
    const dataStr = JSON.stringify(themeData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'smartprozen-theme.json';
    link.click();
}

function previewTheme() {
    window.open('/smartprozen/?preview=1', '_blank');
}

// Initialize preview on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePreview();
    
    // Add event listeners for real-time preview updates
    document.getElementById('primary_color').addEventListener('change', updatePreview);
    document.getElementById('secondary_color').addEventListener('change', updatePreview);
    document.getElementById('font_family').addEventListener('change', updatePreview);
});
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
