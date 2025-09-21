<?php
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';

if (!is_admin_logged_in() || !has_permission('manage_admins')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

$permissions_list = [
    'Content' => [
        'manage_pages' => 'Manage Pages & Menu',
        'manage_posts' => 'Manage Blog Posts',
        'manage_media' => 'Manage Media Library'
    ],
    'E-commerce' => [
        'manage_products' => 'Manage Products',
        'manage_orders' => 'Manage Orders',
        'manage_coupons' => 'Manage Coupons'
    ],
    'Users' => [
        'manage_users' => 'Manage Customers',
        'manage_admins' => 'Manage Admins & Roles'
    ],
    'Settings' => [
        'view_dashboard' => 'View Dashboard',
        'manage_settings' => 'Manage Site Settings',
        'manage_themes' => 'Manage Themes & Appearance',
        'manage_payments' => 'Manage Payment Gateways'
    ]
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role_name'])) {
    $role_name = $_POST['role_name'];
    $permissions = json_encode($_POST['permissions'] ?? []);
    $role_id = $_POST['role_id'] ?? null;

    if ($role_id) {
        $stmt = $conn->prepare("UPDATE roles SET name = ?, permissions = ? WHERE id = ?");
        $stmt->bind_param("ssi", $role_name, $permissions, $role_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO roles (name, permissions) VALUES (?, ?)");
        $stmt->bind_param("ss", $role_name, $permissions);
    }
    $stmt->execute();
    log_activity('admin', $_SESSION['admin_id'], 'role_save', "Saved role: $role_name");
    $_SESSION['success_message'] = "Role saved successfully.";
    header('Location: manage_roles.php');
    exit;
}

$roles = $conn->query("SELECT * FROM roles");
$edit_role = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM roles WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit_role = $stmt->get_result()->fetch_assoc();
    if ($edit_role) {
        $current_permissions = json_decode($edit_role['permissions'], true);
    }
}

?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Manage Roles</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Roles</li>
    </ol>
    <div class="row">
        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-person-plus-fill me-1"></i>
                    <?php echo $edit_role ? 'Edit Role' : 'Add New Role'; ?>
                </div>
                <div class="card-body">
                    <form action="manage_roles.php" method="POST">
                        <input type="hidden" name="role_id" value="<?php echo $edit_role['id'] ?? ''; ?>">
                        <div class="mb-3">
                            <label for="role_name" class="form-label">Role Name</label>
                            <input type="text" id="role_name" name="role_name" class="form-control" value="<?php echo htmlspecialchars($edit_role['name'] ?? ''); ?>" required>
                        </div>
                        
                        <h5>Permissions</h5>
                        <?php foreach ($permissions_list as $group => $permissions): ?>
                            <fieldset class="mb-3">
                                <legend class="fs-6"><strong><?php echo $group; ?></strong></legend>
                                <?php foreach ($permissions as $key => $label): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[<?php echo $key; ?>]" value="true" id="perm_<?php echo $key; ?>" 
                                            <?php if(isset($current_permissions[$key]) && $current_permissions[$key] == 'true') echo 'checked'; ?>>
                                        <label class="form-check-label" for="perm_<?php echo $key; ?>"><?php echo $label; ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </fieldset>
                        <?php endforeach; ?>
                        
                        <button type="submit" class="btn btn-primary"><?php echo $edit_role ? 'Update Role' : 'Add Role'; ?></button>
                        <?php if ($edit_role): ?>
                            <a href="manage_roles.php" class="btn btn-secondary">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-collection-fill me-1"></i>
                    Existing Roles
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Role Name</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($role = $roles->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($role['name']); ?></td>
                                        <td>
                                            <?php
                                            $role_permissions = json_decode($role['permissions'], true);
                                            if ($role['id'] == 1) {
                                                echo '<span class="badge bg-success">Super Admin (All Permissions)</span>';
                                            } elseif (is_array($role_permissions)) {
                                                foreach ($role_permissions as $key => $value) {
                                                    if ($value == 'true') {
                                                        echo '<span class="badge bg-info me-1">' . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . '</span>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($role['id'] != 1): ?>
                                                <a href="manage_roles.php?edit=<?php echo $role['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <a href="manage_roles.php?delete=<?php echo $role['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this role?')">Delete</a>
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
</div>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>