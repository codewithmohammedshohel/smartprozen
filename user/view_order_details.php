<?php
require_once __DIR__ . '/../includes/user_header.php';

if (!is_logged_in()) {
    header('Location: /smartprozen/auth/login.php');
    exit;
}

// Placeholder for order details logic
$order_id = $_GET['id'] ?? null;

?>
<div class="row">
    <?php require_once __DIR__ . '/../includes/user_sidebar.php'; ?>
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="card-title">Order Details</h1>
                <?php if ($order_id): ?>
                    <p>Details for Order ID: <?php echo htmlspecialchars($order_id); ?></p>
                    <!-- Further logic to fetch and display order details will go here -->
                <?php else: ?>
                    <p>No order ID specified.</p>
                <?php endif; ?>
                <a href="orders.php" class="btn btn-primary">Back to My Orders</a>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>