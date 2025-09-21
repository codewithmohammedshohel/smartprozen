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
    $product_id = $_POST['product_id'] ?? null;

    // Prepare bilingual data
    $name_json = json_encode(['en' => $_POST['name_en'], 'bn' => $_POST['name_bn']]);
    $description_json = json_encode(['en' => $_POST['description_en'], 'bn' => $_POST['description_bn']]);
    $price = $_POST['price'];

    // Handle main product image upload
    $image_filename = $_POST['existing_image_filename'] ?? null;
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $target_dir = __DIR__ . "/../uploads/media/";
        $new_filename = uniqid() . '-' . basename($_FILES["main_image"]["name"]);
        if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $target_dir . $new_filename)) {
            $image_filename = $new_filename;
            // Optionally create a thumbnail here
        }
    }

    // Handle digital file upload
    $digital_file_path = $_POST['existing_digital_file'] ?? null;
    if (isset($_FILES['digital_file']) && $_FILES['digital_file']['error'] == 0) {
        $target_dir = __DIR__ . "/../uploads/files/";
        $new_filename = uniqid() . '-' . basename($_FILES["digital_file"]["name"]);
        if (move_uploaded_file($_FILES["digital_file"]["tmp_name"], $target_dir . $new_filename)) {
            $digital_file_path = $new_filename;
        }
    }
    
    if ($product_id) { // Update existing product
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image_filename=?, digital_file_path=? WHERE id=?");
        $stmt->bind_param("ssds_si", $name_json, $description_json, $price, $image_filename, $digital_file_path, $product_id);
        $stmt->execute();
        log_activity('admin', $_SESSION['admin_id'], 'product_update', "Updated product ID: {$product_id}");
    } else { // Insert new product
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image_filename, digital_file_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssds_s", $name_json, $description_json, $price, $image_filename, $digital_file_path);
        $stmt->execute();
        $product_id = $conn->insert_id;
        log_activity('admin', $_SESSION['admin_id'], 'product_create', "Created product ID: {$product_id}");
    }

    // Handle product gallery images
    if ($product_id && isset($_FILES['gallery_images'])) {
        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['gallery_images']['error'][$key] == 0) {
                $target_dir = __DIR__ . "/../uploads/media/";
                $new_filename = uniqid() . '-' . basename($_FILES['gallery_images']['name'][$key]);
                if (move_uploaded_file($tmp_name, $target_dir . $new_filename)) {
                    $stmt = $conn->prepare("INSERT INTO product_images (product_id, image_filename) VALUES (?, ?)");
                    $stmt->bind_param("is", $product_id, $new_filename);
                    $stmt->execute();
                }
            }
        }
    }

    // Handle deletion of gallery images
    if (isset($_POST['delete_gallery_image'])) {
        foreach ($_POST['delete_gallery_image'] as $image_id) {
            // Get filename to delete from disk
            $stmt = $conn->prepare("SELECT image_filename FROM product_images WHERE id = ?");
            $stmt->bind_param("i", $image_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            if ($result && file_exists(__DIR__ . "/../uploads/media/" . $result['image_filename'])) {
                unlink(__DIR__ . "/../uploads/media/" . $result['image_filename']);
            }
            $stmt = $conn->prepare("DELETE FROM product_images WHERE id = ?");
            $stmt->bind_param("i", $image_id);
            $stmt->execute();
        }
    }

    // Handle SEO metadata
    $seo = $_POST['seo'];
    $stmt = $conn->prepare("INSERT INTO seo_metadata (entity_type, entity_id, meta_title, meta_description) VALUES ('product', ?, ?, ?) ON DUPLICATE KEY UPDATE meta_title=VALUES(meta_title), meta_description=VALUES(meta_description)");
    $stmt->bind_param("iss", $product_id, $seo['meta_title'], $seo['meta_description']);
    $stmt->execute();

    $_SESSION['success_message'] = "Product saved successfully.";
    header('Location: manage_products.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Manage Products</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Products</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-box-fill me-1"></i>
                    <?php echo ($action === 'edit') ? 'Edit Product' : 'Add New Product'; ?>
                </div>
                <div class="card-body">
                    <?php show_flash_messages(); ?>
                    <?php if ($action === 'list'): 
                        $products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
                    ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name (English)</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($product = $products->fetch_assoc()): ?>
                                    <tr>
                                        <td><img src="<?php echo SITE_URL . '/uploads/media/thumb-' . htmlspecialchars($product['image_filename']); ?>" alt="" width="50"></td>
                                        <td><?php echo htmlspecialchars(get_translated_text($product['name'], 'name')); ?></td>
                                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                                        <td>
                                            <a href="manage_products.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="manage_products.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="manage_products.php?action=add" class="btn btn-primary mt-3">Add New Product</a>
                    <?php elseif ($action === 'add' || $action === 'edit'): 
                        $product = null;
                        $seo = null;
                        $gallery_images = [];
                        if ($action === 'edit' && isset($_GET['id'])) {
                            $id = (int)$_GET['id'];
                            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();
                            $stmt = $conn->prepare("SELECT * FROM seo_metadata WHERE entity_type = 'product' AND entity_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$seo = $stmt->get_result()->fetch_assoc();
$stmt->close();
                            $gallery_images_stmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
                            $gallery_images_stmt->bind_param("i", $id);
                            $gallery_images_stmt->execute();
                            $gallery_images = $gallery_images_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        }
                    ?>
                        <form action="manage_products.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="product_id" value="<?php echo $product['id'] ?? ''; ?>">
                            
                            <div class="mb-3">
                                <label for="name_en" class="form-label">Product Name (English)</label>
                                <input type="text" id="name_en" name="name_en" class="form-control" value="<?php echo htmlspecialchars(json_decode($product['name'] ?? '""' , true)['en'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="name_bn" class="form-label">Product Name (Bangla)</label>
                                <input type="text" id="name_bn" name="name_bn" class="form-control" value="<?php echo htmlspecialchars(json_decode($product['name'] ?? '""' , true)['bn'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description_en" class="form-label">Description (English)</label>
                                <textarea class="form-control tinymce" id="description_en" name="description_en" rows="5"><?php echo htmlspecialchars(json_decode($product['description'] ?? '""' , true)['en'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="description_bn" class="form-label">Description (Bangla)</label>
                                <textarea class="form-control tinymce" id="description_bn" name="description_bn" rows="5"><?php echo htmlspecialchars(json_decode($product['description'] ?? '""' , true)['bn'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Price ($)</label>
                                <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?php echo $product['price'] ?? '0.00'; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="main_image" class="form-label">Main Product Image</label>
                                <input type="file" id="main_image" name="main_image" class="form-control">
                                <?php if (!empty($product['image_filename'])): ?>
                                    <p class="mt-2">Current: <img src="<?php echo SITE_URL . '/uploads/media/thumb-' . htmlspecialchars($product['image_filename']); ?>" alt="" width="100"></p>
                                    <input type="hidden" name="existing_image_filename" value="<?php echo htmlspecialchars($product['image_filename']); ?>">
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="gallery_images" class="form-label">Product Gallery Images</label>
                                <input type="file" id="gallery_images" name="gallery_images[]" class="form-control" multiple>
                                <div class="mt-2 row">
                                    <?php foreach ($gallery_images as $g_image): ?>
                                        <div class="col-3 mb-3">
                                            <img src="<?php echo SITE_URL . '/uploads/media/' . htmlspecialchars($g_image['image_filename']); ?>" class="img-fluid rounded" alt="">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="delete_gallery_image[]" value="<?php echo $g_image['id']; ?>" id="delete_g_image_<?php echo $g_image['id']; ?>">
                                                <label class="form-check-label" for="delete_g_image_<?php echo $g_image['id']; ?>">Delete</label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="digital_file" class="form-label">Digital File (Upload new to replace)</label>
                                <input type="file" id="digital_file" name="digital_file" class="form-control">
                                <?php if (!empty($product['digital_file_path'])): ?>
                                    <p class="mt-2">Current file: <?php echo htmlspecialchars($product['digital_file_path']); ?></p>
                                    <input type="hidden" name="existing_digital_file" value="<?php echo htmlspecialchars($product['digital_file_path']); ?>">
                                <?php endif; ?>
                            </div>

                            <fieldset class="mb-3 p-3 border rounded">
                                <legend class="float-none w-auto px-2 fs-6">SEO Settings</legend>
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" id="meta_title" name="seo[meta_title]" class="form-control" value="<?php echo htmlspecialchars($seo['meta_title'] ?? ''); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea id="meta_description" name="seo[meta_description]" class="form-control" rows="3"><?php echo htmlspecialchars($seo['meta_description'] ?? ''); ?></textarea>
                                </div>
                            </fieldset>

                            <button type="submit" class="btn btn-primary">Save Product</button>
                            <?php if ($action === 'edit'): ?>
                                <a href="manage_products.php" class="btn btn-secondary">Cancel Edit</a>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>