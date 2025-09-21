<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_reviews')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Handle review actions (approve, reject, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $review_id = $_POST['review_id'] ?? null;
    $action = $_POST['action'] ?? '';

    if ($review_id) {
        switch ($action) {
            case 'approve':
                $stmt = $conn->prepare("UPDATE reviews SET is_approved = 1 WHERE id = ?");
                $stmt->bind_param("i", $review_id);
                $stmt->execute();
                log_activity('admin', $_SESSION['admin_id'], 'review_approve', "Approved review ID: {$review_id}");
                $_SESSION['success_message'] = "Review approved successfully.";
                break;
            case 'reject':
                $stmt = $conn->prepare("UPDATE reviews SET is_approved = 2 WHERE id = ?"); // 2 for rejected
                $stmt->bind_param("i", $review_id);
                $stmt->execute();
                log_activity('admin', $_SESSION['admin_id'], 'review_reject', "Rejected review ID: {$review_id}");
                $_SESSION['success_message'] = "Review rejected successfully.";
                break;
            case 'delete':
                $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
                $stmt->bind_param("i", $review_id);
                $stmt->execute();
                log_activity('admin', $_SESSION['admin_id'], 'review_delete', "Deleted review ID: {$review_id}");
                $_SESSION['success_message'] = "Review deleted successfully.";
                break;
        }
        $stmt->close();
    }
    header('Location: manage_reviews.php');
    exit;
}

// Fetch all reviews
$reviews = [];
$reviews_query = $conn->query("SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, p.name as product_name FROM reviews r LEFT JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC");
if ($reviews_query) {
    while ($row = $reviews_query->fetch_assoc()) {
        $reviews[] = $row;
    }
}

require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Manage Reviews</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Reviews</li>
    </ol>
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <i class="bi bi-chat-dots me-1"></i>
            Product Reviews
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(get_translated_text($review['product_name'], 'name')); ?></td>
                                    <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                                    <td>
                                        <div class="text-warning">
                                            <?php for ($i = 0; $i < $review['rating']; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                                            <?php for ($i = $review['rating']; $i < 5; $i++): ?><i class="bi bi-star"></i><?php endfor; ?>
                                        </div>
                                    </td>
                                    <td><?php echo nl2br(htmlspecialchars($review['comment'])); ?></td>
                                    <td>
                                        <?php
                                        if ($review['is_approved'] == 1) {
                                            echo '<span class="badge bg-success">Approved</span>';
                                        } elseif ($review['is_approved'] == 2) {
                                            echo '<span class="badge bg-danger">Rejected</span>';
                                        } else {
                                            echo '<span class="badge bg-warning">Pending</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo date("F j, Y", strtotime($review['created_at'])); ?></td>
                                    <td>
                                        <form action="manage_reviews.php" method="POST" class="d-inline-block">
                                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                            <?php if ($review['is_approved'] != 1): ?>
                                                <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                            <?php endif; ?>
                                            <?php if ($review['is_approved'] != 2): ?>
                                                <button type="submit" name="action" value="reject" class="btn btn-sm btn-warning">Reject</button>
                                            <?php endif; ?>
                                            <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this review?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No reviews found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>