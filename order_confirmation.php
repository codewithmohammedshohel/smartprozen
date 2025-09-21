<?php
require_once __DIR__ . '/includes/header.php';

if (!is_logged_in() || !isset($_GET['id'])) {
    header('Location: /smartprozen/');
    exit;
}

$order_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: /smartprozen/user/orders.php');
    exit;
}

$page_title = "Order Confirmed";

?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h1 class="card-title h2"><?php echo __('thank_you_for_your_order'); ?></h1>
                    <p class="lead">
                        <?php echo __('hello'); ?> <?php echo htmlspecialchars($order['customer_name']); ?>, <?php echo __('your_order_is_confirmed'); ?>
                    </p>
                    <p>
                        <?php echo __('your_order_id_is'); ?> <strong class="text-primary">#<?php echo $order['id']; ?></strong>.
                    </p>
                    <div class="alert alert-info mt-4">
                        <?php if ($order['status'] === 'Pending'): ?>
                            <p class="mb-0"><?php echo __('order_manual_payment_notice'); ?></p>
                        <?php else: ?>
                            <p class="mb-0"><?php echo __('order_automatic_payment_notice'); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mt-4">
                        <a href="/smartprozen/user/orders.php" class="btn btn-primary">View My Orders</a>
                        <a href="/smartprozen/" class="btn btn-outline-secondary">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>