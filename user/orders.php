<?php
require_once __DIR__ . '/../includes/user_header.php';

if (!is_logged_in()) {
    header('Location: /smartprozen/auth/login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$orders_query = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders_query->bind_param("i", $user_id);
$orders_query->execute();
$orders = $orders_query->get_result();

?>
<div class="row">
    <?php require_once __DIR__ . '/../includes/user_sidebar.php'; ?>
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="card-title">My Orders</h1>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Order ID</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Total</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders->num_rows > 0): ?>
                                <?php while($order = $orders->fetch_assoc()): ?>
                                <tr>
                                    <th scope="row">#<?php echo $order['id']; ?></th>
                                    <td><?php echo date("F j, Y", strtotime($order['created_at'])); ?></td>
                                    <td><span class="badge bg-<?php echo strtolower($order['status']) === 'completed' ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><a href="view_order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">You have no orders yet.</td>
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