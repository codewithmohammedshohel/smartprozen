<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_orders')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

$search_query = "SELECT o.*, u.name as customer_name, u.email as customer_email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
$orders = $conn->query($search_query);

require_once '../includes/admin_header.php';
?>

<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>View Orders</h1>
        </div>

        <?php show_flash_messages(); ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?><br><small><?php echo htmlspecialchars($order['customer_email']); ?></small></td>
                        <td><?php echo date("M j, Y, g:i a", strtotime($order['created_at'])); ?></td>
                        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                                                            <?php
                                                            $gateway_name = 'N/A';
                                                            $gateway_id = $order['payment_gateway_id'];
                                                            if ($gateway_id) {
                                                                $stmt = $conn->prepare("SELECT name FROM payment_gateways WHERE id = ?");
                                                                $stmt->bind_param("i", $gateway_id);
                                                                $stmt->execute();
                                                                $result = $stmt->get_result();
                                                                if ($row = $result->fetch_assoc()) {
                                                                    $gateway_name = $row['name'];
                                                                }
                                                                $stmt->close();
                                                            }
                                                            ?>
                                                            <td><?php echo htmlspecialchars($gateway_name); ?></td>                        <td><span class="status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                        <td>
                            <form action="update_order_status.php" method="POST" class="status-form">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status">
                                    <option value="Pending Payment" <?php if($order['status'] == 'Pending Payment') echo 'selected'; ?>>Pending Payment</option>
                                    <option value="Processing" <?php if($order['status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                                    <option value="Completed" <?php if($order['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                    <option value="Cancelled" <?php if($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                    <option value="Refunded" <?php if($order['status'] == 'Refunded') echo 'selected'; ?>>Refunded</option>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>