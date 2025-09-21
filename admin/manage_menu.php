<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_pages')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['label_en'])) {
    $label_en = $_POST['label_en'];
    $label_bn = $_POST['label_bn'];
    $url = $_POST['url'];
    $display_order = (int)$_POST['display_order'];
    $item_id = $_POST['item_id'] ?? null;

    $label_json = json_encode(['en' => $label_en, 'bn' => $label_bn]);

    if ($item_id) {
        $stmt = $conn->prepare("UPDATE menu_items SET label_json = ?, url = ?, display_order = ? WHERE id = ?");
        $stmt->bind_param("ssii", $label_json, $url, $display_order, $item_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO menu_items (label_json, url, display_order) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $label_json, $url, $display_order);
    }
    $stmt->execute();
    $_SESSION['success_message'] = "Menu item saved.";
    header('Location: manage_menu.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    $_SESSION['success_message'] = "Menu item deleted.";
    header('Location: manage_menu.php');
    exit;
}

$menu_items = $conn->query("SELECT * FROM menu_items ORDER BY display_order ASC");
$edit_item = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit_item = $stmt->get_result()->fetch_assoc();
    if ($edit_item) {
        $labels = json_decode($edit_item['label_json'], true);
    }
}

require_once '../includes/admin_header.php';
?>
<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Manage Main Menu</h1>
        </div>
        
        <?php show_flash_messages(); ?>

        <div class="form-container">
            <h2><?php echo $edit_item ? 'Edit Menu Item' : 'Add New Menu Item'; ?></h2>
            <form action="manage_menu.php" method="POST">
                <input type="hidden" name="item_id" value="<?php echo $edit_item['id'] ?? ''; ?>">
                
                <label for="label_en">Label (English):</label>
                <input type="text" id="label_en" name="label_en" value="<?php echo htmlspecialchars($labels['en'] ?? ''); ?>" required>
                
                <label for="label_bn">Label (Bangla):</label>
                <input type="text" id="label_bn" name="label_bn" value="<?php echo htmlspecialchars($labels['bn'] ?? ''); ?>" required>

                <label for="url">URL:</label>
                <input type="text" id="url" name="url" value="<?php echo htmlspecialchars($edit_item['url'] ?? ''); ?>" placeholder="/smartprozen/page.php?slug=about-us" required>
                
                <label for="display_order">Display Order:</label>
                <input type="number" id="display_order" name="display_order" value="<?php echo $edit_item['display_order'] ?? 0; ?>" required>
                
                <button type="submit" class="btn"><?php echo $edit_item ? 'Update Item' : 'Add Item'; ?></button>
            </form>
        </div>

        <div class="table-container">
            <h2>Current Menu Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Label (EN)</th>
                        <th>Label (BN)</th>
                        <th>URL</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $menu_items->fetch_assoc()): 
                        $item_labels = json_decode($item['label_json'], true);
                    ?>
                    <tr>
                        <td><?php echo $item['display_order']; ?></td>
                        <td><?php echo htmlspecialchars($item_labels['en']); ?></td>
                        <td><?php echo htmlspecialchars($item_labels['bn']); ?></td>
                        <td><?php echo htmlspecialchars($item['url']); ?></td>
                        <td>
                            <a href="manage_menu.php?edit=<?php echo $item['id']; ?>" class="btn btn-secondary">Edit</a>
                            <a href="manage_menu.php?delete=<?php echo $item['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>