<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../includes/user_header.php';

if (!is_logged_in()) {
    header('Location: /smartprozen/auth/login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch all digital products the user has purchased
$downloads_query = "
    SELECT DISTINCT p.id as product_id, p.name as product_name_json, o.id as order_id
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = ? AND o.status = 'Completed' AND p.digital_file_path IS NOT NULL AND p.digital_file_path != ''
    ORDER BY o.created_at DESC
";
$stmt = $conn->prepare($downloads_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$downloads = $stmt->get_result();

?>
<div class="row">
    <?php require_once __DIR__ . '/../includes/user_sidebar.php'; ?>
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="card-title">Available Downloads</h1>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Order ID</th>
                                <th scope="col">Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($downloads->num_rows > 0): ?>
                                <?php while($download = $downloads->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo get_translated_text($download['product_name_json'], 'name'); ?></td>
                                    <td>#<?php echo $download['order_id']; ?></td>
                                    <td><a href="<?php echo SITE_URL; ?>/download.php?id=<?php echo $download['product_id']; ?>" class="btn btn-sm btn-success"><i class="bi bi-download me-2"></i>Download File</a></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">You have no digital products available for download yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>