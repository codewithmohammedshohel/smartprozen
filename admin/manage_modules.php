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
        <div class="dashboard-header mb-4">
            <h1>Manage Modules</h1>
        </div>
        <p>Enable or disable major features of your website. Disabling a module will hide it from both the admin panel and the frontend.</p>
        
        <?php show_flash_messages(); ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-puzzle-fill me-1"></i>
                Module Settings
            </div>
            <div class="card-body">
                <form action="manage_modules.php" method="POST">
                    <div class="table-responsive mb-3">
                        <table class="table table-striped table-hover table-bordered">
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
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="module-<?php echo $module['slug']; ?>" name="modules[<?php echo $module['slug']; ?>]" value="1" <?php if($module['is_active']) echo 'checked'; ?>>
                                            <label class="form-check-label" for="module-<?php echo $module['slug']; ?>"></label>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Module Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>