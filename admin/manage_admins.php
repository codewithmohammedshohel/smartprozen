<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

// Authenticate and authorize admin
if (!is_admin_logged_in() || !has_permission('manage_admins')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Handle form submission for adding/editing admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $admin_id = $_POST['admin_id'] ?? null;

    if ($admin_id) { // Update
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admin_users SET username=?, email=?, password=?, role_id=?, is_active=? WHERE id=?");
            $stmt->bind_param("sssiii", $username, $email, $hashed_password, $role_id, $is_active, $admin_id);
        } else {
            $stmt = $conn->prepare("UPDATE admin_users SET username=?, email=?, role_id=?, is_active=? WHERE id=?");
            $stmt->bind_param("ssiii", $username, $email, $role_id, $is_active, $admin_id);
        }
        log_activity('admin', $_SESSION['admin_id'], 'admin_update', "Updated admin user: $username");
    } else { // Insert
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admin_users (username, email, password, role_id, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $username, $email, $hashed_password, $role_id, $is_active);
        log_activity('admin', $_SESSION['admin_id'], 'admin_create', "Created new admin user: $username");
    }
    $stmt->execute();
    $_SESSION['success_message'] = "Admin user saved successfully.";
    header('Location: manage_admins.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    if ($id_to_delete != 1) { // Prevent deleting super admin
        $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ?");
        $stmt->bind_param("i", $id_to_delete);
        $stmt->execute();
        log_activity('admin', $_SESSION['admin_id'], 'admin_delete', "Deleted admin user ID: $id_to_delete");
        $_SESSION['success_message'] = "Admin user deleted.";
    } else {
        $_SESSION['error_message'] = "Cannot delete the primary Super Admin.";
    }
    header('Location: manage_admins.php');
    exit;
}

// Fetch data for display
$admins = $conn->query("SELECT a.*, r.name AS role_name FROM admin_users a JOIN roles r ON a.role_id = r.id ORDER BY a.id ASC");
$roles = $conn->query("SELECT * FROM roles");

$edit_admin = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit_admin = $stmt->get_result()->fetch_assoc();
}

require_once '../includes/admin_header.php';
?>

<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <div class="dashboard-header d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Admins & Staff</h1>
            <a href="manage_roles.php" class="btn btn-primary">Manage Roles</a>
        </div>

        <?php show_flash_messages(); ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-person-plus me-1"></i>
                <?php echo $edit_admin ? 'Edit Admin' : 'Add New Admin'; ?>
            </div>
            <div class="card-body">
                <form action="manage_admins.php" method="POST">
                    <input type="hidden" name="admin_id" value="<?php echo $edit_admin['id'] ?? ''; ?>">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($edit_admin['username'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($edit_admin['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password: <?php if($edit_admin) echo '(Leave blank to keep current password)'; ?></label>
                        <input type="password" id="password" name="password" class="form-control" <?php if(!$edit_admin) echo 'required'; ?>>
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role:</label>
                        <select id="role_id" name="role_id" class="form-select" required>
                            <?php while($role = $roles->fetch_assoc()): ?>
                                <option value="<?php echo $role['id']; ?>" <?php if(isset($edit_admin) && $edit_admin['role_id'] == $role['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($role['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" <?php if(!isset($edit_admin) || $edit_admin['is_active']) echo 'checked'; ?>>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><?php echo $edit_admin ? 'Update Admin' : 'Add Admin'; ?></button>
                    <?php if ($edit_admin): ?>
                        <a href="manage_admins.php" class="btn btn-secondary">Cancel Edit</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-people me-1"></i>
                Existing Admins
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($admin = $admins->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $admin['id']; ?></td>
                                <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                <td><?php echo htmlspecialchars($admin['role_name']); ?></td>
                                <td>
                                    <?php if ($admin['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="manage_admins.php?edit=<?php echo $admin['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <?php if ($admin['id'] != 1): ?>
                                    <a href="manage_admins.php?delete=<?php echo $admin['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this admin?')">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>