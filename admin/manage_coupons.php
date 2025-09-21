<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

// Authenticate and authorize admin
if (!is_admin_logged_in() || !has_permission('manage_coupons')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Handle form submission for adding/editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    $code = strtoupper(trim($_POST['code']));
    $type = $_POST['type'];
    $value = $_POST['value'];
    $expires_at = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $coupon_id = $_POST['coupon_id'] ?? null;

    if ($coupon_id) { // Update
        $stmt = $conn->prepare("UPDATE coupons SET code=?, type=?, value=?, expires_at=?, is_active=? WHERE id=?");
        $stmt->bind_param("ssdssi", $code, $type, $value, $expires_at, $is_active, $coupon_id);
    } else { // Insert
        $stmt = $conn->prepare("INSERT INTO coupons (code, type, value, expires_at, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $code, $type, $value, $expires_at, $is_active);
    }
    $stmt->execute();
    log_activity('admin', $_SESSION['admin_id'], 'coupon_save', "Saved coupon: $code");
    $_SESSION['success_message'] = "Coupon saved successfully.";
    header('Location: manage_coupons.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM coupons WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    log_activity('admin', $_SESSION['admin_id'], 'coupon_delete', "Deleted coupon ID: {$_GET['delete']}");
    $_SESSION['success_message'] = "Coupon deleted.";
    header('Location: manage_coupons.php');
    exit;
}

// Fetch data for display
$coupons = $conn->query("SELECT * FROM coupons ORDER BY id DESC");
$edit_coupon = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit_coupon = $stmt->get_result()->fetch_assoc();
}

require_once '../includes/admin_header.php';
?>

<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Manage Coupons</h1>
        </div>

        <?php show_flash_messages(); ?>

        <div class="form-container">
            <h2><?php echo $edit_coupon ? 'Edit Coupon' : 'Add New Coupon'; ?></h2>
            <form action="manage_coupons.php" method="POST">
                <input type="hidden" name="coupon_id" value="<?php echo $edit_coupon['id'] ?? ''; ?>">
                
                <label for="code">Coupon Code:</label>
                <input type="text" id="code" name="code" value="<?php echo $edit_coupon['code'] ?? ''; ?>" required>

                <label for="type">Discount Type:</label>
                <select id="type" name="type" required>
                    <option value="percentage" <?php if(isset($edit_coupon) && $edit_coupon['type'] == 'percentage') echo 'selected'; ?>>Percentage (%)</option>
                    <option value="fixed" <?php if(isset($edit_coupon) && $edit_coupon['type'] == 'fixed') echo 'selected'; ?>>Fixed Amount ($)</option>
                </select>

                <label for="value">Value:</label>
                <input type="number" step="0.01" id="value" name="value" value="<?php echo $edit_coupon['value'] ?? ''; ?>" required>

                <label for="expires_at">Expiry Date (optional):</label>
                <input type="date" id="expires_at" name="expires_at" value="<?php echo $edit_coupon['expires_at'] ?? ''; ?>">

                <label>
                    <input type="checkbox" name="is_active" value="1" <?php if(!isset($edit_coupon) || $edit_coupon['is_active']) echo 'checked'; ?>>
                    Active
                </label>
                
                <button type="submit" class="btn"><?php echo $edit_coupon ? 'Update Coupon' : 'Add Coupon'; ?></button>
            </form>
        </div>

        <div class="table-container">
            <h2>Existing Coupons</h2>
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($coupon = $coupons->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($coupon['code']); ?></td>
                        <td><?php echo ucfirst($coupon['type']); ?></td>
                        <td><?php echo ($coupon['type'] == 'percentage') ? $coupon['value'] . '%' : '$' . number_format($coupon['value'], 2); ?></td>
                        <td><?php echo $coupon['expires_at'] ?? 'Never'; ?></td>
                        <td><?php echo $coupon['is_active'] ? 'Active' : 'Inactive'; ?></td>
                        <td>
                            <a href="manage_coupons.php?edit=<?php echo $coupon['id']; ?>" class="btn btn-secondary">Edit</a>
                            <a href="manage_coupons.php?delete=<?php echo $coupon['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>