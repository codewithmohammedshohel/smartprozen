<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_settings')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modules_to_update = $_POST['modules'] ?? [];
    $all_modules = $conn->query("SELECT slug FROM modules");
    
    while ($module = $all_modules->fetch_assoc()) {
        $slug = $module['slug'];
        $is_active = in_array($slug, array_keys($modules_to_update)) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE modules SET is_active = ? WHERE slug = ?");
        $stmt->bind_param("is", $is_active, $slug);
        $stmt->execute();
    }
    
    log_activity('admin', $_SESSION['admin_id'], 'modules_update', 'Updated module statuses.');
    $_SESSION['success_message'] = "Module statuses updated successfully.";
    header("Location: manage_modules.php");
    exit;
}

$modules = $conn->query("SELECT * FROM modules ORDER BY name");

require_once '../includes/admin_header.php';
?>
<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Manage Modules</h1>
        </div>
        <p>Enable or disable major features of your website. Disabling a module will hide it from both the admin panel and the frontend.</p>
        
        <?php show_flash_messages(); ?>

        <div class="form-container">
            <form action="manage_modules.php" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Module Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($module = $modules->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($module['name']); ?></td>
                            <td>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="modules[<?php echo $module['slug']; ?>]" value="1" <?php if($module['is_active']) echo 'checked'; ?>>
                                    <span class="slider"></span>
                                </label>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <br>
                <button type="submit" class="btn">Save Module Settings</button>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>