<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_orders')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Initialize query parts
$where_clauses = [];
$order_by_clause = "ORDER BY o.created_at DESC"; // Default sort

// Handle search
$search_term = $_GET['search'] ?? '';
if (!empty($search_term)) {
    $search_term_escaped = $conn->real_escape_string($search_term);
    $where_clauses[] = "(o.id LIKE '%{$search_term_escaped}%' OR CONCAT(u.first_name, ' ', u.last_name) LIKE '%{$search_term_escaped}%' OR u.email LIKE '%{$search_term_escaped}%')";
}

// Handle status filter
$status_filter = $_GET['status_filter'] ?? '';
if (!empty($status_filter)) {
    $status_filter_escaped = $conn->real_escape_string($status_filter);
    $where_clauses[] = "o.status = '{$status_filter_escaped}'";
}

// Handle sort by
$sort_by = $_GET['sort_by'] ?? 'created_at';
$sort_order = $_GET['sort_order'] ?? 'DESC';

// Validate sort_by and sort_order to prevent SQL injection
$allowed_sort_by = ['created_at', 'total_amount'];
$allowed_sort_order = ['ASC', 'DESC'];

if (!in_array($sort_by, $allowed_sort_by)) {
    $sort_by = 'created_at';
}
if (!in_array($sort_order, $allowed_sort_order)) {
    $sort_order = 'DESC';
}

$order_by_clause = "ORDER BY o.{$sort_by} {$sort_order}";

// Construct the full query
$sql = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name, u.email as customer_email FROM orders o LEFT JOIN users u ON o.user_id = u.id";

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " " . $order_by_clause;

$orders = $conn->query($sql);
?>
<!-- Debugging GET parameters: <?php print_r($_GET); ?> -->
<!-- Debugging SQL query: <?php echo htmlspecialchars($sql); ?> -->
<?php
require_once '../includes/admin_header.php';
?>

<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>View Orders</h1>
        </div>

        <?php show_flash_messages(); ?>

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-filter-square me-1"></i>
                Filter & Sort Orders
            </div>
            <div class="card-body">
                <form action="view_orders.php" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Order ID, Customer Name/Email" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label">Status</label>
                        <select class="form-select" id="status_filter" name="status_filter">
                            <option value="">All Statuses</option>
                            <option value="Pending Payment" <?php echo (($_GET['status_filter'] ?? '') == 'Pending Payment') ? 'selected' : ''; ?>>Pending Payment</option>
                            <option value="Processing" <?php echo (($_GET['status_filter'] ?? '') == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                            <option value="Completed" <?php echo (($_GET['status_filter'] ?? '') == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="Cancelled" <?php echo (($_GET['status_filter'] ?? '') == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="Refunded" <?php echo (($_GET['status_filter'] ?? '') == 'Refunded') ? 'selected' : ''; ?>>Refunded</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="sort_by" class="form-label">Sort By</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="created_at" <?php echo (($_GET['sort_by'] ?? 'created_at') == 'created_at') ? 'selected' : ''; ?>>Date</option>
                            <option value="total_amount" <?php echo (($_GET['sort_by'] ?? '') == 'total_amount') ? 'selected' : ''; ?>>Total Amount</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sort_order" class="form-label">Order</label>
                        <select class="form-select" id="sort_order" name="sort_order">
                            <option value="DESC" <?php echo (($_GET['sort_order'] ?? 'DESC') == 'DESC') ? 'selected' : ''; ?>>Descending</option>
                            <option value="ASC" <?php echo (($_GET['sort_order'] ?? '') == 'ASC') ? 'selected' : ''; ?>>Ascending</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="view_orders.php" class="btn btn-secondary">Reset Filters</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-striped table-hover table-bordered">
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
                        <td>$<?php echo number_format($order['total_amount'] ?? 0, 2); ?></td>
                                                            <td><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></td>                        <td>
    <?php
    $status_class = '';
    switch ($order['status']) {
        case 'Pending Payment':
            $status_class = 'warning';
            break;
        case 'Processing':
            $status_class = 'info';
            break;
        case 'Completed':
            $status_class = 'success';
            break;
        case 'Cancelled':
            $status_class = 'danger';
            break;
        case 'Refunded':
            $status_class = 'secondary';
            break;
        default:
            $status_class = 'secondary';
            break;
    }
    ?>
    <span class="badge bg-<?php echo $status_class; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
</td>
                        <td>
                            <form action="update_order_status.php" method="POST" class="d-flex align-items-center">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <span class="me-2 text-muted small"><?php echo htmlspecialchars($order['status']); ?></span>
                                <select name="status" class="form-select form-select-sm me-2">
                                    <option value="Pending Payment" <?php if($order['status'] == 'Pending Payment') echo 'selected'; ?>>Pending Payment</option>
                                    <option value="Processing" <?php if($order['status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                                    <option value="Completed" <?php if($order['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                    <option value="Cancelled" <?php if($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                    <option value="Refunded" <?php if($order['status'] == 'Refunded') echo 'selected'; ?>>Refunded</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
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