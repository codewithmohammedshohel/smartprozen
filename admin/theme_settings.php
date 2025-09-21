<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_themes')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings_to_update = [
        'theme_primary_color', 'theme_secondary_color', 'theme_background_color', 'theme_text_color',
        'theme_skin', 'font_family_bn', 'font_family_en', 'font_google_url_bn', 'font_google_url_en'
    ];
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    foreach ($settings_to_update as $key) {
        if (isset($_POST[$key])) {
            $stmt->bind_param("ss", $_POST[$key], $key);
            $stmt->execute();
        }
    }
    $stmt->close();
    
    log_activity('admin', $_SESSION['admin_id'], 'theme_update', 'Updated theme and appearance settings.');
    $_SESSION['success_message'] = "Theme settings updated successfully!";
    header("Location: theme_settings.php");
    exit;
}

$settings = get_all_settings($conn);
// Create arrays of Google Fonts for the dropdowns
$google_fonts_bn = ['Hind Siliguri' => '...', 'Anek Bangla' => '...'];
$google_fonts_en = ['Poppins' => '...', 'Roboto' => '...'];

require_once '../includes/admin_header.php';
?>
<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <h1>Theme & Appearance</h1>
        <?php show_flash_messages(); ?>
        <form action="theme_settings.php" method="POST">
             <fieldset>
                <legend>Color Scheme</legend>
                <label>Primary Color: <input type="color" name="theme_primary_color" value="<?php echo $settings['theme_primary_color']; ?>"></label>
                </fieldset>

            <fieldset>
                <legend>Website Skin</legend>
                <select name="theme_skin">
                    <option value="default.css" <?php if($settings['theme_skin'] == 'default.css') echo 'selected'; ?>>Modern Light</option>
                    <option value="dark.css" <?php if($settings['theme_skin'] == 'dark.css') echo 'selected'; ?>>Professional Dark</option>
                    <option value="corporate.css" <?php if($settings['theme_skin'] == 'corporate.css') echo 'selected'; ?>>Corporate Blue</option>
                </select>
            </fieldset>

            <fieldset>
                <legend>Typography</legend>
                </fieldset>

            <button type="submit" class="btn">Save Appearance</button>
        </form>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>