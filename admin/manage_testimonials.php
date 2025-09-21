<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_testimonials')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Handle form submission (Add/Edit Testimonial)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['author_name'])) {
    $author_name = $_POST['author_name'] ?? '';
    $author_title = $_POST['author_title'] ?? '';
    $testimonial_text = $_POST['testimonial_text'] ?? '';
    $rating = $_POST['rating'] ?? null;
    $is_approved = isset($_POST['is_approved']) ? 1 : 0;
    $testimonial_id = $_POST['testimonial_id'] ?? null;

    if ($testimonial_id) {
        // Update existing testimonial
        $stmt = $conn->prepare("UPDATE testimonials SET author_name = ?, author_title = ?, testimonial_text = ?, rating = ?, is_approved = ? WHERE id = ?");
        $stmt->bind_param("sssiii", $author_name, $author_title, $testimonial_text, $rating, $is_approved, $testimonial_id);
    } else {
        // Add new testimonial
        $stmt = $conn->prepare("INSERT INTO testimonials (author_name, author_title, testimonial_text, rating, is_approved) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $author_name, $author_title, $testimonial_text, $rating, $is_approved);
    }

    if ($stmt->execute()) {
        log_activity('admin', $_SESSION['admin_id'], 'testimonial_save', "Saved testimonial: {$author_name}");
        $_SESSION['success_message'] = "Testimonial saved successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to save testimonial: " . $conn->error;
    }
    $stmt->close();
    header('Location: manage_testimonials.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $testimonial_id = $_GET['delete'] ?? null;
    if ($testimonial_id) {
        $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->bind_param("i", $testimonial_id);
        if ($stmt->execute()) {
            log_activity('admin', $_SESSION['admin_id'], 'testimonial_delete', "Deleted testimonial ID: {$testimonial_id}");
            $_SESSION['success_message'] = "Testimonial deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to delete testimonial: " . $conn->error;
        }
        $stmt->close();
    }
    header('Location: manage_testimonials.php');
    exit;
}

// Fetch all testimonials
$testimonials = [];
$testimonials_query = $conn->query("SELECT * FROM testimonials ORDER BY created_at DESC");
if ($testimonials_query) {
    while ($row = $testimonials_query->fetch_assoc()) {
        $testimonials[] = $row;
    }
}

$edit_testimonial = null;
if (isset($_GET['edit'])) {
    $testimonial_id = $_GET['edit'] ?? null;
    if ($testimonial_id) {
        $stmt = $conn->prepare("SELECT * FROM testimonials WHERE id = ?");
        $stmt->bind_param("i", $testimonial_id);
        $stmt->execute();
        $edit_testimonial = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Manage Testimonials</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Testimonials</li>
    </ol>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <i class="bi bi-chat-quote me-1"></i>
            <?php echo $edit_testimonial ? 'Edit Testimonial' : 'Add New Testimonial'; ?>
        </div>
        <div class="card-body">
            <form action="manage_testimonials.php" method="POST">
                <input type="hidden" name="testimonial_id" value="<?php echo $edit_testimonial['id'] ?? ''; ?>">
                
                <div class="mb-3">
                    <label for="author_name" class="form-label">Author Name</label>
                    <input type="text" id="author_name" name="author_name" class="form-control" value="<?php echo htmlspecialchars($edit_testimonial['author_name'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="author_title" class="form-label">Author Title (e.g., Happy Customer, CEO of XYZ Corp)</label>
                    <input type="text" id="author_title" name="author_title" class="form-control" value="<?php echo htmlspecialchars($edit_testimonial['author_title'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="testimonial_text" class="form-label">Testimonial Text</label>
                    <textarea id="testimonial_text" name="testimonial_text" class="form-control" rows="5" required><?php echo htmlspecialchars($edit_testimonial['testimonial_text'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating (1-5, optional)</label>
                    <input type="number" id="rating" name="rating" class="form-control" value="<?php echo htmlspecialchars($edit_testimonial['rating'] ?? ''); ?>" min="1" max="5">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_approved" name="is_approved" <?php echo (isset($edit_testimonial['is_approved']) && $edit_testimonial['is_approved'] == 1) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_approved">Approved</label>
                </div>
                
                <button type="submit" class="btn btn-primary"><?php echo $edit_testimonial ? 'Update Testimonial' : 'Add Testimonial'; ?></button>
                <?php if ($edit_testimonial): ?>
                    <a href="manage_testimonials.php" class="btn btn-secondary">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <i class="bi bi-list-ul me-1"></i>
            Existing Testimonials
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Author</th>
                            <th>Title</th>
                            <th>Testimonial</th>
                            <th>Rating</th>
                            <th>Approved</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($testimonials)): ?>
                            <?php foreach ($testimonials as $testimonial): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($testimonial['author_name']); ?></td>
                                    <td><?php echo htmlspecialchars($testimonial['author_title']); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars(substr($testimonial['testimonial_text'], 0, 100))) . (strlen($testimonial['testimonial_text']) > 100 ? '...' : ''); ?></td>
                                    <td><?php echo htmlspecialchars($testimonial['rating'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($testimonial['is_approved'] == 1): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date("F j, Y", strtotime($testimonial['created_at'])); ?></td>
                                    <td>
                                        <a href="manage_testimonials.php?edit=<?php echo $testimonial['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="manage_testimonials.php?delete=<?php echo $testimonial['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this testimonial?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No testimonials found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>