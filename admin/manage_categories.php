<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_products')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Handle POST request for Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'] ?? null;
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = slugify($name);

    if (empty($name)) {
        $_SESSION['error_message'] = "Category name is required.";
    } else {
        if ($category_id) { // Update existing category
            $stmt = $conn->prepare("UPDATE product_categories SET name = ?, description = ?, slug = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $description, $slug, $category_id);
            $log_action = 'category_update';
            $log_details = "Updated category ID: {$category_id}";
        } else { // Insert new category
            $stmt = $conn->prepare("INSERT INTO product_categories (name, description, slug) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $description, $slug);
            $log_action = 'category_create';
            $log_details = "Created category: {$name}";
        }

        if ($stmt->execute()) {
            log_activity('admin', $_SESSION['admin_id'], $log_action, $log_details);
            $_SESSION['success_message'] = "Category saved successfully.";
        } else {
            $_SESSION['error_message'] = "Error saving category: " . $conn->error;
        }
    }
    header('Location: manage_categories.php');
    exit;
}

// Handle DELETE request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $category_id = (int)$_GET['id'];
    
    // Check if category is in use
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $product_count = $stmt->get_result()->fetch_row()[0];

    if ($product_count > 0) {
        $_SESSION['error_message'] = "Cannot delete category as it is assigned to products.";
    } else {
        $stmt = $conn->prepare("DELETE FROM product_categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        if ($stmt->execute()) {
            log_activity('admin', $_SESSION['admin_id'], 'category_delete', "Deleted category ID: {$category_id}");
            $_SESSION['success_message'] = "Category deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Error deleting category: " . $conn->error;
        }
    }
    header('Location: manage_categories.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Manage Categories</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Categories</li>
    </ol>
    
    <?php show_flash_messages(); ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-tags-fill me-1"></i>
                    All Categories
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $categories = $conn->query("SELECT * FROM product_categories ORDER BY name ASC");
                                while($category = $categories->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['slug']); ?></td>
                                    <td>
                                        <a href="manage_categories.php?action=edit&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="manage_categories.php?action=delete&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-pencil-square me-1"></i>
                    <?php echo ($action === 'edit') ? 'Edit Category' : 'Add New Category'; ?>
                </div>
                <div class="card-body">
                    <?php
                    $edit_category = null;
                    if ($action === 'edit' && isset($_GET['id'])) {
                        $stmt = $conn->prepare("SELECT * FROM product_categories WHERE id = ?");
                        $stmt->bind_param("i", $_GET['id']);
                        $stmt->execute();
                        $edit_category = $stmt->get_result()->fetch_assoc();
                    }
                    ?>
                    <form action="manage_categories.php" method="POST">
                        <input type="hidden" name="category_id" value="<?php echo $edit_category['id'] ?? ''; ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($edit_category['name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($edit_category['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <?php echo ($action === 'edit') ? 'Update Category' : 'Add Category'; ?>
                        </button>
                        <?php if ($action === 'edit'): ?>
                            <a href="manage_categories.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
